<?php

declare(strict_types=1);

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UserLoginBlockConfirm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add(
                        'confirm_button', SubmitType::class, [
                                'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 180px;'],
                                'label' => 'Confirm'
                        ]
                )
                ->add(
                        'hidden_user_id', HiddenType::class, [
                        ]
                );
    }
}
