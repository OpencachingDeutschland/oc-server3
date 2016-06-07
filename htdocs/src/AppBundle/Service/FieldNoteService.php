<?php

namespace AppBundle\Service;

use AppBundle\Entity\FieldNote;
use AppBundle\Exception\WrongFileFormatException;
use AppBundle\Service\Traits\ErrorTrait;
use AppBundle\Util\ArrayUtil;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FieldNoteService
{
    use ErrorTrait;

    const FIELD_NOTE_DATETIME_FORMAT = 'Y-m-d\TH:i:s\Z';
    const LOG_TYPE = [
        'Found it' => 1,
        "Didn't find it" => 2,
    ];

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * FieldNoteService constructor.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @param string $fileName
     * @param int $userId
     *
     * @return bool
     * @throws \AppBundle\Exception\WrongFileFormatException
     */
    public function importFromFile($fileName, $userId)
    {
        $content = file_get_contents($fileName);
        $content = mb_convert_encoding($content, 'UTF-8', 'UCS-2LE');
        $rows = ArrayUtil::trimExplode("\n", $content);
        foreach ($rows as $row) {
            $data = str_getcsv($row, ',', '"', '""');
            if (count($data) !== 4) {
                throw new WrongFileFormatException(
                    $this->translator->trans('This file seems not to be a field notes file.')
                );
            }

            if (!array_key_exists($data[2], self::LOG_TYPE)) {
                $this->addError(
                    $this->translator->trans('Log type "%type%" is not implemented', ['%type%' => $data[2]])
                );
                continue;
            }
            $type = self::LOG_TYPE[$data[2]];
            
            $geocache = $this->entityManager->getRepository('AppBundle:Geocache')->findOneBy(['wpOc' => $data[0]]);
            if (!$geocache) {
                $this->addError(
                    $this->translator->trans('Geocache "%code%" not found', ['%code%' => $data[0]])
                );
                continue;
            }

            $date = DateTime::createFromFormat(
                self::FIELD_NOTE_DATETIME_FORMAT,
                $data[1],
                new DateTimeZone('UTC')
            );

            $fieldNote = new FieldNote();
            $fieldNote->setUser($this->entityManager->getReference('AppBundle:User', $userId));
            $fieldNote->setGeocache($geocache);
            $fieldNote->setDate($date);
            $fieldNote->setType($type);
            $fieldNote->setText($data[3]);
            $this->entityManager->persist($fieldNote);
        }
        $this->entityManager->flush();

        if ($this->hasErrors()) {
            return false;
        }

        return true;
    }
}
