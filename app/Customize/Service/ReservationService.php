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

namespace Customize\Service;

use Carbon\Carbon;
use Customize\Common\Constants;
use Customize\Entity\Holiday;
use Customize\Entity\OrderDetailAdditionalInfo;
use Customize\Entity\ReservationActual;
use Customize\Entity\ReservationInfo;
use Customize\Entity\ReservationSetting;
use Customize\Entity\TimeSlot;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Order;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ReservationService
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ReservationService constructor.
     */
    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $entityManager,
    ) {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    /**
     * Get list time
     *
     * @param string $date
     * @param string $shop
     *
     * @return void
     */
    public function getListTimeDetail(string $date, string $shop)
    {
        $dateParsed = Carbon::parse($date);
        $isHoliday = $this->entityManager->getRepository(Holiday::class)->findOneBy(
            ['date' => $dateParsed]
        );
        $isWeekend = $dateParsed->isWeekend();
        $isTuesday = $dateParsed->dayOfWeek === Constants::TUESDAY;
        $isJulyOrAugustOrMarch = $dateParsed->month === Constants::MARCH || $dateParsed->month === Constants::JULY || $dateParsed->month === Constants::AUGUST;

        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('ts', 'COUNT(DISTINCT ri.id) as totalReservation', 'COUNT(DISTINCT odai.id) as record_count')
            ->from(TimeSlot::class, 'ts')
            ->where('ts.type_slot_time_id = :type_slot_time_id');

        // Take the time frames on holidays
        if ($isHoliday || $isWeekend || $isTuesday && $isJulyOrAugustOrMarch) {
            $queryBuilder->andWhere('ts.is_special_day = :isHoliday')
                ->setParameter('isHoliday', true);
        }
        // Eliminate canceled orders
        $subQuery = $this->entityManager->createQueryBuilder()
            ->select('odai_sub')
            ->from(OrderDetailAdditionalInfo::class, 'odai_sub')
            ->innerJoin(Order::class, 'otb')
            ->andWhere('odai_sub.order_id = otb.id')
            ->andWhere('otb.OrderStatus <> 3');

        $queryBuilder->leftJoin(ReservationInfo::class, 'ri', 'WITH', 'ts.id = ri.time_slot_id AND ri.visit_day = :visitDay AND ri.shop =:shop')
            ->leftJoin(
                OrderDetailAdditionalInfo::class,
                'odai',
                'WITH',
                //  Synchronize the time of the 2 fields
                'SUBSTRING(ts.end_time, 1, 5) = SUBSTRING(odai.time_departure, 1, 5) AND odai.wear_date =:date
                AND odai.order_type = :order_type
                AND odai.visit_store=:shop
                AND  odai.id IN ('.$subQuery->getDQL().')'
            )
            // Just get the user's time frame
            ->setParameter('type_slot_time_id', TimeSlot::TIME_SLOT_OF_USER)
            ->setParameter('visitDay', $date)
            ->setParameter('date', $date)
            ->setParameter('shop', $shop)
            // Orders of type "visit"
            ->setParameter('order_type', 'visit')
            ->groupBy('ts.id');

        $results = $queryBuilder->getQuery()->getResult();
        foreach ($results as $result) {
            $timeSlotsWithTotalPeople[] = [
                'time_slot' => $result[0],
                'total_people' => $result['totalReservation'] ? (int) $result['totalReservation'] + $result['record_count'] : $result['record_count'],
            ];
        }

        return $timeSlotsWithTotalPeople;
    }

    /**
     * Get quantity admin seted
     *
     * @return array
     */
    public function getQuantityCustomerVisit(): array
    {
        $quantityReservationMin = $this->entityManager->getRepository(ReservationSetting::class)->findOneBy(
            ['id' => ReservationSetting::RESERVATION_TRIANGLE_ID]
        );
        $quantityReservationMax = $this->entityManager->getRepository(ReservationSetting::class)->findOneBy(
            ['id' => ReservationSetting::RESERVATION_FULL_SLOT_ID]
        );
        $min = !empty($quantityReservationMin) ? $quantityReservationMin->getValue() : Constants::RESERVATION_TRIANGLE_DEFAULT;
        $max = !empty($quantityReservationMax) ? $quantityReservationMax->getValue() : Constants::RESERVATION_FULL_SLOT_DEFAULT;

        return [
            'quantity_reservation_min' => $min,
            'quantity_reservation_max' => $max,
        ];
    }

    // param store_name in japanes
    public function getListReversationOfStore(string $store_name): array
    {
        $today = date('Y-m-d');
        $qb = $this->entityManager->createQueryBuilder()
            ->select(
                'SUBSTRING(ra.visit_day, 1) as visit_day',
                'ra.quantity as amout_reservation',
                'SUBSTRING(ts.end_time, 1, 5) as endTime'
            )
            ->from(ReservationActual::class, 'ra')
            ->innerJoin(TimeSlot::class, 'ts', 'WITH', 'ra.time_slot_id = ts.id')
            ->where('ra.shop = :shop')
            ->andWhere('ra.visit_day > :today')
            ->setParameter('shop', $store_name)
            ->setParameter('today', $today)
            ->groupBy('ra.shop', 'ra.visit_day', 'ra.time_slot_id')
            ->orderBy('ts.end_time', 'asc')
            ->orderBy('ra.visit_day', 'asc')
            ->getQuery();

        return $qb->getResult();
    }

    // param must have key 'visit_day', 'amout_reservation', 'amout_visit'
    // each item of listReservation like time_slot's instace
    public function getListStatusDateOfStore(array $listReversationActual, string $jaStoreName): array
    {
        $listHoliday = [];
        $triangle = 0;
        $multiply = 0;
        $listCannotBookDate = [];
        $listBookedDate = [];
        $date_visit = [];

        $reservation_setting = $this->entityManager->getRepository(ReservationSetting::class)->findAll();
        $holidays = $this->entityManager->getRepository(Holiday::class)->findAll();
        $orderVisits = $this->getOrderVisit($jaStoreName);

        foreach ($holidays as $holiday) {
            $listHoliday[] = $holiday->getDate()->format('Y-m-d');
        }

        $listOrderVisitFilter = array_filter($orderVisits, function ($order) use ($listHoliday) {
            if (Carbon::parse($order['time_departure']) > Carbon::parse(Constants::TIME_START_RESERVATION)) {
                if (!$this->isSpecialDate($order['wear_date'], $listHoliday)) {
                    return $order;
                } elseif (Carbon::parse($order['time_departure']) <= Carbon::parse(Constants::TIME_END_SPECIAL_DATE)) {
                    return $order;
                }
            }
        });

        // Filter if the reservation exceeds the allowed time frame of user
        $listReversationActualFilter = array_filter($listReversationActual, function ($reservation) use ($listHoliday) {
            if (Carbon::parse($reservation['endTime']) > Carbon::parse(Constants::TIME_START_RESERVATION)) {
                if (!$this->isSpecialDate($reservation['visit_day'], $listHoliday)) {
                    return $reservation;
                } elseif (Carbon::parse($reservation['endTime']) <= Carbon::parse(Constants::TIME_END_SPECIAL_DATE)) {
                    return $reservation;
                }
            }
        });

        $listReservation = array_values($listReversationActualFilter);
        $listOrderVisit = array_values($listOrderVisitFilter);

        $i = 0; // $i is index of array $listReservation
        $j = 0; // $j is index of array $listOrderVisit

        // merged orderVisit into array list reservation
        $arrayMerged = [];
        while (true) {
            if (!isset($listOrderVisit[$j]) && !isset($listReservation[$i])) {
                break;
            }
            if (!isset($listOrderVisit[$j])) {
                $arrayMerged[] = $listReservation[$i++];
                continue;
            } elseif (!isset($listReservation[$i])) {
                $reserVation = $this->convertOrderVisit($listOrderVisit[$j]);
                $arrayMerged[] = $reserVation;
                $j++;
                continue;
            }

            if (
                $listReservation[$i]['visit_day'] == $listOrderVisit[$j]['wear_date']
                && Carbon::parse($listReservation[$i]['endTime'])->greaterThan($listOrderVisit[$j]['time_departure'])
                || Carbon::parse($listReservation[$i]['visit_day'])->greaterThan($listOrderVisit[$j]['wear_date'])
            ) {
                $reserVation = $this->convertOrderVisit($listOrderVisit[$j]);
                $arrayMerged[] = $reserVation;
                $j++;
            } elseif (
                $listReservation[$i]['visit_day'] == $listOrderVisit[$j]['wear_date']
                && $listReservation[$i]['endTime'] == $listOrderVisit[$j]['time_departure']
            ) {
                $listReservation[$i]['amout_visit'] = $listOrderVisit[$j]['amount_oder_visit'];
                $arrayMerged[] = $listReservation[$i];
                $i++;
                $j++;
            } else {
                $arrayMerged[] = $listReservation[$i++];
            }
        }

        // get 2 record and get 2 value column into variable
        foreach ($reservation_setting as $setting) {
            if ($triangle == 0) {
                $triangle = $setting->getValue();
            } elseif ($multiply == 0) {
                $multiply = $setting->getValue();
            }
        }

        // permutation
        if ($triangle > $multiply) {
            $triangle = $triangle + $multiply;
            $multiply = $triangle - $multiply;
            $triangle = $triangle - $multiply;
        }
        foreach ($arrayMerged as $item) {
            $date_visit[$item['visit_day']] = ['triangle' => 0, 'multiply' => 0];
        }

        // List date that have how many type of reversation
        foreach ($arrayMerged as $item) {
            $totalOrder = ($item['amout_reservation'] ?? 0) + ($item['amout_visit'] ?? 0);

            if ($totalOrder >= $triangle && $totalOrder < $multiply) {
                $date_visit[$item['visit_day']]['triangle']++;
            } elseif ($totalOrder >= $multiply) {
                $date_visit[$item['visit_day']]['multiply']++;
            }
        }

        // list the date can book or cannot book
        foreach ($date_visit as $date => $time_slot) {
            $total_time_slot =
                $this->isSpecialDate($date, $listHoliday) ?
                Constants::COUNT_FRAME_TIME_SLOT_SPECIAL :
                Constants::COUNT_FRAME_TIME_SLOT_NORMAL;

            $time_slot_empty = $total_time_slot - $time_slot['triangle'] - $time_slot['multiply'];

            if ($time_slot['multiply'] >= $total_time_slot) {
                $listCannotBookDate[] = $date;
            } elseif ($time_slot_empty <= ceil($total_time_slot / 2)) {
                $listBookedDate[] = $date;
            }
        }

        return [
            'listBookedDate' => $listBookedDate,
            'listCannotBookDate' => $listCannotBookDate,
        ];
    }

    public function isSpecialDate(string $date, array $listHoliday): bool
    {
        $date_time = Carbon::parse($date);

        // saturday, sunday or tuesday of july, august or holiday is special day
        $isHoliday = in_array($date, $listHoliday);
        $isSaturdayOrSunday = in_array($date_time->dayOfWeek, [Constants::SATURDAY, Constants::SUNDAY]);
        $isJulyOrAugustOrMarch = in_array($date_time->month, [Constants::MARCH, Constants::JULY, Constants::AUGUST]);
        $isTuesday = $date_time->dayOfWeek === Constants::TUESDAY;

        if ($isSaturdayOrSunday || ($isJulyOrAugustOrMarch && $isTuesday) || $isHoliday) {
            return true;
        }

        return false;
    }

    /**
     * Method getOrderVisit
     *
     * @param string $jaStoreName [store name in japanese]
     *
     * @return array
     */
    public function getOrderVisit(string $jaStoreName)
    {
        $today = date('Y-m-d');
        $listDayVisit = $this->entityManager->createQueryBuilder()
            ->select('SUBSTRING(odai.wear_date, 1) as wear_date, odai.time_departure, count(odai.id) as amount_oder_visit')
            ->from(OrderDetailAdditionalInfo::class, 'odai')
            ->innerJoin(Order::class, 'o', 'WITH', 'odai.order_id = o.id')
            ->where('o.OrderStatus <> 3')
            ->andWhere('odai.order_type = \'visit\' ')
            ->andWhere('odai.visit_store = :store')
            ->andWhere('odai.wear_date > :today ')
            ->setParameter('store', $jaStoreName)
            ->setParameter('today', $today)
            ->groupBy('odai.wear_date', 'odai.time_departure')
            ->orderBy('odai.time_departure', 'asc')
            ->orderBy('odai.wear_date', 'asc')
            ->getQuery()
            ->getResult();

        return $listDayVisit ?? [];
    }

    public function convertOrderVisit(array $orderVisit): array
    {
        $temp['visit_day'] = $orderVisit['wear_date'];
        $temp['amout_reservation'] = 0;
        $temp['amout_visit'] = $orderVisit['amount_oder_visit'];
        $temp['endTime'] = $orderVisit['time_departure'];

        return $temp;
    }
}
