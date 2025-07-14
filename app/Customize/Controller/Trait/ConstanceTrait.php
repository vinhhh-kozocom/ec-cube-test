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

namespace Customize\Controller\Trait;

use Customize\Common\Constants;

trait ConstanceTrait
{
    private $DEFAULT_BEFORE_USE_DAYS;
    private $DEFAULT_AFTER_USE_DAYS;

    public function setUpValue()
    {
        if (isset($_SERVER['SERVER_NAME'])) {
            if ($_SERVER['SERVER_NAME'] === env('SALON_URL')) {
                $this->DEFAULT_BEFORE_USE_DAYS = Constants::RENTAL_DEFAULT_BEFORE_USE_DAYS;
                $this->DEFAULT_AFTER_USE_DAYS = Constants::RENTAL_DEFAULT_AFTER_USE_DAYS;
            } else {
                $this->DEFAULT_BEFORE_USE_DAYS = Constants::DEFAULT_BEFORE_USE_DAYS;
                $this->DEFAULT_AFTER_USE_DAYS = Constants::DEFAULT_AFTER_USE_DAYS;
            }
        }
    }
}
