<?php

namespace Oc\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

/**
 *
 */
class SupportImportGPX extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'gpx_file', FileType::class, [
                                'attr' => [
                                    'style' => 'width: 250px;'
                                ],
                                'constraints' => [
                                    new File([
                                                 'maxSize' => '20480000',
                                                 'mimeTypes' => [
                                                     'application/gpx+xml',
                                                     'application/xml',
                                                     'text/xml',
                                                 ],
                                                 'mimeTypesMessage' => 'Please upload a valid GPX/XML document',
                                             ])
                                ],
                                'multiple' => false,
                                'required' => true,
                                'disabled' => false,
                                'label' => false,
                                'mapped' => false
                            ]
            )
            ->add(
                'start_upload', SubmitType::class, [
                                  'attr' => ['class' => 'btn btn-primary', 'style' => 'width: 100px;'],
                                  'label' => '🔍'
                              ]
            );
    }
}
