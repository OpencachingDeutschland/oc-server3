<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Entity\UserEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KitchensinkController extends AbstractController
{
    /**
     * @Route("/kitchensink", name="kitchensink_index")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('CAN_VIEW', UserEntity::class);

        return $this->render('backend/kitchensink/index.html.twig');
    }
}
