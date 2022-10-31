<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Repository\CoordinatesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class CoordinatesControllerBackend
 *
 * @package Oc\Controller\Backend
 */
class CoordinatesControllerBackend extends AbstractController
{
    /**
     * @var CoordinatesRepository
     */
    private CoordinatesRepository $coordinatesRepository;

    /**
     * CoordinatesControllerBackend constructor.
     *
     * @param CoordinatesRepository $coordinatesRepository
     */
    public function __construct(CoordinatesRepository $coordinatesRepository)
    {
        $this->coordinatesRepository = $coordinatesRepository;
    }
}
