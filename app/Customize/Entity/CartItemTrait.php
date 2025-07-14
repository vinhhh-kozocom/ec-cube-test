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

use Customize\Common\Constants;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\CartItem")
 */
trait CartItemTrait
{
    /**
     * @var string
     *
     * @ORM\Column(name="order_type", type="string", length=10, nullable=true)
     */
    private $order_type;

    /**
     * @ORM\Column(name="wear_date", type="date", nullable=true)
     */
    private $wear_date;

    /**
     * @var string
     *
     * @ORM\Column(name="purpose", type="string", length=255, nullable=true)
     */
    private $purpose;

    /**
     * @var string
     *
     * @ORM\Column(name="body_height", type="string", length=50, nullable=true)
     */
    private $body_height;

    /**
     * @var string
     *
     * @ORM\Column(name="foot_size", type="string", length=10, nullable=true)
     */
    private $foot_size;

    /**
     * @var string
     *
     * @ORM\Column(name="body_type", type="string", length=255, nullable=true)
     */
    private $body_type;

    /**
     * @var string
     *
     * @ORM\Column(name="secure_pack", type="string", length=255, nullable=true)
     */
    private $secure_pack;

    /**
     * @var string
     *
     * @ORM\Column(name="need_photo", type="string", length=255, nullable=true)
     */
    private $need_photo;

    /**
     * @var string
     *
     * @ORM\Column(name="need_hair_make", type="string", length=255, nullable=true)
     */
    private $need_hair_make;

    /**
     * @ORM\Column(name="date_visit", type="date", nullable=true)
     */
    private $date_visit;
    /* 20170601 非表示
        private $date_visit;
    */

    /**
     * @var string
     *
     * @ORM\Column(name="time_departure", type="string", length=255, nullable=true)
     */
    private $time_departure;

    /**
     * @var string
     *
     * @ORM\Column(name="visit_store", type="string", length=255, nullable=true)
     */
    private $visit_store;

    /**
     * @ORM\Column(name="actual_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $actual_price;

    /**
     * @var int
     *
     * @ORM\Column(name="before_use_days", type="integer", nullable=true)
     */
    private $before_use_days;

    /**
     * @var int
     *
     * @ORM\Column(name="after_use_days", type="integer", nullable=true)
     */
    private $after_use_days;

    /**
     * @var string
     *
     * @ORM\Column(name="decade", type="string", length=255, nullable=true)
     */
    private $decade;

    /**
     * @var string
     *
     * @ORM\Column(name="language_type", type="string", length=50, nullable=true)
     */
    private $language_type;

    /**
     * @ORM\Column(type="string", length=3000, nullable=true)
     */
    private $remark;

    /**
     * @var int
     *
     * @ORM\Column(name="number_of_guests", type="smallint", nullable=true,  options={"unsigned": true})
     */
    private $number_of_guests;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=20, nullable=true)
     */
    private $gender;

    public function getGender()
    {
        return $this->gender;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    public function getNumberOfGuests()
    {
        return $this->number_of_guests;
    }

    public function setNumberOfGuests($number_of_guests)
    {
        $this->number_of_guests = $number_of_guests;

        return $this;
    }

    public function getRemark()
    {
        return $this->remark;
    }

    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    public function getOrderType()
    {
        return $this->order_type;
    }

    public function setOrderType($order_type)
    {
        $this->order_type = $order_type;

        return $this;
    }

    public function getProductClassId()
    {
        return $this->product_class_id;
    }

    public function setProductClassId($product_class_id)
    {
        $this->product_class_id = $product_class_id;

        return $this;
    }

    public function getPurpose()
    {
        return $this->purpose;
    }

    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;

        return $this;
    }

    public function getBodyHeight()
    {
        return $this->body_height;
    }

    public function setBodyHeight($body_height)
    {
        $this->body_height = $body_height;

        return $this;
    }

    public function getFootSize()
    {
        return $this->foot_size;
    }

    public function setFootSize($foot_size)
    {
        $this->foot_size = $foot_size;

        return $this;
    }

    public function getDecade()
    {
        return $this->decade;
    }

    public function setDecade($decade)
    {
        $this->decade = $decade;

        return $this;
    }

    public function getWearDate()
    {
        return $this->wear_date;
    }

    public function setWearDate($wear_date)
    {
        $this->wear_date = $wear_date;

        return $this;
    }

    public function getBodyType()
    {
        return $this->body_type;
    }

    public function getBodyTypeJsonDecode()
    {
        return json_decode($this->body_type);
    }

    public function setBodyType($body_type)
    {
        $this->body_type = $body_type;

        return $this;
    }

    public function getSecurePack()
    {
        // if secure pack have many type in future, but now it has only one
        if (empty($this->secure_pack)) {
            return;
        }

        return explode(Constants::SECURE_PACKET_SEPARATOR_CHARACTER, $this->secure_pack);
    }

    public function setSecurePack($secure_pack)
    {
        $this->secure_pack = $secure_pack;

        return $this;
    }

    public function getNeedHairMake()
    {
        return $this->need_hair_make;
    }

    public function setNeedHairMake($need_hair_make)
    {
        $this->need_hair_make = $need_hair_make;

        return $this;
    }

    /* 20170601 非表示
      public function getDateVisit() {
        return $this -> date_visit;
      }
    */
    public function setDateVisit($date_visit)
    {
        $this->date_visit = $date_visit;

        return $this;
    }

    public function getTimeDeparture()
    {
        return $this->time_departure;
    }

    public function setTimeDeparture($time_departure)
    {
        $this->time_departure = $time_departure;

        return $this;
    }

    public function getVisitStore()
    {
        return $this->visit_store;
    }

    public function setVisitStore($visit_store)
    {
        $this->visit_store = $visit_store;

        return $this;
    }

    public function setActualPrice($actual_price)
    {
        $this->actual_price = $actual_price;

        return $this;
    }

    public function getActualPrice()
    {
        return $this->actual_price;
    }

    public function getNeedPhoto()
    {
        return $this->need_photo;
    }

    public function setNeedPhoto($need_photo)
    {
        $this->need_photo = $need_photo;

        return $this;
    }

    public function getBeforeUseDays()
    {
        return $this->before_use_days;
    }

    public function setBeforeUseDays($before_use_days)
    {
        $this->before_use_days = $before_use_days;

        return $this;
    }

    public function getAfterUseDays()
    {
        return $this->after_use_days;
    }

    public function setAfterUseDays($after_use_days)
    {
        $this->after_use_days = $after_use_days;

        return $this;
    }

    public function getLanguageType()
    {
        return $this->language_type;
    }

    public function setLanguageType($language_type)
    {
        $this->language_type = $language_type;

        return $this;
    }
}
