<?php

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CoordinatesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // see: https://symfonycasts.com/screencast/symfony-forms/form-type-class
        $builder
            ->add(
                'content_coordinates_searchfield', null, [
                                                'attr' => [
                                                    'placeholder' => 'z.B. 5.01234 -10.56789',
                                                    'autofocus' => 'autofocus',
                                                    'size' => '10%',
                                                    'minlength' => '3',
                                                    'maxlength' => '100',
                                                    'style' => 'width: 300px;',
                                                    'pattern' => '^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$', // TODO
                                                    'title' => 'Only numerics, comma, dot, minus and space are allowed.',
                                                ],
                                                'disabled' => false,
                                                'label' => false,
                                                'trim' => true
                                            ]
            )
            ->add('Umwandeln', SubmitType::class, ['attr' => ['class' => 'btn btn-primary']]);
    }
}
