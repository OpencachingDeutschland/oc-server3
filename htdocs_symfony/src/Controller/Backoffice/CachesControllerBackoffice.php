<?php

declare(strict_types=1);

namespace Oc\Controller\Backoffice;

use Oc\Repository\CachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CachesControllerBackoffice extends AbstractController
{
    private CachesRepository $cachesRepository;

    public function __construct(CachesRepository $cachesRepository)
    {
        $this->cachesRepository = $cachesRepository;
    }
}
