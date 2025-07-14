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

if (!class_exists('\Customize\Entity\UserPayment')) {
    /**
     * UserPayment
     *
     * @ORM\Entity
     *
     * @ORM\Table(name="dtb_user_payment")
     *
     * @ORM\HasLifecycleCallbacks()
     */
    class UserPayment extends \Eccube\Entity\AbstractEntity
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
         * @ORM\Column(name="customer_id", type="integer", nullable=true)
         */
        private $customer_id;

        /**
         * @ORM\Column(name="card_seq", type="smallint", nullable=true)
         */
        private $card_seq;

        /**
         * @ORM\Column(name="card_no", type="string", length=20, nullable=true)
         */
        private $card_no;

        /**
         * @ORM\Column(name="brand", type="string", length=20, nullable=true)
         */
        private $brand;

        /**
         * @ORM\Column(name="card_holder", type="string", nullable=true)
         */
        private $card_holder;

        /**
         * @ORM\Column(name="status", type="string", nullable=true)
         */
        private $status;

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

        public function getCustomerId()
        {
            return $this->customer_id;
        }

        public function setCustomerId($customerID)
        {
            $this->customer_id = $customerID;

            return $this;
        }

        public function getCardSeq()
        {
            return $this->card_seq;
        }

        public function setCardSeq($cardSeq)
        {
            $this->card_seq = $cardSeq;

            return $this;
        }

        public function getCardNo()
        {
            return $this->card_no;
        }

        public function setCardNo($cardNo)
        {
            $this->card_no = $cardNo;

            return $this;
        }

        public function getBrand()
        {
            return $this->brand;
        }

        public function setBrand($brand)
        {
            $this->brand = $brand;

            return $this;
        }

        public function getcardHolder()
        {
            return $this->card_holder;
        }

        public function setCardHolder($cardHolder)
        {
            $this->card_holder = $cardHolder;

            return $this;
        }

        public function getStatus()
        {
            return $this->status;
        }

        public function setStatus($status)
        {
            $this->status = $status;

            return $this;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return UserPayment
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
         * @return UserPayment
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
         * @return UserPayment
         */
        public function setDeleteDate($deleteDate)
        {
            $this->delete_date = $deleteDate;

            return $this;
        }
    }
}
