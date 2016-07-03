<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Geocache
 *
 * @ORM\Table(name="caches", uniqueConstraints={@ORM\UniqueConstraint(name="uuid", columns={"uuid"}), @ORM\UniqueConstraint(name="wp_oc", columns={"wp_oc"})}, indexes={@ORM\Index(name="longitude", columns={"longitude", "latitude"}), @ORM\Index(name="date_created", columns={"date_created"}), @ORM\Index(name="latitude", columns={"latitude"}), @ORM\Index(name="country", columns={"country"}), @ORM\Index(name="status", columns={"status", "date_activate"}), @ORM\Index(name="last_modified", columns={"last_modified"}), @ORM\Index(name="wp_gc", columns={"wp_gc"}), @ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="date_activate", columns={"date_activate"}), @ORM\Index(name="need_npa_recalc", columns={"need_npa_recalc"}), @ORM\Index(name="type", columns={"type"}), @ORM\Index(name="size", columns={"size"}), @ORM\Index(name="difficulty", columns={"difficulty"}), @ORM\Index(name="terrain", columns={"terrain"}), @ORM\Index(name="wp_gc_maintained", columns={"wp_gc_maintained"})})
 * @ORM\Entity
 */
class Geocache
{
    const GEOCACHE_TYPE_UNKNOWN = 1;
    const GEOCACHE_TYPE_TRADITIONAL = 2;
    const GEOCACHE_TYPE_MULTI = 3;
    const GEOCACHE_TYPE_VIRTUAL = 4;
    const GEOCACHE_TYPE_WEBCAM = 5;
    const GEOCACHE_TYPE_EVENT = 6;
    const GEOCACHE_TYPE_QUIZ = 7;
    const GEOCACHE_TYPE_MATH = 8;
    const GEOCACHE_TYPE_MOVING = 9;
    const GEOCACHE_TYPE_DRIVE_IN = 10;

