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

use Customize\Controller\Trait\KrakiFileTrait;
use Customize\Service\Cache\UserDataCache;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Cart;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Order;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\InvalidItemException;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Symfony\Component\HttpKernel\KernelInterface;

class SecurePackProcessor extends ItemHolderValidator
{
    use KrakiFileTrait;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    private $secureProductClass;

    private $cartService;

    /**
     * EmptyItemsProcessor constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        KernelInterface $kernelInterface,
        EccubeConfig $eccubeConfig,
        CartService $cartService,
        UserDataCache $userDataCache,
    ) {
        $this->entityManager = $entityManager;
        $this->cartService = $cartService;

        // get settings variable, to get data from file
        $this->getSettingFile($eccubeConfig);
        $this->secureProductClass = $userDataCache->getSecurePackProductClass();
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws InvalidItemException
     */
    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // if $itemHolder is type Of Cart
        if ($itemHolder instanceof Cart) {
            // count each cart have how many item use secure pack ?
            $count = $this->countSecurePackItems($itemHolder->getItems());
            foreach ($itemHolder->getItems() as $item) {
                if ($item->getProductClass()->getId() == $this->secureProductClass->getId()) {
                    if ($count == 0) {
                        $itemHolder->removeItem($item);
                    } else {
                        $item->setQuantity($count);
                    }
                    break;
                }
            }
        }
        // if $itemHolder is type of Order
        if ($itemHolder instanceof Order) {
            $count = $this->countSecurePackOrderItem($itemHolder);

            foreach ($itemHolder->getItems() as $item) {
                if (
                    $item->getProductClass()
                    && $item->getProductClass()->getId() == $this->secureProductClass->getId()
                ) {
                    if ($count == 0) {
                        $itemHolder->removeOrderItem($item);
                    } else {
                        $item->setQuantity($count);
                    }
                    break;
                }
            }
        }
    }

    // count how many secure_pack do one order have ?
    private function countSecurePackOrderItem($itemHolder)
    {
        $count = 0;
        $lisOrdertItem = $itemHolder->getMergedProductOrderItems();
        $cart = $this->cartService->getCart();
        $listCartItem = $cart->getItems();

        // matching orderItem with cartItem
        foreach ($lisOrdertItem as $orderItem) {
            if ($orderItem->getProductCode() != $this->secureProductClass->getCode()) {
                foreach ($listCartItem as $cartItem) {
                    if (
                        $orderItem->getProductClass()->getId() == $cartItem->getProductClass()->getId()
                        && $cartItem->getSecurePack()
                        && $orderItem->getQuantity()
                    ) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    // count how many Cart item use secure pack
    private function countSecurePackItems($cartItems)
    {
        $count = 0;
        foreach ($cartItems as $item) {
            if ($item->getSecurePack() && $item->getQuantity()) {
                $count++;
            }
        }

        return $count;
    }
}
