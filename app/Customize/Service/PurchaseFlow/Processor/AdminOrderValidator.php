<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Customize\Service\PurchaseFlow\Processor;

use Customize\Common\Constants;
use Customize\Controller\Trait\ConstanceTrait;
use Customize\Controller\Trait\KrakiFileTrait;
use Customize\Entity\OrderDetailAdditionalInfo;
use Customize\Entity\ProductUseDays;
use Customize\Service\Cache\UserDataCache;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class AdminOrderValidator extends ItemValidator
{
    use KrakiFileTrait;
    use ConstanceTrait;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $secureProductClass;

    private $requestStack;

    /**
     * EmptyItemsProcessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        KernelInterface $kernelInterface,
        EccubeConfig $eccubeConfig,
        RequestStack $requestStack,
        UserDataCache $userDataCache,
    ) {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->setUpValue();
        // get settings variable, to get data from file
        $this->getSettingFile($eccubeConfig);
        $this->secureProductClass = $userDataCache->getSecurePackProductClass();
    }

    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        // if item is secure_pack, do not done anything
        if (
            !$item->getProductClass()
            || $item->getProductClass()->getId() == $this->secureProductClass->getId()
        ) {
            return;
        }
        // if order is foreign, not validate duplicate
        if ($item->getOrder()->getLocale() != Constants::LOCALE_JAPAN) {
            return;
        }

        $wearDate = $this->getWearDate($item);
        $isNewWearDate = $this->isNewWearDate($wearDate, $item);
        if (!$isNewWearDate) {
            return;
        }

        $notAvailableDate = $this->getNotAvailableDays($item);
        $result = array_intersect($wearDate, $notAvailableDate);

        if (empty($result)) {
            return;
        } else {
            $this->throwInvalidItemException('front.product.duplicate.use_date', $item->getProductClass());
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
    }

    private function getWearDate($item)
    {
        if ($item instanceof OrderItem) {
            // if variant from postForm is empty. It is the bug
            $variant = $this->findVariantFromPostForm($item->getProductClass());
            if (empty($variant)) {
                return [];
            }

            $wearDate = $variant['wear_date'];
        } else {
            return [];
        }

        return [$wearDate];
    }

    // Find the dates that the product was rented
    private function getNotAvailableDays($orderItem)
    {
        $productClass = $orderItem->getProductClass();
        $request = $this->requestStack->getCurrentRequest();
        $arrayUrl = explode('/', $request->getPathInfo());
        $typeRequest = end($arrayUrl);

        $useDays = $this->entityManager
            ->getRepository(ProductUseDays::class)->findOneBy([
                'product_id' => $productClass->getProduct()->getId(),
            ]);
        $beforeUseDays = $useDays ? $useDays->getBeforeUseDays() : $this->DEFAULT_BEFORE_USE_DAYS;
        $afterUseDays = $useDays ? $useDays->getAfterUseDays() : $this->DEFAULT_AFTER_USE_DAYS;

        $today = date('Y-m-d', strtotime('-10 days'));
        $query = $this->entityManager->createQueryBuilder()
            ->select('odai')
            ->from(OrderDetailAdditionalInfo::class, 'odai')
            ->innerJoin(Order::class, 'odr')
            ->where('odai.product_class_id = :product_class_id and odai.wear_date >= :wear_date')
            ->andWhere('odai.order_id = odr.id')
            ->andWhere('odr.OrderStatus <> 3')
            ->setParameter('product_class_id', $productClass->getId())
            ->setParameter('wear_date', $today)
            ->getQuery();

        $orderDetailAdditionalInfoList = $query->getResult();
        $not_available_dates = [];

        foreach ($orderDetailAdditionalInfoList as $orderDetailAdditionalInfo) {
            // Avoid validate with itself when edit
            if (
                $typeRequest === 'edit'
                && $orderDetailAdditionalInfo->getOrderId() === $orderItem->getOrderId()
            ) {
                continue;
            }
            if (!empty($orderDetailAdditionalInfo->getBeforeUseDays())) {
                $beforeUseDays = $orderDetailAdditionalInfo->getBeforeUseDays();
            }
            if (!empty($orderDetailAdditionalInfo->getAfterUseDays())) {
                $afterUseDays = $orderDetailAdditionalInfo->getAfterUseDays();
            }

            $wearDate = $orderDetailAdditionalInfo->getWearDate()->format('Y-m-d');
            $not_available_dates[] = $wearDate;
            for ($i = $beforeUseDays; $i > 0; $i--) {
                $tmpDate = strtotime($wearDate." - {$i} day");
                $not_available_dates[] = date('Y-m-d', $tmpDate);
            }
            for ($i = 1; $i <= $afterUseDays; $i++) {
                $tmpDate = strtotime($wearDate." + {$i} day");
                $not_available_dates[] = date('Y-m-d', $tmpDate);
            }
        }

        $not_available_dates = array_unique($not_available_dates);
        sort($not_available_dates);

        return $not_available_dates;
    }

    // in admin Order site. variant come from form input
    private function findVariantFromPostForm($productClass)
    {
        $result = [];
        $postForm = $_POST['OrderDetailAdditionalInfo'] ?? $result;
        // post form maybe have list of variant
        foreach ($postForm as $item) {
            if ($item['product_class_id'] == $productClass->getId()) {
                $result = $item;
                break;
            }
        }

        return $result;
    }

    // avoid admin update product without change weardate, but not pass
    public function isNewWearDate($wearDate, $item)
    {
        $additionalInfo = $this->entityManager->getRepository(OrderDetailAdditionalInfo::class)
            ->findOneBy(['order_detail_id' => $item->getId()]);
        if (!$additionalInfo || empty($wearDate)) {
            return true;
        }

        return $additionalInfo->getWearDate()->format('Y-m-d') == $wearDate[0] ? false : true;
    }
}
