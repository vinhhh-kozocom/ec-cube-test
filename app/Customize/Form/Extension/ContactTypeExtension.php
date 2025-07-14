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

use Eccube\Form\Type\Front\ContactType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('postal_code');
        $builder
            ->add(
                'store',
                ChoiceType::class,
                [
                    'label' => '特定の店舗にお問い合わせご希望',
                    'choices' => [
                        '渋谷本店' => '渋谷本店',
                        '銀座店' => '銀座店',
                        '池袋店' => '池袋店',
                        '横浜店' => '横浜店',
                        '熱海店（PETIT）' => '熱海店（PETIT）',
                        '指定なし' => '指定なし',
                    ],
                    'required' => false,
                    'placeholder' => false,
                ]
            )
            ->add(
                'reply',
                ChoiceType::class,
                [
                    'label' => '返信の方法',
                    'choices' => [
                        '' => '',
                        'メール' => 'メール',
                        '電話' => '電話',
                    ],
                    'required' => true,
                    'placeholder' => false,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ContactType::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [ContactType::class];
    }
}
