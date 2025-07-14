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

if (!class_exists('\Customize\Entity\TimeSlotType')) {
    /**
     * TimeSlotType
     *
     * @ORM\Table(name="dtb_time_slot_type")
     *
     * @ORM\InheritanceType("SINGLE_TABLE")
     *
     * @ORM\HasLifecycleCallbacks()
     *
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     *
     * @ORM\Entity(repositoryClass="Customize\Repository\TimeSlotTypeRepository")
     */
    class TimeSlotType extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @return string
         */
        public function __toString()
        {
            return (string) $this->getName();
        }

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
         * @var string
         *
         * @ORM\Column(name="name", type="string", length=255)
         */
        protected $name;

        /**
         * @var \Doctrine\Common\Collections\Collection|TimeSlot[]
         *
         * @ORM\OneToMany(targetEntity="Customize\Entity\TimeSlot", mappedBy="type_slot_time_id", cascade={"persist","remove"})
         */
        protected $TimeSlots;

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

        /**
         * Set name.
         *
         * @param string $name
         *
         * @return $this
         */
        public function setName($name)
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         *
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * Get timeSlot.
         *
         * @return \Doctrine\Common\Collections\Collection|TimeSlot[]
         */
        public function getTimeSlots()
        {
            return $this->TimeSlots;
        }
    }
}
