<?php

namespace Oc\Form;

use Oc\Entity\UserEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *
 */
class UserRegistrationForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username', null, [
                              'attr' => [
                                  'autofocus' => 'autofocus',
                                  'size' => '10%',
                                  'pattern' => '[a-zA-Z0-9_-]{3,60}',
                                  'style' => 'width: 250px;'
                              ],
                              'required' => true,
                              'disabled' => false,
                              'label' => false,
                              'trim' => true
                          ]
            )
            ->add(
                'firstname', null, [
                               'attr' => [
                                   'size' => '10%',
                                   'minlength' => '3',
                                   'maxlength' => '100',
                                   'style' => 'width: 250px;'
                               ],
                               'required' => false,
                               'disabled' => false,
                               'label' => false,
                               'trim' => true
                           ]
            )
            ->add(
                'lastname', null, [
                              'attr' => [
                                  'size' => '10%',
                                  'minlength' => '3',
                                  'maxlength' => '100',
                                  'style' => 'width: 250px;'
                              ],
                              'required' => false,
                              'disabled' => false,
                              'label' => false,
                              'trim' => true
                          ]
            )
            ->add(
                'country', ChoiceType::Class, [
                             'attr' => [
                                 'expanded' => false,
                                 'multiple' => false,
                                 'style' => 'width: 250px;'
                             ],
                             'choices' => $options['countryList'],
                             'required' => false,
                             'disabled' => false,
                             'label' => false,
                         ]
            )
            ->add(
                'email', EmailType::Class, [
                           'attr' => [
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
                'plainPassword', RepeatedType::Class, [
                                   'options' => [
                                       'attr' => [
                                           'size' => '10%',
                                           'minlength' => '8',
                                           'maxlength' => '60',
                                           // TODO: pattern anpassen. Aktuell: Minimum eight characters, at least one letter, one number and one special character.
                                           'pattern' => '^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$',
                                           'style' => 'width: 250px;'
                                       ]
                                   ],
                                   'first_options' => ['label' => false, 'error_bubbling' => true],
                                   'second_options' => ['label' => false],
                                   'required' => true,
                                   'disabled' => false,
                                   'trim' => true,
                                   'type' => PasswordType::class,
                                   'invalid_message' => 'Your passwords do not match.',
                                   'mapped' => false,
                               ]
            )
            ->add(
                'tos', CheckboxType::class, [
                         'attr' => [],
                         'label' => false,
                         'mapped' => false,
                         'required' => true,
                     ]
            )
            ->add(
                'submit', SubmitType::class, [
                            'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 120px;'],
                            'label' => '🔍=1'
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
        $resolver->setDefaults([
                                   'countryList' => [],
                                   'data_class' => UserEntity::class,
                               ]);
    }
}
