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
 * @EntityExtension("Eccube\Entity\Order")
 */
trait OrderTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="fax", type="string", length=12, nullable=true)
     */
    private $fax;

    /**
     * @var string|null
     *
     * @ORM\Column(name="locale", type="string", length=12, nullable=true)
     */
    private $locale;

    /**
     * @var string|null
     *
     * @ORM\Column(name="creditcard_method", type="string", nullable=true, options={"comment": "1: Bulk, 2: Installment"})
     */
    private $creditcard_method;

    /**
     * @var string|null
     *
     * @ORM\Column(name="creditcard_paytime", type="string", nullable=true)
     */
    private $creditcard_paytime;

    /**
     * @ORM\Column(name="access_id", type="string", length=255, nullable=true)
     */
    private $access_id;

    /**
     * @ORM\Column(name="access_pass", type="string", length=255, nullable=true)
     */
    private $access_pass;

    /**
     * Get access_id.
     *
     * @return string
     */
    public function getAccessId()
    {
        return $this->access_id;
    }

    /**
     * Set access_id.
     *
     * @param string|null $access_id
     *
     * @return Order
     */
    public function setAccessId($access_id)
    {
        $this->access_id = $access_id;

        return $this;
    }

    /**
     * Get access_pass.
     *
     * @return string
     */
    public function getAccessPass()
    {
        return $this->access_pass;
    }

    /**
     * Set access_pass.
     *
     * @param string|null $access_pass
     *
     * @return Order
     */
    public function setAccessPass($access_pass)
    {
        $this->access_pass = $access_pass;

        return $this;
    }

    /**
     * Get fax.
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set fax.
     *
     * @param string|null $fax
     *
     * @return Order
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get creditcard method.
     *
     * @return string
     */
    public function getCreditcardMethod()
    {
        return $this->creditcard_method;
    }

    /**
     * Get creditcard method.
     *
     * @return string
     */
    public function setCreditcardMethod($creditcard_method)
    {
        $this->creditcard_method = $creditcard_method;

        return $this;
    }

    /**
     * Get creditcard method.
     *
     * @return string
     */
    public function getCreditcardPaytime()
    {
        return $this->creditcard_paytime;
    }

    /**
     * Get creditcard method.
     *
     * @return string
     */
    public function setCreditcardPaytime($creditcard_paytime)
    {
        $this->creditcard_paytime = $creditcard_paytime;

        return $this;
    }
}
