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

namespace Customize\Common;

class Utils
{
    public static function getArrayFromSettings($settings, $key)
    {
        if ($key == 'order_type' || $key == 'pay_method') {
            return $settings['form'][$key];
        }

        $ret = [];
        if (isset($settings['form'][$key])) {
            $values = $settings['form'][$key];
            foreach ($values as $value) {
                $ret[$value] = $value;
            }
        }

        return $ret;
    }

    public static function getOrderType($settings)
    {
        return self::getArrayFromSettings($settings, 'order_type');
    }

    public static function getPurpose($settings)
    {
        return self::getArrayFromSettings($settings, 'purpose');
    }

    public static function getFootSize($settings)
    {
        return self::getArrayFromSettings($settings, 'foot_size');
    }

    public static function getBodyHeight($settings)
    {
        return self::getArrayFromSettings($settings, 'body_height');
    }

    public static function getBodyType($settings)
    {
        return self::getArrayFromSettings($settings, 'body_type');
    }

    public static function getHairMake($settings)
    {
        return self::getArrayFromSettings($settings, 'hair_make');
    }

    public static function getTimeDeparture($settings)
    {
        return self::getArrayFromSettings($settings, 'time_departure');
    }

    public static function getVisitStore($settings)
    {
        return self::getArrayFromSettings($settings, 'visit_store');
    }

    public static function getSecurePack($settings)
    {
        return self::getArrayFromSettings($settings, 'secure_pack');
    }

    public static function getPhotoPlan($settings)
    {
        return self::getArrayFromSettings($settings, 'photo_plan');
    }

    public static function getPayMethod($settings)
    {
        return self::getArrayFromSettings($settings, 'pay_method');
    }

    public static function getDecade($settings)
    {
        return self::getArrayFromSettings($settings, 'decade');
    }

    public static function getNumberOfGuests($settings)
    {
        return self::getArrayFromSettings($settings, 'number_of_guests');
    }

    public static function getGender($settings)
    {
        return self::getArrayFromSettings($settings, 'gender');
    }
}
