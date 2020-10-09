<?php

declare(strict_types=1);

namespace Oc\Security;

use Oc\Repository\Exception\RecordNotFoundException;
use Oc\User\UserEntity;
use Oc\User\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function loadUserByUsername($username): UserInterface
    {
        try {
            return $this->userRepository->fetchOneByUsername($username);
        } catch (RecordNotFoundException $e) {
            throw new UsernameNotFoundException('User by username "' . $username . '" not found!', 0, $e);
        }
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserEntity) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->userRepository->fetchOneByUsername($user->getUsername());
    }

    public function supportsClass($class): bool
    {
        return UserEntity::class === $class || is_subclass_of($class, UserEntity::class);
    }
}
