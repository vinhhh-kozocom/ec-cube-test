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
use Customize\Controller\Trait\KrakiFileTrait;
use Customize\Entity\OrderDetailAdditionalInfo;
use Customize\Service\Cache\UserDataCache;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class OrderValidator extends ItemValidator
{
    use KrakiFileTrait;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $cartService;

    private $secureProductClass;
    private $requestStack;

    /**
     * EmptyItemsProcessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CartService $cartService,
        KernelInterface $kernelInterface,
        EccubeConfig $eccubeConfig,
        UserDataCache $userDataCache,
        RequestStack $requestStack,
    ) {
        $this->entityManager = $entityManager;
        $this->cartService = $cartService;
        $this->requestStack = $requestStack;

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
        // if product is foreign, not validate
        $request = $this->requestStack->getCurrentRequest();
        if ($request->getLocale() != Constants::LOCALE_JAPAN) {
            return;
        }

        $wearDate = $this->getWearDate($item);
        $notAvailableDate = $this->getNotAvailableDays($item->getProductClass());
        $result = array_intersect($wearDate, $notAvailableDate);

        if (empty($result)) {
            return;
        } else {
            $this->throwInvalidItemException('front.product.duplicate.use_date', $item->getProductClass());
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $item->setQuantity(0);
    }

    private function getWearDate($item)
    {
        if ($item instanceof CartItem) {
            $wearDate = $item->getWearDate()->format('Y-m-d');
        }

        if ($item instanceof OrderItem) {
            $variant = $this->findVariantFromCart($item->getProductClass());
            if (empty($variant)) {
                return [];
            }
            $wearDate = $variant->getWearDate()->format('Y-m-d');
        }

        return [$wearDate];
    }

    // Find the dates that the product was rented
    private function getNotAvailableDays($productClass)
    {
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

    // order item have no variant => base on cart item to find it
    private function findVariantFromCart($productClass)
    {
        $cart = $this->cartService->getCart();
        if (empty($cart)) {
            return [];
        }
        foreach ($cart->getCartItems() as $item) {
            if ($item->getProductClass()->getId() == $productClass->getId()) {
                return $item;
            }
        }
    }
}
