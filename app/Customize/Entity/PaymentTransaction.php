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

namespace Customize\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Customize\Entity\PaymentTransaction')) {
    /**
     * PaymentTransaction
     *
     * @ORM\Entity
     *
     * @ORM\Table(name="dtb_payment_transaction")
     *
     * @ORM\HasLifecycleCallbacks()
     */
    class PaymentTransaction extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         *
         * @ORM\Id
         *
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var int
         *
         * @ORM\Column(name="order_id", type="integer")
         */
        private $order_id;

        /**
         * @var string
         *
         * @ORM\Column(name="forward", type="string", nullable=true)
         */
        private $forward;

        /**
         * @var int
         *
         * @ORM\Column(name="method", type="smallint", nullable=true)
         */
        private $method;

        /**
         * @var int
         *
         * @ORM\Column(name="paytimes", type="integer", nullable=true)
         */
        private $paytimes;

        /**
         * @var string
         *
         * @ORM\Column(name="approve", type="string", nullable=true)
         */
        private $approve;

        /**
         * @var string
         *
         * @ORM\Column(name="transaction_id", type="string", nullable=true)
         */
        private $transaction_id;

        /**
         * @var string
         *
         * @ORM\Column(name="tran_date", type="string", nullable=true)
         */
        private $tran_date;

        /**
         * @var string
         *
         * @ORM\Column(name="check_string", type="string", nullable=true)
         */
        private $check_string;

        /**
         * @var string
         *
         * @ORM\Column(name="client_field1", type="string", nullable=true)
         */
        private $client_field1;

        /**
         * @var string
         *
         * @ORM\Column(name="client_field2", type="string", nullable=true)
         */
        private $client_field2;

        /**
         * @var string
         *
         * @ORM\Column(name="client_field3", type="string", nullable=true)
         */
        private $client_field3;

        /**
         * @var string
         *
         * @ORM\Column(name="err_code", type="string", nullable=true)
         */
        private $err_code;

        /**
         * @var string
         *
         * @ORM\Column(name="err_info", type="string", nullable=true)
         */
        private $err_info;

        /**
         * @var string
         *
         * @ORM\Column(name="err_message", type="string", nullable=true)
         */
        private $err_message;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="update_date", type="datetimetz")
         */
        private $update_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="delete_date", type="datetimetz", nullable=true)
         */
        private $delete_date;

        public function getId()
        {
            return $this->id;
        }

        public function getOrderId()
        {
            return $this->order_id;
        }

        public function setOrderId($orderID)
        {
            $this->order_id = $orderID;

            return $this;
        }

        public function getForward()
        {
            return $this->forward;
        }

        public function setForward($forward)
        {
            $this->forward = $forward;

            return $this;
        }

        public function getMethod()
        {
            return $this->method;
        }

        public function setMethod($method)
        {
            $this->method = $method;

            return $this;
        }

        public function getPaytimes()
        {
            return $this->paytimes;
        }

        public function setPaytimes($paytimes)
        {
            $this->paytimes = $paytimes;

            return $this;
        }

        public function getApprove()
        {
            return $this->approve;
        }

        public function setApprove($approve)
        {
            $this->approve = $approve;

            return $this;
        }

        public function getTransactionID()
        {
            return $this->transaction_id;
        }

        public function setTransactionID($transactionID)
        {
            $this->transaction_id = $transactionID;

            return $this;
        }

        public function getTransactionDate()
        {
            return $this->tran_date;
        }

        public function setTranDate($tranDate)
        {
            $this->tran_date = $tranDate;

            return $this;
        }

        public function getCheckstring()
        {
            return $this->check_string;
        }

        public function setCheckstring($checkString)
        {
            $this->check_string = $checkString;

            return $this;
        }

        public function getClientField01()
        {
            return $this->client_field1;
        }

        public function setClientField01($clientField01)
        {
            $this->client_field1 = $clientField01;

            return $this;
        }

        public function getClientField02()
        {
            return $this->client_field2;
        }

        public function setClientField02($clientField02)
        {
            $this->client_field2 = $clientField02;

            return $this;
        }

        public function getClientField03()
        {
            return $this->client_field3;
        }

        public function setClientField03($clientField03)
        {
            $this->client_field3 = $clientField03;

            return $this;
        }

        public function getErrCode()
        {
            return $this->err_code;
        }

        public function setErrCode($errCode)
        {
            $this->err_code = $errCode;

            return $this;
        }

        public function getErrInfo()
        {
            return $this->err_info;
        }

        public function setErrInfo($errInfo)
        {
            $this->err_info = $errInfo;

            return $this;
        }

        public function getErrMessage()
        {
            return $this->err_message;
        }

        public function setErrMessage($errMessage)
        {
            $this->err_message = $errMessage;

            return $this;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return PaymentTransaction
         */
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return PaymentTransaction
         */
        public function setUpdateDate($updateDate)
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
        }

        /**
         * Get deleteDate.
         *
         * @return \DateTime
         */
        public function getDeleteDate()
        {
            return $this->delete_date;
        }

        /**
         * Delete updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return PaymentTransaction
         */
        public function setDeleteDate($deleteDate)
        {
            $this->delete_date = $deleteDate;

            return $this;
        }
    }
}
