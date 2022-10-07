<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Repository\MapsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class MapsControllerBackend
 *
 * @package Oc\Controller\Backend
 */
class MapsControllerBackend extends AbstractController
{
    /**
     * @var MapsRepository
     */
    private MapsRepository $mapsRepository;

    public function __construct(MapsRepository $mapsRepository)
    {
        $this->mapsRepository = $mapsRepository;
    }
}
