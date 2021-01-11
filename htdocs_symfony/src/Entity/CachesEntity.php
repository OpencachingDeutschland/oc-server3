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
    protected $cache_id;

    /** @var datetime */
    protected $date_Created;

    /** @var datetime */
    protected $last_Modified;

    /** @var int */
    protected $user_Id;

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
    protected $wp_gc;

    /** @var string */
    protected $wp_oc;

    public function isNew()
    : bool
    {
        return $this->cache_id === null;
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
        return $this->cache_id;
    }

    public function setCacheId($arg)
    {
        $this->cache_id = $arg;
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

    public function getUserId()
    : string
    {
        return $this->user_Id;
    }

    public function setUserId($arg)
    {
        $this->user_Id = $arg;
    }

    public function getGCid()
    : string
    {
        return $this->wp_gc;
    }

    /**
     * Set wpGC
     * @param string $arg
     */
    public function setGCid($arg)
    {
        $this->wp_gc = $arg;
    }

    public function getOCid()
    : string
    {
        return $this->wp_oc;
    }

    /**
     * Set wpOC
     * @param string $arg
     */
    public function setOCid($arg)
    {
        $this->wp_oc = $arg;
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

    public function convertEntityToArray() : array
    {
        $entityArray = [];

        foreach ($this as $key => $value) {
            $entityArray = array_merge($entityArray, [$key => $value]);
        }

        $entityArrayX[0] = $entityArray;

        return $entityArrayX;
    }
}
