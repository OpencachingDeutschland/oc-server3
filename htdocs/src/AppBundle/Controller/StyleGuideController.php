<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StyleGuideController extends Controller
{
    /**
     * @Route("/styleguide")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:StyleGuide:index.html.twig', array(
            // ...
        ));
    }

}
