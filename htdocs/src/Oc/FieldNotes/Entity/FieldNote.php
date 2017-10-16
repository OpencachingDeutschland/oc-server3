<?php

namespace Oc\FieldNotes\Entity;

use Oc\GeoCache\Entity\Geocache;
use Oc\GeoCache\Entity\GeocacheLog;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oc\User\Entity\User;

/**
 * FieldNote
 *
 * @ORM\Table(name="field_note")
 * @ORM\Entity(repositoryClass="Oc\FieldNotes\Entity\FieldNoteRepository")
 */
class FieldNote
{
    const LOG_TYPE_FOUND = GeocacheLog::LOG_TYPE_FOUND;
    const LOG_TYPE_NOT_FOUND = GeocacheLog::LOG_TYPE_NOT_FOUND;
    const LOG_TYPE_NOTE = GeocacheLog::LOG_TYPE_NOTE;
    const LOG_TYPE_NEEDS_MAINTENANCE = 1000;
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="\Oc\User\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255, nullable=true)
     */
    private $text;

    /**
     * @var Geocache
     *
     * @ORM\ManyToOne(targetEntity="\Oc\GeoCache\Entity\Geocache")
     * @ORM\JoinColumn(name="geocache_id", referencedColumnName="cache_id", onDelete="CASCADE")
     */
    private $geocache;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return FieldNote
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return FieldNote
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return FieldNote
     */
    public function setText($text)
    {
        $text = str_replace("\n", '<br/>', $text);
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set user
     *
     * @param User|null $user
     *
     * @return FieldNote
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set geocache
     *
     * @param Geocache|null $geocache
     *
     * @return FieldNote
     */
    public function setGeocache(Geocache $geocache = null)
    {
        $this->geocache = $geocache;

        return $this;
    }

    /**
     * Get geocache
     *
     * @return Geocache
     */
    public function getGeocache()
    {
        return $this->geocache;
    }
}
