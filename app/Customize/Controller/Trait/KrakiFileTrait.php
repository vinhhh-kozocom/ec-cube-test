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
use Symfony\Component\Yaml\Parser;

trait KrakiFileTrait
{
    /**
     * settings
     *
     * @var mixed
     */
    private $settings;

    public function getSettingFile($eccubeConfig, $orderHelper = null)
    {
        $root = $eccubeConfig['root_dir'];

        $yaml = new Parser();
        // ?? '' help avoid error message when run command, batch
        if (($_SERVER['SERVER_NAME'] ?? '') === env('SALON_URL')) {
            $url = $root.$eccubeConfig['setting_salon_url'];
        } else {
            $locale = !empty($GLOBALS['request']) ? $GLOBALS['request']->getLocale() : Constants::LOCALE_JAPAN;
            if (!empty($GLOBALS['request'])) {
                $urlArray = explode('/', $GLOBALS['request']->getPathInfo());
                // if access admin order
                $adminRoute = env('ECCUBE_ADMIN_ROUTE', 'admin');
                if ($urlArray[1] == $adminRoute && ($urlArray[2] ?? '') == 'order') {
                    !(empty($orderHelper)) ? $locale = $orderHelper->getLocaleOfOrder($urlArray[3]) : '';
                }
            }
            $url = isset($eccubeConfig["setting_{$locale}_url"]) && $locale !== Constants::LOCALE_JAPAN
                ? $root.$eccubeConfig["setting_{$locale}_url"]
                : $root.$eccubeConfig['setting_url'];
        }
        $this->settings = $yaml->parse(file_get_contents($url))['settings'];
    }
}
