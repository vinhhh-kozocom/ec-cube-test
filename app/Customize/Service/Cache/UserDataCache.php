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
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\ProductClass;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class UserDataCache
{
    use KrakiFileTrait;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, EccubeConfig $eccubeConfig)
    {
        $this->entityManager = $entityManager;
        $this->getSettingFile($eccubeConfig);
    }

    public function getSecurePackProductClass()
    {
        $cache = new FilesystemAdapter(Constants::USER_DATA_CACHE, 60 * 60 * 24); // time to auto clear cache (1 day)
        $cacheItem = $cache->getItem(Constants::SECURE_PACK_CACHE_KEY); // this text is name of cache item's key

        if (!$cacheItem->isHit()) {
            $productClass = $this->entityManager->getRepository(ProductClass::class)->findOneBy([
                'code' => $this->settings['secure_pack']['product_code'],
            ]);
            $cacheItem->set($productClass);
            $cache->save($cacheItem);
        }

        return $cacheItem->get();
    }
}
