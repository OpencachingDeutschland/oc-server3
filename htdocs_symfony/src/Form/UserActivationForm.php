<?php

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 */
class UserActivationForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email', EmailType::Class, [
                           'attr' => [
                               'autofocus' => 'autofocus',
                               'size' => '10%',
                               'style' => 'width: 250px;'
                           ],
                           'required' => true,
                           'disabled' => false,
                           'label' => false,
                           'trim' => true
                       ]
            )
            ->add(
                'activationCode', null, [
                              'attr' => [
                                  'size' => '10%',
                                  'pattern' => '[A-F0-9]{13}',
                                  'style' => 'width: 250px;'
                              ],
                              'required' => true,
                              'disabled' => false,
                              'label' => false,
                              'trim' => true
                          ]
            )
            ->add(
                'submit', SubmitType::class, [
                            'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 120px;'],
                            'label' => false
                        ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    : void {
        $resolver->setDefaults([]);
    }
}
