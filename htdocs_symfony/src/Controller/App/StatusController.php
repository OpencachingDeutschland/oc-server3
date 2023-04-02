<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Composer\InstalledVersions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatusController extends AbstractController
{
    /**
     * @Route("/status", name="status_index")
     * @Security("is_granted('ROLE_DEVELOPER_CORE')")
     */
    public function index(): Response
    {
        $composerInstalledPackages = InstalledVersions::getInstalledPackages();
        $composerInstalledPackagesAndVersions = [];

        foreach ($composerInstalledPackages as $packageName) {
            $composerInstalledPackagesAndVersions[] = ['pkgName' => $packageName, 'pkgVersion' => InstalledVersions::getVersion($packageName)];
        }
        return $this->render(
                'app/index/status.html.twig',
                ['composerInstalledPackages' => $composerInstalledPackagesAndVersions, 'serverInfo' => $_SERVER]
        );
    }
}