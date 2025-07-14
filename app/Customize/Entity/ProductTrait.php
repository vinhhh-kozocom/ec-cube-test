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
use Eccube\Annotation\EntityExtension;

/**
 * @EntityExtension("Eccube\Entity\Product")
 */
trait ProductTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="product_order_status_id", type="integer", nullable=true)
     */
    private $product_order_status_id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Customize\Entity\GrayoutManagement", mappedBy="product_id", cascade={"remove"})
     *
     * @ORM\OrderBy({
     *     "id"="ASC"
     * })
     */
    private $grayout_management;

    public function __construct()
    {
        $this->grayout_management = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get product_order_status_id.
     *
     * @return string
     */
    public function getGrayoutManagement()
    {
        return $this->grayout_management;
    }

    /**
     * Get product_order_status_id.
     *
     * @return string
     */
    public function getProductOrderStatusId()
    {
        return $this->product_order_status_id;
    }

    /**
     * Set product_order_status_id.
     *
     * @param string|null $product_order_status_id
     *
     * @return Customer
     */
    public function setProductOrderStatusId($productOrderStatusId)
    {
        $this->product_order_status_id = $productOrderStatusId;

        return $this;
    }

    /** 20170522 追加
     * 商品がカテゴリーに属しているかどうかチェックする 子カテゴリーは考慮しない
     *
     * @param $category \Eccube\Entity\Category|integer|string
     *
     * @return bool
     */
    public function belongsToCategory($category)
    {
        if ($category instanceof \Eccube\Entity\Category) {
            $category = $category->getId();
        }

        foreach ($this->ProductCategories as $C) {
            if (is_int($category)) {
                if ($C->getCategoryId() === $category) {
                    return true;
                }
            } elseif (is_string($category)) {
                if ($C->getCategory()->getName() === $category) {
                    return true; // もし同じ名前のカテゴリーが複数登録されていればこの比較はできません
                }
            } else {
                // throw new \Exception()するなりお好きに
            }
        }

        return false;
    }
}
