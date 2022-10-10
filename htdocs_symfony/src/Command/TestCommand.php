<?php

declare(strict_types=1);

namespace Oc\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Security;

class TestCommand extends Command
{

    protected static $defaultName = 'test';

    private RoleHierarchyInterface $roleHierarchy;

    private Security $security;

    public function __construct(RoleHierarchyInterface $roleHierarchy, Security $security)
    {
        parent::__construct();
        $this->roleHierarchy = $roleHierarchy;
        $this->security = $security;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        dd($this->security->isGranted('ROLE'));

        return 0;
    }

}
