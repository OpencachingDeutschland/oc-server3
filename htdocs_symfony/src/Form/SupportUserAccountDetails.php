<?php

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SupportUserAccountDetails
 *
 * @package Oc\Form
 */
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
