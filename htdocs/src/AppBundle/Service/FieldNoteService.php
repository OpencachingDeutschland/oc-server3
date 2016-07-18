<?php

namespace AppBundle\Service;

use AppBundle\Entity\FieldNote;
use AppBundle\Exception\WrongFileFormatException;
use AppBundle\Service\Interfaces\FieldNoteServiceInterface;
use AppBundle\Service\Traits\ErrorTrait;
use AppBundle\Util\ArrayUtil;
use AppBundle\Util\DateUtil;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FieldNoteService implements FieldNoteServiceInterface
{
    use ErrorTrait;

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
     * @param null|\DateTime $ignoreBeforeDate
     *
     * @return bool
     * @throws \AppBundle\Exception\WrongFileFormatException
     */
    public function importFromFile($fileName, $userId, DateTime $ignoreBeforeDate = null)
    {
        $content = file_get_contents($fileName);
        $content = mb_convert_encoding($content, 'UTF-8', 'UCS-2LE');
        $rows = ArrayUtil::trimExplode("\n", $content);
        foreach ($rows as $row) {
            $data = str_getcsv($row, ',', '"', '""');
            if (count($data) !== 4) {
                throw new WrongFileFormatException(
                    $this->translator->trans('field_notes.error.wrong_file_format')
                );
            }

            $date = DateTime::createFromFormat(
                self::FIELD_NOTE_DATETIME_FORMAT,
                $data[1],
                new DateTimeZone('UTC')
            );

            if ($ignoreBeforeDate !== null && $date < $ignoreBeforeDate) {
                continue;
            }

            if (!array_key_exists($data[2], self::LOG_TYPE)) {
                $this->addError(
                    /** @Desc("Log type ""%type%"" is not implemented.") */
                    $this->translator->trans('field_notes.error.log_type_not_implemented', ['%type%' => $data[2]])
                );
                continue;
            }
            $type = self::LOG_TYPE[$data[2]];
            
            $geocache = $this->entityManager->getRepository('AppBundle:Geocache')->findOneBy(['wpOc' => $data[0]]);
            if (!$geocache) {
                $this->addError(
                    /** @Desc("Geocache ""%code%"" not found.") */
                    $this->translator->trans('field_notes.error.geocache_not_found', ['%code%' => $data[0]])
                );
                continue;
            }

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

    /**
     * @param int $userId
     *
     * @return \DateTime|null
     */
    public function getLatestFieldNoteOrLogDate($userId)
    {
        $maxFieldNote = $this->getMaxDateFromEntityByUserId('AppBundle:FieldNote', $userId);
        $maxLog = $this->getMaxDateFromEntityByUserId('AppBundle:GeocacheLog', $userId);

        return max($maxFieldNote, $maxLog);
    }

    /**
     * @param string $entityName
     * @param int $userId
     *
     * @return \DateTime|null
     */
    protected function getMaxDateFromEntityByUserId($entityName, $userId)
    {
        $max = null;
        $query = $this->entityManager->createQueryBuilder();
        $query
            ->select('MAX(e.date) AS max_date')
            ->from($entityName, 'e')
            ->where('e.user = :user_id')
                ->setParameter('user_id', $userId)
            ->setMaxResults(1);
        $result = $query->getQuery()->getResult();
        if ($result && isset($result[0]['max_date'])) {
            $max = DateUtil::dateTimeFromMySqlFormat($result[0]['max_date']);
        }

        return $max;
    }
}
