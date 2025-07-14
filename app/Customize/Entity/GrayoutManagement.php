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

if (!class_exists('\Customize\Entity\GrayoutManagement')) {
    /**
     * GrayoutManagement
     *
     * @ORM\Entity
     *
     * @ORM\Table(name="dtb_gray_out_management")
     *
     * @ORM\HasLifecycleCallbacks()
     */
    class GrayoutManagement extends \Eccube\Entity\AbstractEntity
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
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product", inversedBy="grayout_management")
         *
         * @ORM\JoinColumns({
         *
         *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
         * })
         */
        private $product_id;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="start_date", type="date", nullable=true)
         */
        protected $start_date;

        /**
         * @var \DateTime|null
         *
         * @ORM\Column(name="end_date", type="date", nullable=true)
         */
        protected $end_date;

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

        public function getProductId()
        {
            return $this->product_id;
        }

        public function setProductId($product_id)
        {
            $this->product_id = $product_id;

            return $this;
        }

        public function getStartDate()
        {
            return $this->start_date;
        }

        public function setStartDate($start_date)
        {
            $this->start_date = $start_date;

            return $this;
        }

        public function getEndDate()
        {
            return $this->end_date;
        }

        public function setEndDate($end_date)
        {
            $this->end_date = $end_date;

            return $this;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return GrayoutManagement
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
         * @return GrayoutManagement
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
         * @return GrayoutManagement
         */
        public function setDeleteDate($deleteDate)
        {
            $this->delete_date = $deleteDate;

            return $this;
        }
    }
}
