<?php

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SupportAdminComment
 *
 * @package Oc\Form
 */
class SupportAdminComment extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'support_admin_comment', TextareaType::class, [
                                           'attr' => [
                                               'maxlength' => '32000',
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
                'save_admin_comment', SubmitType::class, [
                                        'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 180px;'],
                                        'label' => '💾'
                                    ]
            )
            ->add(
                'hidden_repID', HiddenType::class, [
                                  'attr' => ['maxlength' => '10'],
                              ]
            );
    }
}
