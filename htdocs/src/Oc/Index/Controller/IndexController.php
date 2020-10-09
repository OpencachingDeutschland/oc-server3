<?php

namespace Oc\Index\Controller;

use Oc\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(service="Oc\Index\Controller\IndexController")
 */
class IndexController extends AbstractController
{
    /**
     * @Route(path="", name="index.index")
     */
    public function indexAction(): Response
    {
        return new Response('test');
    }
}
