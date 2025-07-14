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

use Customize\Common\Constants;
use Eccube\Form\Type\Front\NonMemberType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class NonmemberTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($GLOBALS['request']->getLocale() != Constants::LOCALE_JAPAN) {
            $builder->remove('kana');
            $builder->remove('postal_code');
            $builder->remove('address');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return [
            NonMemberType::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        yield NonMemberType::class;
    }
}
