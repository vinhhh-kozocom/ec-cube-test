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

class Constants
{
    // 商品詳細
    public const PLG_ORDER_TYPE_NAME = 'plg_order_type';
    public const PLG_PURPOSE_NAME = 'plg_purpose';
    public const PLG_BODY_HEIGHT_NAME = 'plg_body_height';
    public const PLG_FOOT_SIZE_NAME = 'plg_foot_size';
    public const PLG_WEAR_DATE_NAME = 'plg_wear_date';
    public const PLG_BODY_TYPE_NAME = 'plg_body_type';
    public const PLG_SECURE_PACK_NAME = 'plg_secure_pack';
    public const PLG_DECADE = 'plg_decade';
    public const PLG_REMARK = 'plg_remark';

    public const PLG_NUMBER_OF_GUESTS = 'plg_number_of_guests';

    public const PLG_GENDER = 'plg_gender';

    public const FORM_SETTINGS = 'form_settings';

    // 商品一覧
    public const DATE_NAME = 'date';

    // 購入確認画面
    public const PLG_NEED_HAIR_MAKE = 'plg_need_hair_make';
    /* 20170601 非表示
  const PLG_DATE_VISIT_NAME     = "plg_date_visit";
*/
    public const PLG_TIME_DEPARTURE_NAME = 'plg_time_departure';
    public const PLG_VISIT_STORE_NAME = 'plg_visit_store';
    public const PLG_NEED_PHOTO = 'plg_need_photo';
    public const PLG_PAY_METHOD = 'plg_pay_method';

    // セッションキー
    public const ORDER_ADDITIONAL_INFO_LIST = 'order_additional_info_list';
    public const ORDER_DETAIL_ADDITIONAL_INFO_LIST = 'order_detail_additional_info_list';
    public const SEARCH_DATE = 'search_date';

    // 内金
    public const ORDER_TOTAL_PRICE = 'order_total_price';
    public const ORDER_TOTAL_PRICE_WITHOUT_TAX = 'order_total_price_without_tax';
    public const CART_ORIGINAL = 'cart_original';
    public const CART_ITEMS_ORIGINAL = 'cart_items_original';

    // 内金(税込み価格)
    public const DEPOSIT_PRICE = 10800;

    // 内金(税抜価格)
    public const DEPOSIT_PRICE_WITHOUT_TAX = 10000;

    // バックオフィス
    public const PLG_BEFORE_USE_DAYS = 'plg_before_use_days';
    public const PLG_AFTER_USE_DAYS = 'plg_after_use_days';

    // 利用日前後デフォルト値
    public const DEFAULT_BEFORE_USE_DAYS = 3;
    public const DEFAULT_AFTER_USE_DAYS = 3;

    public const RENTAL_DEFAULT_BEFORE_USE_DAYS = 4;
    public const RENTAL_DEFAULT_AFTER_USE_DAYS = 5;

    public const FITTING_BEFORE_USE_DAYS = 4;
    public const FITTING_AFTER_USE_DAYS = 3;

    // Register
    public const ALLOW_REGISTER_A_DAY = 2;

    public const SECURE_PACKET_SEPARATOR_CHARACTER = ';';

    public const BANK_TRANSFER_ID = 3;

    public const CREDIT_CARD_NAME = 'クレジットカード';

    public const PAYFAIL = 10;

    public const PAY_SENTED = 6;

    public const ADD_YEAR_NOW = 3;

    public const YEAR_PLUS = 15;

    public const PAYMENT_METHOD_ADMIN = ['クレジットカード', '銀行振込', '来店時店舗支払'];

    public const SALON_NOT_ACCESS =
        [
            '/entry',
            '/shopping/nonmember',
            '/reservation/calendar',
            '/reservation/calendar/detail',
            '/reservation/form',
            '/products/list',
            '/contact',
        ];

    public const SALON_USER_DATA_CAN_ACCESS = [
        '/user_data/choose/adult',
        '/user_data/choose/graduation_entrance',
        '/user_data/choose/wedding',
        '/user_data/choose/party',
        '/user_data/choose/theater_dinner',
        '/user_data/choose/shrine_visit',
        '/user_data/mens',
        '/user_data/hitoenatu',
        '/user_data/big',
    ];
    public const SALON_CATEGORY_CAN_ACCESS = [34, 35, 36, 37, 38, 39, 40, 41, 44, 45, 47, 48, 49, 50, 51, 52, 53, 158, 206, 207, 208, 209, 210, 211, 212, 214, 227, 228, 229, 230, null];
    public const JULY = 7;
    public const AUGUST = 8;
    public const MARCH = 3;
    public const TUESDAY = 2;

    public const RESERVATION_TRIANGLE_DEFAULT = 0;
    public const RESERVATION_FULL_SLOT_DEFAULT = 0;
    public const SATURDAY = 6;

    public const SUNDAY = 0;
    public const COUNT_FRAME_TIME_SLOT_NORMAL = 20;

    public const COUNT_FRAME_TIME_SLOT_SPECIAL = 16;

    // 10:00 AM - 10:30 AM
    public const USER_TIME_START_ID = 9;

    //  7:30 PM - 8:00 PM
    public const USER_TIME_END_NORMAL_DAY_ID = 28;

    // 5:30 PM - 6:00 PM
    public const USER_TIME_END_HOLIDAY_ID = 24;

    public const TIME_END_SPECIAL_DATE = '18:00';

    public const TIME_START_RESERVATION = '10:00';

    public const LOCALE_ENGLISH = 'en';
    public const LOCALE_JAPAN = 'ja';
    public const LOCALE_CHINA = 'zh';
    public const USER_DATA_CACHE = 'user_data_cache';

    public const EXPAND_COLUMN_MATERIAL = '素材';
    public const EXPAND_COLUMN_LANGUAGE = '言語';

    // "secure_pack_cache" is a part (item) of "user_data_cache"
    public const SECURE_PACK_CACHE_KEY = 'secure_pack_cache';

    public const DAY_GRAY_OUT_RESERVATION_AFTER_TODAY = 2;

    // id of record expand product column plugin
    public const LOCALE_RECORD_ID = 5;

    public const PRODUCT_ORDER_ON_LOAN = 1;

    public const PRODUCT_ORDER_RESERVABLE = 2;

    public const SHOP_NAME_SALON = '美容室様';

    public const NOTIFI_DEFERRED_PAYMENT_MAIL_SUBJECT = 'GMO後払い結果通知プログラム';
    public const NOTIFI_CREDITCARD_PAYMENT_MAIL_SUBJECT = 'クレジットカード決済の結果をお知らせするプログラムです。';

    public const PRODUCT_STATUS_PUBLISH = 1;
    public const CREDIT_CARD_ID = 20;
    public const CREDIT_CARD_METHOD_INSTALLMENT = '2';
    public const PAYMENT_METHOD_SPLIT_OPTIONS = ['3', '5', '6', '10', '12', '15', '18', '20', '24'];
}
