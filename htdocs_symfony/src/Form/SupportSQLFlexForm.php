<?php

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SupportSQLFlexForm
 *
 * @package Oc\Form
 */
class SupportSQLFlexForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content_SELECT', ChoiceType::class, [
                                    'choices' => ['SELECT' => 'SELECT'],
                                    'attr' => [
                                        'style' => 'width: 180px;'
                                    ],
                                    'disabled' => true,
                                    'label' => false,
                                    'trim' => true
                                ]
            )
            ->add(
                'content_WHAT', null, [
                                  'required' => true,
                                  'data' => '*',
                                  'attr' => [
                                      'style' => 'width: 180px;'
                                  ],
                                  'disabled' => false,
                                  'label' => false,
                                  'trim' => true
                              ]
            )
            ->add(
                'content_FROM', ChoiceType::class, [
                                  'choices' => ['FROM' => 'FROM'],
                                  'attr' => [
                                      'style' => 'width: 180px;'
                                  ],
                                  'disabled' => true,
                                  'label' => false,
                                  'trim' => true
                              ]
            )
            ->add(
                'content_TABLE', ChoiceType::class, [
                                   'choices' => ['caches' => 'caches', 'user' => 'user'],
                                   'attr' => [
                                       'style' => 'width: 180px;'
                                   ],
                                   'disabled' => false,
                                   'label' => false,
                                   'trim' => true
                               ]
            )
            ->add(
                'content_WHERE', ChoiceType::class, [
                                  'choices' => ['WHERE' => 'WHERE'],
                                  'attr' => [
                                      'style' => 'width: 180px;'
                                  ],
                                  'disabled' => true,
                                  'label' => false,
                                  'trim' => true
                              ]
            )
            ->add(
                'content_CONDITION', null, [
                                  'required' => false,
                                  'data' => '',
                                  'attr' => [
                                      'placeholder' => 'id=\'123\'',
                                      'style' => 'width: 180px;'
                                  ],
                                  'disabled' => false,
                                  'label' => false,
                                  'trim' => true
                              ]
            )
            ->add(
                'Suchen', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
                'label' => '🔍'
            ]
            );
    }
}
