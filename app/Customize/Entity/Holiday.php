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

if (!class_exists('\Customize\Entity\Holiday')) {
    /**
     * Holiday
     *
     * @ORM\Table(name="dtb_holiday")
     *
     * @ORM\InheritanceType("SINGLE_TABLE")
     *
     * @ORM\HasLifecycleCallbacks()
     *
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     *
     * @ORM\Entity(repositoryClass="Customize\Repository\HolidayRepository")
     */
    class Holiday extends \Eccube\Entity\AbstractEntity
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
         * @var \DateTime|null
         *
         * @ORM\Column(name="date", type="date", nullable=true)
         */
        protected $date;

        /**
         * @var string|null
         *
         * @ORM\Column(name="name", type="string", nullable=true)
         */
        protected $name;

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

        public function getDate()
        {
            return $this->date;
        }

        public function setDate($date)
        {
            $this->date = $date;
        }

        public function getName()
        {
            return $this->name;
        }

        public function setName($name)
        {
            $this->name = $name;
        }
    }
}
