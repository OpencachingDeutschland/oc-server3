<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass="Oc\Repository\CachesRepository")
 */
class CachesEntity extends AbstractEntity
{
    /** @var int */
    public $cache_id;

    /** @var datetime */
    public $date_created;

    /** @var datetime */
    public $last_modified;

    /** @var int */
    public $user_id;

    /** @var string */
    public $name;

    /** @var float */
    public $longitude;

    /** @var float */
    public $latitude;

    /** @var int */
    public $status;

    /** @var string */
    public $country;

    /** @var float */
    public $difficulty;

    /** @var float */
    public $terrain;

    /** @var int */
    public $size;

    /** @var string */
    public $wp_gc;

    /** @var string */
    public $wp_oc;

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
        return $this->cache_id;
    }

    public function getName()
    : string
    {
        return $this->name;
    }

    public function getGCid()
    : string
    {
        return $this->wp_gc;
    }

    public function getOCid()
    : string
    {
        return $this->wp_oc;
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
