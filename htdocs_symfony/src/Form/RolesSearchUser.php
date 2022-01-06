<?php

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class RolesSearchUser
 */
class RolesSearchUser extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'content_user_searchfield', null, [
                                                 'attr' => [
                                                     'placeholder' => 'User-Id',
                                                     'autofocus' => 'autofocus',
                                                     'size' => '10%',
                                                     'minlength' => '6',
                                                     'maxlength' => '7',
                                                     'style' => 'width: 250px;',
                                                     'pattern' => '^[0-9]{6,7}',
                                                     'title' => 'Only 6-7 numerics are allowed.',
                                                 ],
                                                 'required' => true,
                                                 'disabled' => false,
                                                 'label' => false,
                                                 'trim' => true
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
