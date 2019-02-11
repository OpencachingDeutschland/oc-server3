<?php

namespace Oc\FieldNotes\Form;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class UploadType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'field_notes.upload.file',
                ]
            );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetFormData']);
    }

    /**
     * Event listener for pre set data.
     */
    public function onPreSetFormData(FormEvent $event): void
    {
        /**
         * @var UploadFormData
         */
        $data = $event->getData();

        // check if we have a last log date
        // if so, show the checkbox to ignore logs before this date
        if ($data->ignoreBeforeDate) {
            $ignoreFieldNotesBeforeLabel = $this->translator->trans(
                'field_notes.upload.label.ignore',
                ['%date%' => $this->getFormattedDate($data->ignoreBeforeDate)]
            );

            $event->getForm()->add(
                'ignore',
                CheckboxType::class,
                [
                    'required' => false,
                    'attr' => [
                        'checked' => 'checked',
                    ],
                    'label' => $ignoreFieldNotesBeforeLabel,
                ]
            );
        }
    }

    private function getFormattedDate(DateTime $date): string
    {
        return $date->format(
            $this->translator->trans('field_notes.date_format')
        );
    }

    /**
     * Configure form.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UploadFormData::class,
        ]);
    }
}