    const EVENT_CACHE_TYPES = [
        self::GEOCACHE_TYPE_EVENT,
    ];

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=false)
     */
    private $uuid;

    /**
     * @var int
     *
     * @ORM\Column(name="node", type="smallint", nullable=false)
     */
    private $node = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_publishdate", type="boolean", nullable=false)
     */
    private $isPublishdate = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_modified", type="datetime", nullable=false)
     */
    private $lastModified;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="listing_last_modified", type="datetime", nullable=false)
     */
    private $listingLastModified;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="meta_last_modified", type="datetime", nullable=false)
     */
    private $metaLastModified;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $longitude;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $latitude;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2, nullable=false)
     */
    private $country;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_hidden", type="date", nullable=false)
     */
    private $dateHidden;

    /**
     * @var bool
     *
     * @ORM\Column(name="size", type="boolean", nullable=false)
     */
    private $size;

    /**
     * @var bool
     *
     * @ORM\Column(name="difficulty", type="boolean", nullable=false)
     */
    private $difficulty;

    /**
     * @var bool
     *
     * @ORM\Column(name="terrain", type="boolean", nullable=false)
     */
    private $terrain;

    /**
     * @var string
     *
     * @ORM\Column(name="logpw", type="string", length=20, nullable=true)
     */
    private $logpw;

    /**
     * @var float
     *
     * @ORM\Column(name="search_time", type="float", precision=10, scale=0, nullable=false)
     */
    private $searchTime = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(name="way_length", type="float", precision=10, scale=0, nullable=false)
     */
    private $wayLength = 0.0;

    /**
     * @var string
     *
     * @ORM\Column(name="wp_gc", type="string", length=7, nullable=false)
     */
    private $wpGc;

    /**
     * @var string
     *
     * @ORM\Column(name="wp_gc_maintained", type="string", length=7, nullable=false)
     */
    private $wpGcMaintained;

    /**
     * @var string
     *
     * @ORM\Column(name="wp_nc", type="string", length=6, nullable=false)
     */
    private $wpNc;

    /**
     * @var string
     *
     * @ORM\Column(name="wp_oc", type="string", length=6, nullable=true)
     */
    private $wpOc;

    /**
     * @var string
     *
     * @ORM\Column(name="desc_languages", type="string", length=60, nullable=false)
     */
    private $descLanguages;

    /**
     * @var string
     *
     * @ORM\Column(name="default_desclang", type="string", length=2, nullable=false)
     */
    private $defaultDesclang;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_activate", type="datetime", nullable=true)
     */
    private $dateActivate;

    /**
     * @var bool
     *
     * @ORM\Column(name="need_npa_recalc", type="boolean", nullable=false)
     */
    private $needNpaRecalc;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_cachelists", type="boolean", nullable=false)
     */
    private $showCachelists = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="protect_old_coords", type="boolean", nullable=false)
     */
    private $protectOldCoords = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="needs_maintenance", type="boolean", nullable=false)
     */
    private $needsMaintenance = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="listing_outdated", type="boolean", nullable=false)
     */
    private $listingOutdated = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="flags_last_modified", type="datetime", nullable=false)
     */
    private $flagsLastModified;

    /**
     * @var integer
     *
     * @ORM\Column(name="cache_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cacheId;

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return \AppBundle\Entity\Geocache
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
     * @return \AppBundle\Entity\Geocache
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
     * @param \DateTime $dateCreated
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setDateCreated(DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set isPublishdate
     *
     * @param bool $isPublishdate
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setIsPublishdate($isPublishdate)
    {
        $this->isPublishdate = $isPublishdate;

        return $this;
    }

    /**
     * Get isPublishdate
     *
     * @return bool
     */
    public function getIsPublishdate()
    {
        return $this->isPublishdate;
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setLastModified(DateTime $lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set listingLastModified
     *
     * @param \DateTime $listingLastModified
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setListingLastModified(DateTime $listingLastModified)
    {
        $this->listingLastModified = $listingLastModified;

        return $this;
    }

    /**
     * Get listingLastModified
     *
     * @return \DateTime
     */
    public function getListingLastModified()
    {
        return $this->listingLastModified;
    }

    /**
     * Set metaLastModified
     *
     * @param \DateTime $metaLastModified
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setMetaLastModified(DateTime $metaLastModified)
    {
        $this->metaLastModified = $metaLastModified;

        return $this;
    }

    /**
     * Get metaLastModified
     *
     * @return \DateTime
     */
    public function getMetaLastModified()
    {
        return $this->metaLastModified;
    }

    /**
     * Set userId
     *
     * @param int $userId
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return \AppBundle\Entity\Geocache
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
     * Set status
     *
     * @param bool $status
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set dateHidden
     *
     * @param \DateTime $dateHidden
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setDateHidden(DateTime $dateHidden)
    {
        $this->dateHidden = $dateHidden;

        return $this;
    }

    /**
     * Get dateHidden
     *
     * @return \DateTime
     */
    public function getDateHidden()
    {
        return $this->dateHidden;
    }

    /**
     * Set size
     *
     * @param bool $size
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return bool
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set difficulty
     *
     * @param bool $difficulty
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /**
     * Get difficulty
     *
     * @return bool
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * Set terrain
     *
     * @param bool $terrain
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setTerrain($terrain)
    {
        $this->terrain = $terrain;

        return $this;
    }

    /**
     * Get terrain
     *
     * @return bool
     */
    public function getTerrain()
    {
        return $this->terrain;
    }

    /**
     * Set logpw
     *
     * @param string $logpw
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setLogpw($logpw)
    {
        $this->logpw = $logpw;

        return $this;
    }

    /**
     * Get logpw
     *
     * @return string
     */
    public function getLogpw()
    {
        return $this->logpw;
    }

    /**
     * Set searchTime
     *
     * @param float $searchTime
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setSearchTime($searchTime)
    {
        $this->searchTime = $searchTime;

        return $this;
    }

    /**
     * Get searchTime
     *
     * @return float
     */
    public function getSearchTime()
    {
        return $this->searchTime;
    }

    /**
     * Set wayLength
     *
     * @param float $wayLength
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setWayLength($wayLength)
    {
        $this->wayLength = $wayLength;

        return $this;
    }

    /**
     * Get wayLength
     *
     * @return float
     */
    public function getWayLength()
    {
        return $this->wayLength;
    }

    /**
     * Set wpGc
     *
     * @param string $wpGc
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setWpGc($wpGc)
    {
        $this->wpGc = $wpGc;

        return $this;
    }

    /**
     * Get wpGc
     *
     * @return string
     */
    public function getWpGc()
    {
        return $this->wpGc;
    }

    /**
     * Set wpGcMaintained
     *
     * @param string $wpGcMaintained
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setWpGcMaintained($wpGcMaintained)
    {
        $this->wpGcMaintained = $wpGcMaintained;

        return $this;
    }

    /**
     * Get wpGcMaintained
     *
     * @return string
     */
    public function getWpGcMaintained()
    {
        return $this->wpGcMaintained;
    }

    /**
     * Set wpNc
     *
     * @param string $wpNc
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setWpNc($wpNc)
    {
        $this->wpNc = $wpNc;

        return $this;
    }

    /**
     * Get wpNc
     *
     * @return string
     */
    public function getWpNc()
    {
        return $this->wpNc;
    }

    /**
     * Set wpOc
     *
     * @param string $wpOc
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setWpOc($wpOc)
    {
        $this->wpOc = $wpOc;

        return $this;
    }

    /**
     * Get wpOc
     *
     * @return string
     */
    public function getWpOc()
    {
        return $this->wpOc;
    }

    /**
     * Set descLanguages
     *
     * @param string $descLanguages
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setDescLanguages($descLanguages)
    {
        $this->descLanguages = $descLanguages;

        return $this;
    }

    /**
     * Get descLanguages
     *
     * @return string
     */
    public function getDescLanguages()
    {
        return $this->descLanguages;
    }

    /**
     * Set defaultDesclang
     *
     * @param string $defaultDesclang
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setDefaultDesclang($defaultDesclang)
    {
        $this->defaultDesclang = $defaultDesclang;

        return $this;
    }

    /**
     * Get defaultDesclang
     *
     * @return string
     */
    public function getDefaultDesclang()
    {
        return $this->defaultDesclang;
    }

    /**
     * Set dateActivate
     *
     * @param \DateTime $dateActivate
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setDateActivate(DateTime $dateActivate)
    {
        $this->dateActivate = $dateActivate;

        return $this;
    }

    /**
     * Get dateActivate
     *
     * @return \DateTime
     */
    public function getDateActivate()
    {
        return $this->dateActivate;
    }

    /**
     * Set needNpaRecalc
     *
     * @param bool $needNpaRecalc
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setNeedNpaRecalc($needNpaRecalc)
    {
        $this->needNpaRecalc = $needNpaRecalc;

        return $this;
    }

    /**
     * Get needNpaRecalc
     *
     * @return bool
     */
    public function getNeedNpaRecalc()
    {
        return $this->needNpaRecalc;
    }

    /**
     * Set showCachelists
     *
     * @param bool $showCachelists
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setShowCachelists($showCachelists)
    {
        $this->showCachelists = $showCachelists;

        return $this;
    }

    /**
     * Get showCachelists
     *
     * @return bool
     */
    public function getShowCachelists()
    {
        return $this->showCachelists;
    }

    /**
     * Set protectOldCoords
     *
     * @param bool $protectOldCoords
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setProtectOldCoords($protectOldCoords)
    {
        $this->protectOldCoords = $protectOldCoords;

        return $this;
    }

    /**
     * Get protectOldCoords
     *
     * @return bool
     */
    public function getProtectOldCoords()
    {
        return $this->protectOldCoords;
    }

    /**
     * Set needsMaintenance
     *
     * @param bool $needsMaintenance
     *
     * @return \AppBundle\Entity\Geocache
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
     * @param bool $listingOutdated
     *
     * @return \AppBundle\Entity\Geocache
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
     * Set flagsLastModified
     *
     * @param \DateTime $flagsLastModified
     *
     * @return \AppBundle\Entity\Geocache
     */
    public function setFlagsLastModified(DateTime $flagsLastModified)
    {
        $this->flagsLastModified = $flagsLastModified;

        return $this;
    }

    /**
     * Get flagsLastModified
     *
     * @return \DateTime
     */
    public function getFlagsLastModified()
    {
        return $this->flagsLastModified;
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
}
