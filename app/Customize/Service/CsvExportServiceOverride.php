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

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Repository\CsvRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Repository\ShippingRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormFactoryInterface;

class CsvExportServiceOverride extends \Eccube\Service\CsvExportService
{
    /**
     * @var resource
     */
    protected $fp;

    /**
     * @var bool
     */
    protected $closed = false;

    /**
     * @var \Closure
     */
    protected $convertEncodingCallBack;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var QueryBuilder;
     */
    protected $qb;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var CsvType
     */
    protected $CsvType;

    /**
     * @var Csv[]
     */
    protected $Csvs;

    /**
     * @var CsvRepository
     */
    protected $csvRepository;

    /**
     * @var CsvTypeRepository
     */
    protected $csvTypeRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /** @var PaginatorInterface */
    protected $paginator;

    /**
     * CsvExportService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CsvRepository $csvRepository
     * @param CsvTypeRepository $csvTypeRepository
     * @param OrderRepository $orderRepository
     * @param ShippingRepository $shippingRepository
     * @param CustomerRepository $customerRepository
     * @param ProductRepository $productRepository
     * @param EccubeConfig $eccubeConfig
     * @param FormFactoryInterface $formFactory
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CsvRepository $csvRepository,
        CsvTypeRepository $csvTypeRepository,
        OrderRepository $orderRepository,
        ShippingRepository $shippingRepository,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository,
        EccubeConfig $eccubeConfig,
        FormFactoryInterface $formFactory,
        PaginatorInterface $paginator,
    ) {
        $this->entityManager = $entityManager;
        $this->csvRepository = $csvRepository;
        $this->csvTypeRepository = $csvTypeRepository;
        $this->orderRepository = $orderRepository;
        $this->shippingRepository = $shippingRepository;
        $this->customerRepository = $customerRepository;
        $this->eccubeConfig = $eccubeConfig;
        $this->productRepository = $productRepository;
        $this->formFactory = $formFactory;
        $this->paginator = $paginator;
    }

    /**
     * CSV出力項目と比較し, 合致するデータを返す.
     *
     * @param Csv $Csv
     * @param $entity
     *
     * @return string|null
     */
    public function getData(Csv $Csv, $entity)
    {
        // エンティティ名が一致するかどうかチェック.
        $csvEntityName = str_replace('\\\\', '\\', $Csv->getEntityName());
        $entityName = ClassUtils::getClass($entity);
        $colValueEntity = null;
        $plg = false;
        if ($csvEntityName == 'Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumns') {
            $plg = true;
            $colValueEntity = $this->entityManager
                ->getRepository(\Plugin\PlgExpandProductColumns\Entity\PlgExpandProductColumnsValue::class)
                ->findOneBy([
                    'productId' => $entity->getId(),
                    'columnId' => str_replace('PlgExpandProductColumns_', '', $Csv->getFieldName()),
                ]);
            if (is_null($colValueEntity)) {
                return null;
            }
        }
        if ($csvEntityName !== $entityName && $plg === false) {
            return null;
        }
        if ($plg) {
            if (!$colValueEntity->offsetExists('value')) {
                return null;
            }
        } else {
            if (!$entity->offsetExists($Csv->getFieldName())) {
                return null;
            }
        }
        // カラム名がエンティティに存在するかどうかをチェック.
        if ($plg) {
            $data = $colValueEntity->offsetGet('value');
        } else {
            $data = $entity->offsetGet($Csv->getFieldName());
        }
        // データを取得.
        // one to one の場合は, dtb_csv.reference_field_name, 合致する結果を取得する.
        if ($data instanceof \Eccube\Entity\AbstractEntity) {
            return $data->offsetGet($Csv->getReferenceFieldName());
        } elseif ($data instanceof \Doctrine\Common\Collections\Collection) {
            // one to manyの場合は, カンマ区切りに変換する.
            $array = [];
            foreach ($data as $elem) {
                $array[] = $elem->offsetGet($Csv->getReferenceFieldName());
            }
            if ($Csv->getFieldName() == 'ProductImage') {
                $joinedString = implode($this->eccubeConfig['eccube_csv_export_multidata_separator'], $array);

                return str_replace('"', '', $joinedString);
            }

            return '"'.implode($this->eccubeConfig['eccube_csv_export_multidata_separator'], $array).'"';
        } elseif ($data instanceof \DateTime) {
            // datetimeの場合は文字列に変換する.
            return $data->format($this->eccubeConfig['eccube_csv_export_date_format']);
        } elseif (is_bool($data)) {
            // booleanの場合は文字列に変換する.
            return $data ? '1' : '0';
        } else {
            // スカラ値の場合はそのまま.
            return $data;
        }
    }
}
