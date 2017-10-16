<?php

namespace OcDev\KitchenSink\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class KitchenSinkController extends Controller
{
    /**
     * @Route("/kitchensink")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('kitchensink/index.html.twig');
    }
}
