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

if (!class_exists('\Customize\Entity\ProductUseDate')) {
    /**
     * ProductUseDate
     *
     * @ORM\Entity
     *
     * @ORM\Table(name="plg_product_use_date")
     *
     * @ORM\HasLifecycleCallbacks()
     */
    class ProductUseDate extends \Eccube\Entity\AbstractEntity
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
         * @ORM\Column(name="product_id", type="integer")
         */
        private $product_id;

        /**
         * @ORM\Column(name="use_date", type="date")
         */
        private $use_date;

        public function getId()
        {
            return $this->id;
        }

        public function getProductId()
        {
            return $this->product_id;
        }

        public function getUseDate()
        {
            return $this->use_date;
        }

        public function setProductUseDateId($product_use_date_id)
        {
            $this->product_use_date_id = $product_use_date_id;

            return $this;
        }

        public function setProductId($product_id)
        {
            $this->product_id = $product_id;

            return $this;
        }

        public function setUseDate($use_date)
        {
            $this->use_date = $use_date;

            return $this;
        }
    }
}
