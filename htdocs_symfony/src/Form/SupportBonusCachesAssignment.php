<?php

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 *
 */
class SupportBonusCachesAssignment extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content_wp_to_be_assigned', null, [
                                               'attr' => [
                                                   'placeholder' => 'waypoint',
                                                   'size' => '10%',
                                                   'minlength' => '6',
                                                   'maxlength' => '7',
                                                   'pattern' => '[a-fA-FoO0-9]{6,7}',
                                                   'style' => 'width: 250px;'
                                               ],
                                               'required' => false,
                                               'disabled' => false,
                                               'label' => false,
                                               'trim' => true
                                           ]
            )
            ->add(
                'content_wp_that_is_bonus_cache', null, [
                                               'attr' => [
                                                   'placeholder' => 'waypoint bonus',
                                                   'size' => '10%',
                                                   'minlength' => '6',
                                                   'maxlength' => '7',
                                                   'style' => 'width: 250px;'
                                               ],
                                               'required' => true,
                                               'disabled' => false,
                                               'label' => false,
                                               'trim' => true
                                           ]
            )
        ->add(
            'start_assignment', SubmitType::class, [
                              'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 100px;'],
                              'label' => false
                          ]
        );
    }
}
