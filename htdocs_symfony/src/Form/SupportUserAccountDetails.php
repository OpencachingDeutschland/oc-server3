<?php

declare(strict_types=1);

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class SupportUserAccountDetails extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add(
                        'button_account_inactive', SubmitType::class, [
                                'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 180px;'],
                                'label' => false
                        ]
                )
                ->add(
                        'button_login_block', SubmitType::class, [
                                'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 180px;'],
                                'label' => false
                        ]
                )
                ->add(
                        'dropDown_login_block', ChoiceType::class, [
                                'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 180px;'],
                                'expanded' => false,
                                'multiple' => false,
                                'choices'  => [
                                        'Clear existing block' => -1,
                                        '0' => 0,
                                        '1' => 1,
                                        '7' => 7,
                                        '14' => 14,
                                        '30' => 30],
                                'data' => 0,
                                'empty_data' => 0,
                                'label' => false
                        ]
                )
                ->add(
                        'message_login_block', TextareaType::class, [
                                'attr' => [
                                        'maxlength' => '100000',
                                        'overflow' => 'auto',
                                        'rows' => '10',
                                ],
                                'required' => false,
                                'disabled' => false,
                                'label' => false,
                                'trim' => true
                        ]
                )
                ->add(
                        'button_GDPR_deletion', SubmitType::class, [
                                'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 180px;'],
                                'label' => false
                        ]
                )
                ->add(
                        'button_mark_email_invalid', SubmitType::class, [
                                'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 180px;'],
                                'label' => false
                        ]
                )
                ->add(
                        'check_Sure', CheckboxType::class, [
                                'mapped' => false,
                                'label' => false,
                        ]
                );
    }
}
