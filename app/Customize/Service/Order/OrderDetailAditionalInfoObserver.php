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

namespace Customize\Service\Order;

use Customize\Controller\Trait\KrakiFileTrait;
use Customize\Entity\OrderDetailAdditionalInfo;
use Customize\Service\Cache\UserDataCache;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Eccube\Entity\Order;
use Eccube\Service\OrderHelper;

class OrderDetailAditionalInfoObserver
{
    use KrakiFileTrait;

    private $securePackProductClass;
    private $entityManagerInterface;

    public function __construct(
        EntityManagerInterface $entityManagerInterface,
        UserDataCache $userDataCache,
        OrderHelper $orderHelper,
    ) {
        $this->entityManagerInterface = $entityManagerInterface;
        $this->securePackProductClass = $userDataCache->getSecurePackProductClass();
        $this->orderHelper = $orderHelper;
    }

    public function postUpdate(OrderDetailAdditionalInfo $orderDetailAdditionalInfo, LifecycleEventArgs $event): void
    {
        $order = $this->entityManagerInterface->getRepository(Order::class)->findOneBy(['id' => $orderDetailAdditionalInfo->getOrderId()]);
        if ($order) {
            $this->orderHelper->processUpdateProductOrderStatus($order);
        }
    }

    public function postPersist(OrderDetailAdditionalInfo $orderDetailAdditionalInfo, LifecycleEventArgs $event)
    {
        $order = $this->entityManagerInterface->getRepository(Order::class)->findOneBy(['id' => $orderDetailAdditionalInfo->getOrderId()]);
        if ($order) {
            $this->orderHelper->processUpdateProductOrderStatus($order);
        }
    }

    public function postRemove(OrderDetailAdditionalInfo $orderDetailAdditionalInfo, LifecycleEventArgs $event)
    {
        $this->orderHelper->processUpdateOrderStatus($orderDetailAdditionalInfo);
    }
}
