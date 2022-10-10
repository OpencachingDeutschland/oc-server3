<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Repository\CoordinatesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CoordinatesControllerBackend extends AbstractController
{
    private CoordinatesRepository $coordinatesRepository;

    public function __construct(CoordinatesRepository $coordinatesRepository)
    {
        $this->coordinatesRepository = $coordinatesRepository;
    }
}
