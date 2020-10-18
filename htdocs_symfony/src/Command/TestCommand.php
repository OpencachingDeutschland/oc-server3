<?php

declare(strict_types=1);

namespace Oc\Command;

use Oc\Security\RoleHierarchyFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Security;

class TestCommand extends Command
{

    protected static $defaultName = 'test';

    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;
    /**
     * @var Security
     */
    private $security;

    public function __construct(RoleHierarchyInterface $roleHierarchy, Security $security)
    {
        parent::__construct();
        $this->roleHierarchy = $roleHierarchy;
        $this->security = $security;
    }

    protected function configure(): void
    {

    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        dd($this->security->isGranted('ROLE'));

        return 0;
    }

}
