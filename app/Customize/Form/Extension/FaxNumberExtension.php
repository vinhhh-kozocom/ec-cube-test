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

namespace Customize\Form\Extension;

use Eccube\Form\Type\Admin\CustomerType;
use Eccube\Form\Type\Admin\OrderType;
use Eccube\Form\Type\Admin\ShippingType;
use Eccube\Form\Type\Admin\ShopMasterType;
use Eccube\Form\Type\Front\CustomerAddressType;
use Eccube\Form\Type\Front\EntryType;
use Eccube\Form\Type\Front\ShoppingShippingType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class FaxNumberExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fax', TextType::class, [
            'required' => false,
            'constraints' => [
                new Length([
                    'min' => 10,
                    'max' => 12,
                    'minMessage' => 'form_error.fax_number',
                    'maxMessage' => 'form_error.fax_number',
                ]),
                new Regex([
                    'pattern' => '/^\d+$/',
                    'message' => 'errors.numeric_only',
                ]),
            ],
            'attr' => [
                'placeholder' => 'common.fax_number_sample',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return [
            EntryType::class,
            OrderType::class,
            ShippingType::class,
            CustomerType::class,
            ShoppingShippingType::class,
            CustomerAddressType::class,
            ShopMasterType::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        yield EntryType::class;
        yield OrderType::class;
        yield ShippingType::class;
        yield CustomerType::class;
        yield ShoppingShippingType::class;
        yield CustomerAddressType::class;
        yield ShopMasterType::class;
    }
}
