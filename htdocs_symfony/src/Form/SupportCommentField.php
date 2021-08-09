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
class SupportCommentField extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content_comment_field', TextareaType::class, [
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
                'save_comment_button', SubmitType::class, [
                                         'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 180px;'],
                                         'label' => '💾'
                                     ]
            )
            ->add(
                'hidden_ID', HiddenType::class, [
                               'attr' => ['maxlength' => '10'],
                           ]
            )
            ->add(
                'hidden_ID2', HiddenType::class, [
                                'attr' => ['maxlength' => '10'],
                            ]
            )
            ->add(
                'hidden_sender', HiddenType::class, [
                                'attr' => ['maxlength' => '10'],
                            ]
    );
    }
}
