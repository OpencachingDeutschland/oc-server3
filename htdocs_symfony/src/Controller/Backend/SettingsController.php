<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

//use Oc\Entity\CachesEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    /**
     * @Route("/settings", name="settings_index")
     */
    public function index(): Response
    {
        // $this->denyAccessUnlessGranted('CAN_VIEW', CachesEntity::class);

        return $this->render('backend/settings/index.html.twig');
    }
}
