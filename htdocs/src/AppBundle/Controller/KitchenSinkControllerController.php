<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class KitchenSinkControllerController extends Controller
{
    /**
     * @Route("/kitchensink")
     *
     * @return array
     */
    public function indexAction()
    {
        return $this->render('AppBundle:KitchenSinkController:index.html.twig', [
            // ...
        ]);
    }

}
