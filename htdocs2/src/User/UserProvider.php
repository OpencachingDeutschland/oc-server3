<?php

namespace Oc\User;

use Oc\Session\SessionDataInterface;

class UserProvider
{
    /**
     * @var SessionDataInterface
     */
    private $sessionData;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(SessionDataInterface $sessionData, UserService $userService)
    {
        $this->sessionData = $sessionData;
        $this->userService = $userService;
    }

    /**
     * Fetches the user by its session.
     *
     * @return UserEntity|null User entity or null if there is no userId in session or the user is not found
     */
    public function bySession(): ?UserEntity
    {
        if ($userId = $this->sessionData->get('userid')) {
            return $this->userService->fetchOneById($userId);
        }

        return null;
    }
}
