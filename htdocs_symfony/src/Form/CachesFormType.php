<?php

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CachesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // see: https://symfonycasts.com/screencast/symfony-forms/form-type-class
        $builder->add(
            'content_caches_searchfield', null, [
                                            'label' => 'What are you looking for?',
                                            'disabled' => false,
                                            'trim' => true,
                                            'help' => 'TIPP: Nur Wegpunkte oder Teile des Cachetitels werden nachgeschlagen. Um alle Caches aufzulisten: %'
                                        ]
        );;
    }
}
