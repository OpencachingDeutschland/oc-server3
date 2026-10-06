<?php

declare(strict_types=1);

namespace Oc\Controller\Backoffice;

use Oc\Repository\CoordinatesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CoordinatesControllerBackoffice extends AbstractController
{
    private CoordinatesRepository $coordinatesRepository;

    public function __construct(CoordinatesRepository $coordinatesRepository)
    {
        $this->coordinatesRepository = $coordinatesRepository;
    }
}
