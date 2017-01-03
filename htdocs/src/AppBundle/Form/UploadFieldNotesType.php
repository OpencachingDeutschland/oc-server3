<?php

namespace AppBundle\Form;

use AppBundle\Util\DateUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;

class UploadFieldNotesType extends AbstractType
{
    const FIELD_FILE = 'file';
    const FIELD_IGNORE = 'ignore';
    const FIELD_IGNORE_DATE = 'ignore-date';

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * UploadFieldNotesType constructor.
     *
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     * @return void
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \Symfony\Component\Form\Exception\AlreadySubmittedException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                self::FIELD_IGNORE_DATE,
                HiddenType::class
            )
            ->add(
                self::FIELD_FILE,
                FileType::class,
                [
                    'label' => 'field_notes.upload.file'
                ]
            );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // check if we have a last log date
            // if so, show the checkbox to ignore logs before this date
            if ($data[self::FIELD_IGNORE_DATE]) {
                $date = DateUtil::dateTimeFromMySqlFormat($data[self::FIELD_IGNORE_DATE])->format(
                    $this->translator->trans('field_notes.date_format')
                );
                $form->add(
                    self::FIELD_IGNORE,
                    CheckboxType::class,
                    [
                        'required' => false,
                        'attr' => ['checked' => 'checked'],
                        /** @Desc("Ignore Field Notes before %date%") */
                        'label' => $this->translator->trans('field_notes.upload.label.ignore', ['%date%' => $date]),
                    ]
                );
            }
        });
    }
}
