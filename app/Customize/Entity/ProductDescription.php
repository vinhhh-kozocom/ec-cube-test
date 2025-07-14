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

if (!class_exists('\Customize\Entity\ProductDescription')) {
    /**
     * ProductDescription
     *
     * @ORM\Table(name="dtb_product_description")
     *
     * @ORM\InheritanceType("SINGLE_TABLE")
     *
     * @ORM\HasLifecycleCallbacks()
     *
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     *
     * @ORM\Entity(repositoryClass="Customize\Repository\ProductDescriptionRepository")
     */
    class ProductDescription extends \Eccube\Entity\AbstractEntity
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
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $term_title;

        /**
         * @ORM\Column(type="string", length=3000, nullable=true)
         */
        private $term_description;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $cancelation_title;

        /**
         * @ORM\Column(type="string", length=3000, nullable=true)
         */
        private $cancelation_description;

        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private $location_title;

        /**
         * @ORM\Column(type="string", length=3000, nullable=true)
         */
        private $location_description;

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
        }

        public function getTermTitle()
        {
            return $this->term_title;
        }

        public function setTermTitle($term_title)
        {
            $this->term_title = $term_title;
        }

        public function getTermDescription()
        {
            return $this->term_description;
        }

        public function setTermDescription($term_description)
        {
            $this->term_description = $term_description;
        }

        public function getCancelationTitle()
        {
            return $this->cancelation_title;
        }

        public function setCancelationTitle($cancelation_title)
        {
            $this->cancelation_title = $cancelation_title;
        }

        public function getCancelationDescription()
        {
            return $this->cancelation_description;
        }

        public function setCancelationDescription($cancelation_description)
        {
            $this->cancelation_description = $cancelation_description;
        }

        public function getLocationTitle()
        {
            return $this->location_title;
        }

        public function setLocationTitle($location_title)
        {
            $this->location_title = $location_title;
        }

        public function getLocationDescription()
        {
            return $this->location_description;
        }

        public function setLocationDescription($location_description)
        {
            $this->location_description = $location_description;
        }
    }
}
