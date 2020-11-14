<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Entity\CachesEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CachesController extends AbstractController
{
    /**
     * @Route("/caches", name="caches_index")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('CAN_VIEW', CachesEntity::class);

        return $this->render('backend/caches/index.html.twig');
    }
}
