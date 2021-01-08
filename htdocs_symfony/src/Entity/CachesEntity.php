<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Oc\Repository\CachesRepository")
 */
class CachesEntity extends AbstractEntity
{
    /** @var int */
    protected $cacheId;

    /** @var datetime */
    protected $dateCreated;

    /** @var datetime */
    protected $lastModified;

    /** @var int */
    protected $userId;

    /** @var string */
    protected $name;

    /** @var float */
    protected $longitude;

    /** @var float */
    protected $latitude;

    /** @var int */
    protected $status;

    /** @var string */
    protected $country;

    /** @var float */
    protected $difficulty;

    /** @var float */
    protected $terrain;

    /** @var int */
    protected $size;

    /** @var string */
    protected $wpGC;

    /** @var string */
    protected $wpOC;

    public function isNew()
    : bool
    {
        return $this->$cacheId === null;
    }

    public function isActiveAndFindable()
    : bool
    {
        if ($this->status == 1) {
            return $this->true;
        } else {
            return $this->false;
        }
    }

    public function getCacheId()
    : int
    {
        return $this->cacheId;
    }

    public function setCacheId($arg)
    {
        $this->cacheId = $arg;
    }

    public function getName()
    : string
    {
        return $this->name;
    }

    public function setName($arg)
    {
        $this->name = $arg;
    }

    public function getGCid()
    : string
    {
        return $this->wpGC;
    }

    /**
     * Set wpGC
     * @param string $arg
     */
    public function setGCid($arg)
    {
        $this->wpGC = $arg;
    }

    public function getOCid()
    : string
    {
        return $this->wpOC;
    }

    /**
     * Set wpOC
     * @param string $arg
     */
    public function setOCid($arg)
    {
        $this->wpOC = $arg;
    }

    public function getDifficulty()
    : string
    {
        return $this->difficulty;
    }

    public function getTerrain()
    : string
    {
        return $this->difficulty;
    }
}
