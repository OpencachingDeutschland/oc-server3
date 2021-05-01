<?php

namespace Oc\Form;

use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SupportSearchCaches extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // see: https://symfonycasts.com/screencast/symfony-forms/form-type-class
        $builder
            ->add(
                'content_support_searchfield', null, [
                                                 'attr' => [
                                                     'placeholder' => 'OC / GC / Name / Owner / %%%',
                                                     'autofocus' => 'autofocus',
                                                     'size' => '10%',
                                                     'minlength' => '3',
                                                     'maxlength' => '100',
                                                     'style' => 'width: 250px;'
                                                 ],
                                                 'required' => true,
                                                 'disabled' => false,
                                                 'label' => false,
                                                 'trim' => true
                                             ]
            )
            ->add(
                'search_All', SubmitType::class, [
                                'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 60px;'],
                                'label' => '🔍'
                            ]
            )
            ->add(
                'search_One', SubmitType::class, [
                                'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 60px;'],
                                'label' => '🔍=1'
                            ]
            );
    }
}
