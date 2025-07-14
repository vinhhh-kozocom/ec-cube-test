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

use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

class OrderTypeValidator extends ItemValidator
{
    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemInterface $item, PurchaseContext $context)
    {
        // if item has order type different from cart's orderType
        if (
            $item->getOrderType()
            && $item->getOrderType() !== $this->getOrderTypeOfCart($item)
            && $item->getQuantity() != 0
        ) {
            $this->throwInvalidItemException('front.cart.orderType.different', $item->getProductClass());
        }
    }

    protected function handle(ItemInterface $item, PurchaseContext $context)
    {
        $item->setQuantity(0);
    }

    // first item's orderType is order Type of cart
    public function getOrderTypeOfCart($item)
    {
        $cart = $item->getCart();
        $orderType = '';
        foreach ($cart->getItems() as $cartItem) {
            if ($cartItem->getOrderType() && $cartItem->getQuantity() != 0) {
                $orderType = $cartItem->getOrderType();
                break;
            }
        }

        return $orderType;
    }
}
