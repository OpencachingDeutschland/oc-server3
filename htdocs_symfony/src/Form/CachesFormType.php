<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CachesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // see: https://symfonycasts.com/screencast/symfony-forms/form-type-class
        $builder
            ->add(
                'content_caches_searchfield', null, [
                                                'attr' => [
                                                    'placeholder' => 'OC / GC / Name / Owner. Platzhalter: %%%',
                                                    'autofocus' => 'autofocus',
                                                    'size' => '10%',
                                                    'minlength' => '3',
                                                    'maxlength' => '100',
                                                    'style' => 'width: 300px;'
                                                ],
                                                'disabled' => false,
                                                'label' => false,
                                                'trim' => true
                                            ]
            )
            ->add('Suchen', SubmitType::class, ['attr' => ['class' => 'btn btn-primary']]);
    }
}
