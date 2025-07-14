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
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints as Assert;

class TypeCustomerAdminExtenstion extends AbstractTypeExtension
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route') ?: '';
        // if edit, not required. If new customer => required
        $constraint = $route == 'admin_customer_edit' ? [] : new Assert\NotBlank();

        $options = [
            'label' => '会員区分',
            'choices' => [
                '通常' => 'common',
                'サロン用' => 'salon',
            ],
            'expanded' => true,
            'multiple' => false,
            'required' => true,
            'constraints' => $constraint,
            'mapped' => true,
        ];

        $builder->add('type_customer', ChoiceType::class, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CustomerType::class;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        yield CustomerType::class;
    }
}
