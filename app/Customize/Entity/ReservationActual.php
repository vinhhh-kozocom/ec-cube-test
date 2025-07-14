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

if (!class_exists('\Customize\Entity\ReservationActual')) {
    /**
     * ReservationActual
     *
     * @ORM\Table(name="dtb_reservation_actual")
     *
     * @ORM\InheritanceType("SINGLE_TABLE")
     *
     * @ORM\HasLifecycleCallbacks()
     *
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     *
     * @ORM\Entity(repositoryClass="Customize\Repository\ReservationActualRepository")
     */
    class ReservationActual extends \Eccube\Entity\AbstractEntity
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
        protected $id;

        /**
         * @var TimeSlot
         *
         * @ORM\ManyToOne(targetEntity="Customize\Entity\TimeSlot", inversedBy="ReservationActuals")
         *
         * @ORM\JoinColumns({
         *
         *   @ORM\JoinColumn(name="time_slot_id", referencedColumnName="id")
         * })
         */
        protected $time_slot_id;

        /**
         * @var string
         *
         * @ORM\Column(name="shop", type="string", length=255, nullable=true)
         */
        protected $shop;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="visit_day", type="date", nullable=true)
         */
        protected $visit_day;

        /**
         * @var int
         *
         * @ORM\Column(name="quantity", type="integer", nullable=true)
         */
        protected $quantity;

        /**
         * @var int
         *
         * @ORM\Column(name="amount_customer_reservation", type="integer", nullable=true)
         */
        protected $amount_customer_reservation;

        /**
         * @var int
         *
         * @ORM\Column(name="amount_order_type_visit", type="integer", nullable=true)
         */
        protected $amount_order_type_visit;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz", nullable=true)
         */
        private $create_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="update_date", type="datetimetz", nullable=true)
         */
        private $update_date;

        /**
         * Set id.
         *
         * @param int $id
         *
         * @return $this
         */
        public function setId($id)
        {
            $this->id = $id;

            return $this;
        }

        /**
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        public function getTimeSlotId()
        {
            return $this->time_slot_id;
        }

        public function setTimeSlotId($timeSlotId)
        {
            $this->time_slot_id = $timeSlotId;
        }

        public function getShop()
        {
            return $this->shop;
        }

        public function setShop($shop)
        {
            $this->shop = $shop;
        }

        public function getVisitDay()
        {
            return $this->visit_day;
        }

        public function setVisitDay($visitDay)
        {
            $this->visit_day = $visitDay;
        }

        public function getQuantity()
        {
            return $this->quantity;
        }

        public function setQuantity($quantity)
        {
            $this->quantity = $quantity;
        }

        public function getAmountCustomerReservation()
        {
            return $this->amount_customer_reservation;
        }

        public function setAmountCustomerReservation($amountReservation)
        {
            $this->amount_customer_reservation = $amountReservation;
        }

        public function getAmountOrdertypeVisit()
        {
            return $this->amount_customer_reservation;
        }

        public function setAmountOrdertypeVisit($amountOrdertypeVisit)
        {
            $this->amount_order_type_visit = $amountOrdertypeVisit;
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
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return ReservationActual
         */
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Set updateDate.
         *
         * @param \DateTime $updateDate
         *
         * @return ReservationInfo
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
    }
}
