<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;
#use Symfony\Component\Security\Core\User\UserInterface; ??

#class CachesEntity extends AbstractEntity implements UserInterface // original kopierter Text von UserEntity..
class CachesEntity extends AbstractEntity implements CachesInterface
{

    /**
     * @var int
     */
    public $cache_id;

    /**
     * @var datetime
     */
    public $date_created;

    /**
     * @var datetime
     */
    public $last_modified;

    /**
     * @var int
     */
    public $user_id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var float (DB:double?)
     */
    public $longitude;

    /**
     * @var float (DB:double?)
     */
    public $latitude;

    /**
     * @var int
     */
    public $status;

    /**
     * @var string
     */
    public $country;

    /**
     * @var int
     */
    public $difficulty;

    /**
     * @var int
     */
    public $terrain;

    /**
     * @var int
     */
    public $size;

    /**
     * @var string (DB:varchar)
     */
    public $wp_oc;

    /**
     * @var string (DB:varchar)
     */
    public $wp_gc;

    /**
     * @var string (DB:varchar)
     */
    public $wp_nc;

    public function isNew(): bool
    {
        #Hinweis vom Scrutinizer: "There is one abstract method isNew in this class; you could implement it, or declare this class as abstract."
        // TODO: Implement isNew() method.
    }

    /**
     *
     */
    public function isActiveAndFindableAndSoOn(): bool
    {
        if ($this->status == 1)
            return $this->true;
        else
            return $this->false;
    }

    public function getID(): int
    {
        return $this->cache_id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGCid(): string
    {
        return $this->wp_gc;
    }

    public function getOCid(): string
    {
        return $this->wp_oc;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

/*
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {
    }
*/

}
