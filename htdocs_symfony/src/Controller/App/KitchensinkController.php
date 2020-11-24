<?php

namespace Oc\Controller\App;

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
       return $this->render('kitchensink/index.html.twig');
    }

    /**
     * @Route("/kitchensink/bs4", name="kitchensink_bs4")
     */
    public function style_bs4(): Response
    {
        return $this->render('kitchensink/kitchensink-bootstrap.html.twig');
    }

    /**
     * @Route("/kitchensink/oc4", name="kitchensink_oc4")
     */
    public function style_oc4(): Response
    {
        return $this->render('kitchensink/kitchensink-oc.html.twig');
    }
}
