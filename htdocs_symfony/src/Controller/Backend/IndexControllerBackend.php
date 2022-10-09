<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class IndexControllerBackend extends AbstractController
{
    /**
     * @Route("/", name="index_index")
     */
    public function index(): Response
    {
        return $this->render('backend/index/index.html.twig');
    }
}
