<?php

declare(strict_types=1);

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SimpleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add(
                        'content_searchfield', null, [
                                'attr' => [
                                        'placeholder' => 'Your input here',
                                        'autofocus' => 'autofocus',
                                        'size' => '10%',
                                        'minlength' => '3',
                                        'style' => 'width: 250px;',
                                ],
                                'required' => true,
                                'disabled' => false,
                                'label' => false,
                                'trim' => true
                        ]
                )
                ->add(
                        'button_submit', SubmitType::class, [
                                'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 60px;'],
                                'label' => 'Submit'
                        ]
                );
    }
}
