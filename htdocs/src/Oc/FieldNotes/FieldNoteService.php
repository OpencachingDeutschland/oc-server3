<?php

namespace Oc\FieldNotes;

use Oc\FieldNotes\Entity\FieldNote;
use Oc\FieldNotes\Exception\WrongDateFormatException;
use Oc\FieldNotes\Exception\WrongFileFormatException;
use Oc\GeoCache\Entity\Geocache;
use Oc\GeoCache\Entity\GeocacheLog;
use Oc\User\Entity\User;
use Oc\Util\Error\ErrorTrait;
use Oc\Util\ArrayUtil;
use Oc\Util\DateUtil;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FieldNoteService implements FieldNoteServiceInterface
{
    use ErrorTrait;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * FieldNoteService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * @param string $fileName
     * @param int $userId
     * @param null|DateTime $ignoreBeforeDate
     *
     * @return bool
     * @throws WrongDateFormatException
     * @throws WrongFileFormatException
     */
    public function importFromFile($fileName, $userId, DateTime $ignoreBeforeDate = null)
    {
        $content = file_get_contents($fileName);
        $content = str_replace("\xFF\xFE", '', $content); // remove UTF16(LE) BOM
        $content = mb_convert_encoding($content, 'UTF-8', 'UCS-2LE');
        // unify line feeds
        $content = str_replace("\r\n", "\n", $content);
        $rows = ArrayUtil::explodeTrim("\"\n", $content);
        $notFoundGeocacheCodes = [];
        foreach ($rows as $row) {
            $data = str_getcsv($row, ',', '"', '""');
            if (count($data) !== 4) {
                throw new WrongFileFormatException(
                    $this->translator->trans('field_notes.error.wrong_file_format')
                );
            }

            $date = $this->getDate($data[1]);

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

            if (0 === stripos($data[0], 'GC')) {
                $query = $this->entityManager->createQueryBuilder()
                    ->select('g')
                    ->from(Geocache::class, 'g')
                    ->where("IFELSE(g.wpGcMaintained = '', g.wpGc, g.wpGcMaintained) = :code")
                    ->setParameter('code', $data[0])
                    ->getQuery();
                $geocache = $query->getOneOrNullResult();
            } else {
                $geocache = $this->entityManager->getRepository(Geocache::class)->findOneBy(['wpOc' => $data[0]]);
            }
            if (!$geocache) {
                $notFoundGeocacheCodes[] = $data[0];
                $this->addError(
                    /** @Desc("Geocache ""%code%"" not found.") */
                    $this->translator->transChoice(
                        'field_notes.error.geocache_not_found',
                        count($notFoundGeocacheCodes),
                        [
                            '%code%' => ArrayUtil::humanImplode(
                                $notFoundGeocacheCodes,
                                $this->translator->trans('array_util.human_lang_implode.and')
                            ),
                        ]
                    ),
                    'geocache-not-found'
                );
                continue;
            }

            $fieldNote = new FieldNote();
            $fieldNote->setUser($this->entityManager->getReference(User::class, $userId));
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
     * @return DateTime|null
     */
    public function getLatestFieldNoteOrLogDate($userId)
    {
        $maxFieldNote = $this->getMaxDateFromEntityByUserId(FieldNote::class, $userId);
        $maxLog = $this->getMaxDateFromEntityByUserId(GeocacheLog::class, $userId);

        return max($maxFieldNote, $maxLog);
    }

    /**
     * @param string $entityName
     * @param int $userId
     * @return DateTime|null
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

    /**
     * @param string $dateString
     * @throws WrongDateFormatException
     * @return DateTime
     */
    protected function getDate($dateString)
    {
        $format = null;
        if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}Z/', $dateString)) {
            $format = self::FIELD_NOTE_DATETIME_FORMAT_SHORT;
        } elseif (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}Z/', $dateString)) {
            $format = self::FIELD_NOTE_DATETIME_FORMAT;
        }

        if ($format === null) {
            throw new WrongDateFormatException(
                $this->translator->trans('field_notes.error.wrong_date_format')
            );
        }

        $date = DateTime::createFromFormat(
            $format,
            $dateString,
            new DateTimeZone('UTC')
        );
        $date->setTimezone(new DateTimeZone(date_default_timezone_get()));

        return $date;
    }
}
