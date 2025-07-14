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

if (!class_exists('\Customize\Entity\ReservationSetting')) {
    /**
     * ReservationSetting
     *
     * @ORM\Table(name="dtb_reservation_setting")
     *
     * @ORM\InheritanceType("SINGLE_TABLE")
     *
     * @ORM\HasLifecycleCallbacks()
     *
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     *
     * @ORM\Entity(repositoryClass="Customize\Repository\ReservationSettingRepository")
     */
    class ReservationSetting extends \Eccube\Entity\AbstractEntity
    {
        public const RESERVATION_TRIANGLE_ID = 1;
        public const RESERVATION_FULL_SLOT_ID = 2;

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
         * @ORM\Column(name="type", type="string", length=255, nullable=true)
         */
        protected $type;

        /**
         * @var int
         *
         * @ORM\Column(name="value", type="integer", nullable=true)
         */
        protected $value;

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
         * Set type
         *
         * @param string $type
         *
         * @return $this
         */
        public function setType($type)
        {
            $this->type = $type;

            return $this;
        }

        /**
         * Get type
         *
         * @return string
         */
        public function getType()
        {
            return $this->type;
        }

        /**
         * Set value.
         *
         * @param int $value
         *
         * @return $this
         */
        public function setValue($value)
        {
            $this->value = $value;

            return $this;
        }

        /**
         * Get value.
         *
         * @return int
         */
        public function getValue()
        {
            return $this->value;
        }
    }
}
