<?php

namespace AppBundle\Legacy\User;

use Symfony\Component\Security\Core\User\UserInterface;

class LegacyUser implements UserInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $userName;

    /**
     * LegacyUser constructor.
     *
     * @param int $id
     * @param string $userName
     */
    public function __construct($id, $userName)
    {
        $this->id       = $id;
        $this->userName = $userName;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array The user roles
     */
    public function getRoles()
    {
        if (!$this->id) {
            return [];
        }
        
        return ['ROLE_USER'];
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->userName;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     * @return void
     */
    public function eraseCredentials()
    {
    }

    /**
     * @param string $userName
     *
     * @return \AppBundle\Legacy\User\LegacyUser
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @param int $id
     *
     * @return \AppBundle\Legacy\User\LegacyUser
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
