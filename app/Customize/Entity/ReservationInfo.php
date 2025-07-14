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

if (!class_exists('\Customize\Entity\ReservationInfo')) {
    /**
     * ReservationInfo
     *
     * @ORM\Table(name="dtb_reservation_info")
     *
     * @ORM\InheritanceType("SINGLE_TABLE")
     *
     * @ORM\HasLifecycleCallbacks()
     *
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     *
     * @ORM\Entity(repositoryClass="Customize\Repository\ReservationInfoRepository")
     */
    class ReservationInfo extends \Eccube\Entity\AbstractEntity
    {
        public const PEOPLE_QUANTITY_LIST = [
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
        ];

        public const BUDGET_LISTS = [
            '10,000円以下' => '10,000円以下',
            '10,001円～30,000円' => '10,001円～30,000円',
            '30,001円～50,000円' => '30,001円～50,000円',
            '50,001円～100,000円' => '50,001円～100,000円',
            '100,001円以上' => '100,001円以上',
        ];

        public const BODY_TYPE_LISTS = [
            '手が長い' => '手が長い',
            'ふくよか' => 'ふくよか',
        ];

        public const RESERVATION_MAIL_SUBJECT = '[着物レンタルあき] 下見来店予約申し込み受付（自動配信メール）';

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
         * @ORM\ManyToOne(targetEntity="Customize\Entity\TimeSlot", inversedBy="ReservationInfoes")
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
         * @var string
         *
         * @ORM\Column(name="customer_name_01", type="string", length=255, nullable=true)
         */
        protected $customer_name_01;

        /**
         * @var string
         *
         * @ORM\Column(name="customer_name_02", type="string", length=255, nullable=true)
         */
        protected $customer_name_02;

        /**
         * @var string
         *
         * @ORM\Column(name="customer_kana_01", type="string", length=255, nullable=true)
         */
        protected $customer_kana_01;

        /**
         * @var string
         *
         * @ORM\Column(name="customer_kana_02", type="string", length=255, nullable=true)
         */
        protected $customer_kana_02;

        /**
         * @var string|null
         *
         * @ORM\Column(name="tel", type="string", length=14, nullable=true)
         */
        protected $tel;

        /**
         * @var string|null
         *
         * @ORM\Column(name="email", type="string", length=255, nullable=true)
         */
        protected $email;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="visit_day", type="date", nullable=true)
         */
        protected $visit_day;

        /**
         * @var int
         *
         * @ORM\Column(name="use_people", type="integer",  nullable=true)
         */
        protected $use_people;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="use_day", type="date", nullable=true)
         */
        protected $use_day;

        /**
         * @var string|null
         *
         * @ORM\Column(name="purpose", type="string", length=255, nullable=true)
         */
        protected $purpose;

        /**
         * @var string|null
         *
         * @ORM\Column(name="height", type="string", length=255, nullable=true)
         */
        protected $height;

        /**
         * @var string|null
         *
         * @ORM\Column(name="item", type="string", length=255, nullable=true)
         */
        protected $item;

        /**
         * @var string
         *
         * @ORM\Column(name="budget", type="string", nullable=true)
         */
        protected $budget;

        /**
         * @var string|null
         *
         * @ORM\Column(name="characteristic", type="string", length=255, nullable=true)
         */
        protected $characteristic;

        /**
         * @var string|null
         *
         * @ORM\Column(name="note", type="string", length=255, nullable=true)
         */
        protected $note;

        /**
         * @var string|null
         *
         * @ORM\Column(name="body_type", type="string", length=255, nullable=true)
         */
        protected $body_type;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz",nullable=true)
         */
        private $create_date;

        /**
         * @var \DateTime
         *
         * @ORM\Column(name="update_date", type="datetimetz",nullable=true)
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

        public function getCustomerName01()
        {
            return $this->customer_name_01;
        }

        public function setCustomerName01($customerName01)
        {
            $this->customer_name_01 = $customerName01;
        }

        public function getCustomerName02()
        {
            return $this->customer_name_02;
        }

        public function setCustomerName02($customerName02)
        {
            $this->customer_name_02 = $customerName02;
        }

        public function getCustomerKana01()
        {
            return $this->customer_kana_01;
        }

        public function setCustomerKana01($customerKana01)
        {
            $this->customer_kana_01 = $customerKana01;
        }

        public function getCustomerKana02()
        {
            return $this->customer_kana_02;
        }

        public function setCustomerKana02($customerKana02)
        {
            $this->customer_kana_02 = $customerKana02;
        }

        public function getTel()
        {
            return $this->tel;
        }

        public function setTel($tel)
        {
            $this->tel = $tel;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function setEmail($email)
        {
            $this->email = $email;
        }

        public function getVisitDay()
        {
            return $this->visit_day;
        }

        public function setVisitDay($visitDay)
        {
            $this->visit_day = $visitDay;
        }

        public function getUsePeople()
        {
            return $this->use_people;
        }

        public function setUsePeople($usePeople)
        {
            $this->use_people = $usePeople;
        }

        public function getUseDay()
        {
            return $this->use_day;
        }

        public function setUseDay($useDay)
        {
            $this->use_day = $useDay;
        }

        public function getPurpose()
        {
            return $this->purpose;
        }

        public function setPurpose($purpose)
        {
            $this->purpose = $purpose;
        }

        public function getHeight()
        {
            return $this->height;
        }

        public function setHeight($height)
        {
            $this->height = $height;
        }

        public function getItem()
        {
            return $this->item;
        }

        public function setItem($item)
        {
            $this->item = $item;
        }

        public function getBudget()
        {
            return $this->budget;
        }

        public function setBudget($budget)
        {
            $this->budget = $budget;
        }

        public function getCharacteristic()
        {
            return $this->characteristic;
        }

        public function setCharacteristic($characteristic)
        {
            $this->characteristic = $characteristic;
        }

        public function getNote()
        {
            return $this->note;
        }

        public function setNote($note)
        {
            $this->note = $note;
        }

        public function getBodyType()
        {
            return $this->body_type;
        }

        public function setBodyType($bodyType)
        {
            $this->body_type = $bodyType;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return ReservationInfo
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
