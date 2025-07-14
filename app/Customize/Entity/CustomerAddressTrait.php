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
 * @EntityExtension("Eccube\Entity\CustomerAddress")
 */
trait CustomerAddressTrait
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="fax", type="string", length=12, nullable=true)
     */
    private $fax;

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
     * @return CustomerAddress
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }
}
