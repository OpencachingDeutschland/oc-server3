<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index_index")
     */
    public function index()
    : Response
    {
        return $this->render('app/index/index.html.twig');
    }
}
