<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserControllerBackend extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
