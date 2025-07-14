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

if (!class_exists('\Customize\Entity\TimeSlot')) {
    /**
     * TimeSlot
     *
     * @ORM\Table(name="dtb_time_slot")
     *
     * @ORM\InheritanceType("SINGLE_TABLE")
     *
     * @ORM\HasLifecycleCallbacks()
     *
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     *
     * @ORM\Entity(repositoryClass="Customize\Repository\TimeSlotRepository")
     */
    class TimeSlot extends \Eccube\Entity\AbstractEntity
    {
        public const TIME_SLOT_OF_USER = 1;
        public const TIME_SLOT_OF_ADMIN = 2;
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
         * @var TimeSlotType
         *
         * @ORM\ManyToOne(targetEntity="Customize\Entity\TimeSlotType", inversedBy="TimeSlots")
         *
         * @ORM\JoinColumns({
         *
         *   @ORM\JoinColumn(name="type_slot_time_id", referencedColumnName="id")
         * })
         */
        protected $type_slot_time_id;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="start_time", type="time")
         */
        protected $start_time;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="end_time", type="time")
         */
        protected $end_time;

        /**
         * @var bool
         *
         * @ORM\Column(name="is_special_day", type="boolean", options={"default":false})
         */
        protected $is_special_day;

        /**
         * @var \Doctrine\Common\Collections\Collection|ReservationActual[]
         *
         * @ORM\OneToMany(targetEntity="Customize\Entity\ReservationActual", mappedBy="time_slot_id", cascade={"persist","remove"})
         */
        protected $ReservationActuals;

        /**
         * @var \Doctrine\Common\Collections\Collection|ReservationInfoes[]
         *
         * @ORM\OneToMany(targetEntity="Customize\Entity\ReservationInfo", mappedBy="time_slot_id", cascade={"persist","remove"})
         */
        protected $ReservationInfoes;

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

        public function getTypeSlotTimeId()
        {
            return $this->type_slot_time_id;
        }

        public function setTypeSlotTimeId($typeSlotTimeId)
        {
            $this->type_slot_time_id = $typeSlotTimeId;
        }

        public function getStartTime()
        {
            return $this->start_time;
        }

        public function setStartTime($startTime)
        {
            $this->start_time = $startTime;
        }

        public function getEndTime()
        {
            return $this->end_time;
        }

        public function setEndTime($endTime)
        {
            $this->end_time = $endTime;
        }

        public function getIsSpecialDay()
        {
            return $this->is_special_day;
        }

        public function setIsSpecialDay($isSpecialDay)
        {
            $this->is_special_day = $isSpecialDay;
        }

        /**
         * Get ReservationActuals.
         *
         * @return \Doctrine\Common\Collections\Collection|ReservationActual[]
         */
        public function getReservationActuals()
        {
            return $this->ReservationActuals;
        }

        /**
         * Get ReservationInfoes.
         *
         * @return \Doctrine\Common\Collections\Collection|ReservationActual[]
         */
        public function getReservationInfoes()
        {
            return $this->ReservationInfoes;
        }
    }
}
