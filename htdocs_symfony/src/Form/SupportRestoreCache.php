<?php

declare(strict_types=1);

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;

class SupportRestoreCache extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add(
                        'radioCacheSelection', ChoiceType::class, [
//                                'attr' => [
//                                        'maxlength' => '100000',
//                                        'overflow' => 'auto',
//                                        'rows' => '10',
//                                ],
//                                'required' => false,
//                                'disabled' => false,
                                'expanded' => false,
                                'multiple' => false,
                                'label' => false,
                                'trim' => true
                        ]
                )
                ->add(
                        'checkboxCoordCountry', CheckboxType::class, [
                                'attr' => ['maxlength' => '10'],
                        ]
                )->add(
                        'checkboxSure', CheckboxType::class, [
                                'attr' => ['maxlength' => '10'],
                        ]
                )->add(
                        'checkboxUserPermission', CheckboxType::class, [
                                'attr' => ['maxlength' => '10'],
                        ]
                );
    }
}
