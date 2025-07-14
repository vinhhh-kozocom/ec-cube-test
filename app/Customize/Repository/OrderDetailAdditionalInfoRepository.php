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

namespace Customize\Repository;

use Customize\Common\Constants;
use Customize\Controller\Trait\ConstanceTrait;
use Customize\Entity\OrderDetailAdditionalInfo;
use Doctrine\Persistence\ManagerRegistry as RegistryInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Doctrine\Query\Queries;
use Eccube\Entity\Order;
use Eccube\Entity\Product;
use Eccube\Repository\AbstractRepository;

class OrderDetailAdditionalInfoRepository extends AbstractRepository
{
    use ConstanceTrait;
    /**
     * @var Queries
     */
    protected $queries;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * ProductRepository constructor.
     *
     * @param RegistryInterface $registry
     * @param Queries $queries
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        RegistryInterface $registry,
        Queries $queries,
        EccubeConfig $eccubeConfig,
    ) {
        parent::__construct($registry, OrderDetailAdditionalInfo::class);
        $this->queries = $queries;
        $this->eccubeConfig = $eccubeConfig;
        $this->setUpValue();
    }

    public function getListNotAvailableDates($en, $productId, $typeOder = null)
    {
        $product = $en->getRepository(Product::class)->findOneBy(['id' => $productId]);
        $productClassId = $product->getProductClasses()[0]->getId();

        $beforeUseDays = $this->DEFAULT_BEFORE_USE_DAYS;
        $afterUseDays = $this->DEFAULT_AFTER_USE_DAYS;

        $today = date('Y-m-d', strtotime('-5 days'));
        $orderDetailAdditionalInfoList = $en->createQueryBuilder()
            ->select('odai')
            ->from(OrderDetailAdditionalInfo::class, 'odai')
            ->innerJoin(Order::class, 'odr')
            ->where('odai.product_class_id = :product_class_id and odai.wear_date >= :wear_date')
            ->andWhere('odai.order_id = odr.id')
            ->andWhere('odr.OrderStatus <> 3')
            ->setParameter('product_class_id', $productClassId)
            ->setParameter('wear_date', $today)
            ->getQuery()
            ->getResult();

        $not_available_dates = [];
        foreach ($orderDetailAdditionalInfoList as $orderDetailAdditionalInfo) {
            if (!empty($orderDetailAdditionalInfo->getBeforeUseDays())) {
                $beforeUseDays = $orderDetailAdditionalInfo->getBeforeUseDays();
            }
            if (!empty($orderDetailAdditionalInfo->getAfterUseDays())) {
                $afterUseDays = $orderDetailAdditionalInfo->getAfterUseDays();
            }
            $wearDate = $orderDetailAdditionalInfo->getWearDate()->format('Y-m-d');

            if ($typeOder === 'fitting') {
                $beforeUseDays = Constants::FITTING_BEFORE_USE_DAYS;
                $afterUseDays = Constants::FITTING_AFTER_USE_DAYS;
            }

            if ($typeOder === 'flex') {
                $type = $orderDetailAdditionalInfo->getOrderType();
                if ($type == 'fitting') {
                    $beforeUseDays = Constants::FITTING_BEFORE_USE_DAYS;
                    $afterUseDays = Constants::FITTING_AFTER_USE_DAYS;
                }
            }

            $not_available_dates[] = $wearDate;

            for ($i = $beforeUseDays; $i > 0; $i--) {
                $tmpDate = strtotime($wearDate." - {$i} day");
                $not_available_dates[] = date('Y-m-d', $tmpDate);
            }
            for ($i = 1; $i <= $afterUseDays; $i++) {
                $tmpDate = strtotime($wearDate." + {$i} day");
                $not_available_dates[] = date('Y-m-d', $tmpDate);
            }
        }

        $not_available_dates = array_unique($not_available_dates);
        sort($not_available_dates);
        $parameters = ['not_available_dates' => $not_available_dates];

        $start_year = date('Y', time());
        $start_month = date('m', time());

        // // 当月を出す
        $year = $start_year;
        $month = $start_month;
        $today = date('Y-m-d');
        $current_month_time = strtotime("$year/$month/1");
        $end_of_month = date('t', $current_month_time);
        $start_week = date('w', $current_month_time);

        $next_month_time = strtotime("$year/$month/1 + 1 month");
        $next_month_year = date('Y', $next_month_time);
        $next_month = date('m', $next_month_time);
        $next_end_of_month = date('t', $next_month_time);
        $next_month_start_week = date('w', $next_month_time);

        $parameters += ['today' => $today];
        $parameters += ['year' => intval($year)];
        $parameters += ['current_month' => intval($month)];
        $parameters += ['current_end_of_month' => intval($end_of_month)];
        $parameters += ['start_week' => intval($start_week)];

        $parameters += ['next_month_year' => intval($next_month_year)];
        $parameters += ['next_month' => intval($next_month)];
        $parameters += ['next_end_of_month' => intval($next_end_of_month)];
        $parameters += ['next_month_start_week' => intval($next_month_start_week)];

        return $parameters;
    }
}
