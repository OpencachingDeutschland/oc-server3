<?php


namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CachesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // see: https://symfonycasts.com/screencast/symfony-forms/form-type-class
        $builder->add('content_caches_searchfield', null, ['label' => 'What are you looking for?', 'disabled' => false, 'trim' => true, 'help' => 'SQL: SELECT * FROM caches
            WHERE wp_oc = "???" OR wp_gc = "???" OR wp_nc = "???" OR name LIKE "%???%"']);
    }

}
