<?php

namespace Oc\GeoCache\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oc\User\Entity\User;

/**
 * CacheLogs
 *
 * @ORM\Table(name="cache_logs", uniqueConstraints={@ORM\UniqueConstraint(name="uuid", columns={"uuid"})}, indexes={@ORM\Index(name="owner_notified", columns={"owner_notified"}), @ORM\Index(name="last_modified", columns={"last_modified"}), @ORM\Index(name="type", columns={"type", "cache_id"}), @ORM\Index(name="date_created", columns={"date_created"}), @ORM\Index(name="user_id", columns={"user_id", "cache_id"}), @ORM\Index(name="cache_id", columns={"cache_id", "user_id"}), @ORM\Index(name="date", columns={"cache_id", "date", "date_created"}), @ORM\Index(name="order_date", columns={"cache_id", "order_date", "date_created", "id"})})
 * @ORM\Entity
 */
class GeocacheLog
{
    const LOG_TYPE_FOUND = 1;
    const LOG_TYPE_NOT_FOUND = 2;
    const LOG_TYPE_NOTE = 3;
    const LOG_TYPE_ATTENDED = 7;

    const NEEDS_MAINTENANCE_ACTIVATE = 2;
    const NEEDS_MAINTENANCE_DEACTIVATE = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=false)
     */
    private $uuid;

    /**
     * @var int
     *
     * @ORM\Column(name="node", type="integer", nullable=false)
     */
    private $node = 0;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="entry_last_modified", type="datetime", nullable=false)
     */
    private $entryLastModified;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="last_modified", type="datetime", nullable=false)
     */
    private $lastModified;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="log_last_modified", type="datetime", nullable=false)
     */
    private $logLastModified;

    /**
     * @var integer
     *
     * @ORM\Column(name="cache_id", type="integer", nullable=false)
     */
    private $cacheId;

    /**
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="\Oc\User\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="oc_team_comment", type="boolean", nullable=false)
     */
    private $ocTeamComment = false;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="order_date", type="datetime", nullable=false)
     */
    private $orderDate;

    /**
     * @var int
     *
     * @ORM\Column(name="needs_maintenance", type="integer", nullable=false)
     */
    private $needsMaintenance = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="listing_outdated", type="integer", nullable=false)
     */
    private $listingOutdated = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var bool
     *
     * @ORM\Column(name="text_html", type="boolean", nullable=false)
     */
    private $textHtml = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="text_htmledit", type="boolean", nullable=false)
     */
    private $textHtmledit = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="owner_notified", type="boolean", nullable=false)
     */
    private $ownerNotified = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="picture", type="smallint", nullable=false)
     */
    private $picture;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return GeocacheLog
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set node
     *
     * @param int $node
     *
     * @return GeocacheLog
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return bool
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set dateCreated
     *
     * @param DateTime $dateCreated
     *
     * @return GeocacheLog
     */
    public function setDateCreated(DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set entryLastModified
     *
     * @param DateTime $entryLastModified
     *
     * @return GeocacheLog
     */
    public function setEntryLastModified(DateTime $entryLastModified)
    {
        $this->entryLastModified = $entryLastModified;

        return $this;
    }

    /**
     * Get entryLastModified
     *
     * @return DateTime
     */
    public function getEntryLastModified()
    {
        return $this->entryLastModified;
    }

    /**
     * Set lastModified
     *
     * @param DateTime $lastModified
     *
     * @return GeocacheLog
     */
    public function setLastModified(DateTime $lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set logLastModified
     *
     * @param DateTime $logLastModified
     *
     * @return GeocacheLog
     */
    public function setLogLastModified(DateTime $logLastModified)
    {
        $this->logLastModified = $logLastModified;

        return $this;
    }

    /**
     * Get logLastModified
     *
     * @return DateTime
     */
    public function getLogLastModified()
    {
        return $this->logLastModified;
    }

    /**
     * Set cacheId
     *
     * @param int $cacheId
     *
     * @return GeocacheLog
     */
    public function setCacheId($cacheId)
    {
        $this->cacheId = $cacheId;

        return $this;
    }

    /**
     * Get cacheId
     *
     * @return int
     */
    public function getCacheId()
    {
        return $this->cacheId;
    }

    /**
     * Set type
     *
     * @param bool $type
     *
     * @return GeocacheLog
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return bool
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set ocTeamComment
     *
     * @param bool $ocTeamComment
     *
     * @return GeocacheLog
     */
    public function setOcTeamComment($ocTeamComment)
    {
        $this->ocTeamComment = $ocTeamComment;

        return $this;
    }

    /**
     * Get ocTeamComment
     *
     * @return bool
     */
    public function getOcTeamComment()
    {
        return $this->ocTeamComment;
    }

    /**
     * Set date
     *
     * @param DateTime $date
     *
     * @return GeocacheLog
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set orderDate
     *
     * @param DateTime $orderDate
     *
     * @return GeocacheLog
     */
    public function setOrderDate(DateTime $orderDate)
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    /**
     * Get orderDate
     *
     * @return DateTime
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * Set needsMaintenance
     *
     * @param int $needsMaintenance
     *
     * @return GeocacheLog
     */
    public function setNeedsMaintenance($needsMaintenance)
    {
        $this->needsMaintenance = $needsMaintenance;

        return $this;
    }

    /**
     * Get needsMaintenance
     *
     * @return bool
     */
    public function getNeedsMaintenance()
    {
        return $this->needsMaintenance;
    }

    /**
     * Set listingOutdated
     *
     * @param int $listingOutdated
     *
     * @return GeocacheLog
     */
    public function setListingOutdated($listingOutdated)
    {
        $this->listingOutdated = $listingOutdated;

        return $this;
    }

    /**
     * Get listingOutdated
     *
     * @return bool
     */
    public function getListingOutdated()
    {
        return $this->listingOutdated;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return GeocacheLog
     */
    public function setText($text)
    {
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
     * Set textHtml
     *
     * @param bool $textHtml
     *
     * @return GeocacheLog
     */
    public function setTextHtml($textHtml)
    {
        $this->textHtml = $textHtml;

        return $this;
    }

    /**
     * Get textHtml
     *
     * @return bool
     */
    public function getTextHtml()
    {
        return $this->textHtml;
    }

    /**
     * Set textHtmledit
     *
     * @param bool $textHtmledit
     *
     * @return GeocacheLog
     */
    public function setTextHtmledit($textHtmledit)
    {
        $this->textHtmledit = $textHtmledit;

        return $this;
    }

    /**
     * Get textHtmledit
     *
     * @return bool
     */
    public function getTextHtmledit()
    {
        return $this->textHtmledit;
    }

    /**
     * Set ownerNotified
     *
     * @param bool $ownerNotified
     *
     * @return GeocacheLog
     */
    public function setOwnerNotified($ownerNotified)
    {
        $this->ownerNotified = $ownerNotified;

        return $this;
    }

    /**
     * Get ownerNotified
     *
     * @return bool
     */
    public function getOwnerNotified()
    {
        return $this->ownerNotified;
    }

    /**
     * Set picture
     *
     * @param int $picture
     *
     * @return GeocacheLog
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return int
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
