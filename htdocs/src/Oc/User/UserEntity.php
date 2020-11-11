<?php

namespace Oc\User;

use Oc\Repository\AbstractEntity;

class UserEntity extends AbstractEntity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $email;

    /**
     * @var float
     */
    public $latitude;

    /**
     * @var float
     */
    public $longitude;

    /**
     * @var bool
     */
    public $isActive;

    /**
     * @var string
     */
    public $firstname;

    /**
     * @var string
     */
    public $lastname;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $language;

    /**
     * Checks if the entity is new.
     */
    public function isNew(): bool
    {
        return $this->id === null;
    }
}
