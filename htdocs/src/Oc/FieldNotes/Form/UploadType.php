<?php

namespace Oc\FieldNotes\Form;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
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

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @throws UnexpectedTypeException
     * @throws LogicException
     * @throws AlreadySubmittedException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
     *
     * @param FormEvent $event
     */
    public function onPreSetFormData(FormEvent $event)
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

    /**
     * @param DateTime $date
     *
     * @return string
     */
    private function getFormattedDate(DateTime $date)
    {
        return $date->format(
            $this->translator->trans('field_notes.date_format')
        );
    }

    /**
     * Configure form.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UploadFormData::class,
        ]);
    }
}
