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

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\Delivery;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Eccube\Repository\DeliveryRepository;
use Eccube\Service\PurchaseFlow\ItemHolderValidator;
use Eccube\Service\PurchaseFlow\PurchaseContext;

/**
 * 支払い方法が一致しない明細がないかどうか.
 */
class PaymentValidator extends ItemHolderValidator
{
    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * PaymentProcessor constructor.
     *
     * @param DeliveryRepository $deliveryRepository
     */
    public function __construct(DeliveryRepository $deliveryRepository)
    {
        $this->deliveryRepository = $deliveryRepository;
    }

    protected function validate(ItemHolderInterface $itemHolder, PurchaseContext $context)
    {
        // 明細の個数が1以下の場合はOK
        if (count($itemHolder->getItems()) <= 1) {
            return;
        }

        // a, ab, c
        $i = 0;
        $paymentIds = [];
        foreach ($itemHolder->getItems() as $item) {
            if (false === $item->isProduct()) {
                continue;
            }
            $Deliveries = $this->getDeliveries($item->getProductClass()->getSaleType());
            $Payments = $this->getPayments($Deliveries);

            $ids = [];
            foreach ($Payments as $Payment) {
                $ids[] = $Payment->getId();
            }
            if ($i === 0) {
                $paymentIds = $ids;
                ++$i;
                continue;
            }

            $paymentIds = array_intersect($paymentIds, $ids);
        }

        // If the order is in error. The system will assign the new message to the top (stack)
        // and redirect to the shopping/error page
        // there no message here. Because get message from warning
        if (empty($paymentIds)) {
            $this->throwInvalidItemException('');
        }

        if ($itemHolder instanceof Order) {
            if (null === $itemHolder->getPayment()) {
                return;
            }

            // 支払い方法が非表示の場合はエラー
            if (false === $itemHolder->getPayment()->isVisible()) {
                $this->throwInvalidItemException('front.shopping.not_available_payment_method');
            }
        }
    }

    private function getDeliveries(SaleType $SaleType)
    {
        $Deliveries = $this->deliveryRepository->findBy(
            [
                'SaleType' => $SaleType,
                'visible' => true,
            ]
        );

        return $Deliveries;
    }

    /**
     * @param Delivery[] $Deliveries
     *
     * @return ArrayCollection|Payment[]
     */
    private function getPayments($Deliveries)
    {
        $Payments = new ArrayCollection();
        foreach ($Deliveries as $Delivery) {
            $PaymentOptions = $Delivery->getPaymentOptions();
            foreach ($PaymentOptions as $PaymentOption) {
                $Payments->add($PaymentOption->getPayment());
            }
        }

        return $Payments;
    }
}
