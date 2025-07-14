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

if (!class_exists('\Customize\Entity\ProductUseDays')) {
    /**
     * ProductUseDays
     *
     * @ORM\Entity
     *
     * @ORM\Table(name="plg_product_use_days")
     *
     * @ORM\HasLifecycleCallbacks()
     */
    class ProductUseDays extends \Eccube\Entity\AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="product_use_days_id", type="integer", options={"unsigned":true})
         *
         * @ORM\Id
         *
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var int
         *
         * @ORM\Column(name="product_id", type="integer", options={"unsigned":true})
         */
        private $product_id;

        /**
         * @var int
         *
         * @ORM\Column(name="before_use_days", type="integer", options={"unsigned":true})
         */
        private $before_use_days;

        /**
         * @var int
         *
         * @ORM\Column(name="after_use_days", type="integer", options={"unsigned":true})
         */
        private $after_use_days;

        /**
         * Method getId
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Method getProduct
         *
         * @return int
         */
        public function getProductId()
        {
            return $this->product_id;
        }

        /**
         * Method setProduct
         *
         * @param int $product_id
         *
         * @return ProductUseDays
         */
        public function setProductId(int $product_id)
        {
            $this->product_id = $product_id;

            return $this;
        }

        /**
         * Method getBeforeUseDays
         *
         * @return int
         */
        public function getBeforeUseDays()
        {
            return $this->before_use_days;
        }

        /**
         * Method setBeforeUseDays
         *
         * @param int $before_use_days
         *
         * @return ProductUseDays
         */
        public function setBeforeUseDays(int $before_use_days)
        {
            $this->before_use_days = $before_use_days;

            return $this;
        }

        /**
         * Method getAfterUseDays
         *
         * @return int
         */
        public function getAfterUseDays()
        {
            return $this->after_use_days;
        }

        /**
         * Method setAfterUseDays
         *
         * @param int $after_use_days [explicite description]
         *
         * @return ProductUseDays
         */
        public function setAfterUseDays(int $after_use_days)
        {
            $this->after_use_days = $after_use_days;

            return $this;
        }
    }
}
