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

use Customize\Controller\Trait\ConstanceTrait;
use Customize\Controller\Trait\KrakiFileTrait;
use Customize\Service\Cache\UserDataCache;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\ItemInterface;
use Eccube\Service\OrderHelper;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class AdminOrderLocaleValidator extends ItemValidator
{
    use KrakiFileTrait;
    use ConstanceTrait;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $secureProductClass;

    private $requestStack;

    private $orderHelper;

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
        OrderHelper $orderHelper,
    ) {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->orderHelper = $orderHelper;
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
        if (
            !$item->getProductClass()
            || $item->getProductClass()->getId() == $this->secureProductClass->getId()
        ) {
            return;
        }

        $canAdd = $this->canAddToOrder($item);

        if (!$canAdd) {
            $this->throwInvalidItemException('front.product.locale.error', $item->getProductClass());
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
    }

    private function canAddToOrder($item)
    {
        $localeOfOrder = $this->orderHelper->getLocaleOfOrder($item->getOrder());
        $localeOfItem = $this->orderHelper->getLocaleOfItem($item);

        if ($localeOfOrder == $localeOfItem) {
            return true;
        }

        return false;
    }
}
