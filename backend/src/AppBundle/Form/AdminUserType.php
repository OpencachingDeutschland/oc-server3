<?php

namespace AppBundle\Form;

use AdamQuaile\Bundle\FieldsetBundle\Form\FieldsetType;
use Mirsch\Bundle\AdminBundle\Form\AdminUserType as MirschAdminUserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminUserType extends MirschAdminUserType
{

    /**
     * add enable fields like 'isActive' to the form
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    protected function buildEnableFields(FormBuilderInterface $builder)
    {
        $builder->add('enable_group', FieldsetType::class, [
            'legend' => 'mirsch.admin.form.legend.enable',
            'attr' => [
                'class' => 'box-primary',
            ],
            'fields' => function (FormBuilderInterface $builder) {
                $builder->add('isActive', CheckboxType::class, [
                    'required' => false,
                    'label' => 'mirsch.admin.form.is_active',
                ]);
                $builder->add('isAdmin', CheckboxType::class, [
                    'required' => false,
                    'label' => 'admin.form.is_admin',
                ]);
            },
        ]);
    }

}
