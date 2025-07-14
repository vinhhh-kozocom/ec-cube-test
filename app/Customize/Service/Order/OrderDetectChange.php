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
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Eccube\Entity\Order;
use Eccube\Service\OrderHelper;

class OrderDetectChange
{
    use KrakiFileTrait;

    private $securePackProductClass;
    private $entityManagerInterface;

    private $orderHelper;

    public function __construct(
        OrderHelper $orderHelper,
    ) {
        $this->orderHelper = $orderHelper;
    }

    public function postUpdate(Order $order, LifecycleEventArgs $event): void
    {
        $urlArray = [];
        if (!empty($GLOBALS['request'])) {
            $urlArray = explode('/', $GLOBALS['request']->getPathInfo());
        }

        if (
            end($urlArray) != 'checkout'
            && end($urlArray) != 'redirect_to'
            && end($urlArray) != 'shopping'
            && end($urlArray) != 'confirm'
        ) {
            $newOrder = $event->getEntity();
            $this->orderHelper->processUpdateProductOrderStatus($newOrder);
        }
    }
}
