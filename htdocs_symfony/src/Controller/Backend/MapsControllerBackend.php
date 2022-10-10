<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Repository\MapsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MapsControllerBackend extends AbstractController
{
    private MapsRepository $mapsRepository;

    public function __construct(MapsRepository $mapsRepository)
    {
        $this->mapsRepository = $mapsRepository;
    }
}
