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

namespace Customize\Service\Cache;

use Customize\Common\Constants;
use Customize\Controller\Trait\KrakiFileTrait;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\ProductClass;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class DetectSecurePack
{
    use KrakiFileTrait;

    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->getSettingFile($eccubeConfig);
    }

    // this event will be call when productclass table was updated
    public function postUpdate(ProductClass $productClass, LifecycleEventArgs $event): void
    {
        if ($productClass->getCode() == $this->settings['secure_pack']['product_code']) {
            $cache = new FilesystemAdapter(Constants::USER_DATA_CACHE);
            $cache->deleteItem(Constants::SECURE_PACK_CACHE_KEY);
        }
    }
}
