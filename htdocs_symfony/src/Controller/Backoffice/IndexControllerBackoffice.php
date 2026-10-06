<?php

declare(strict_types=1);

namespace Oc\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexControllerBackoffice extends AbstractController
{
    #[Route("/", name: "index_index")]
    public function index(): Response
    {
        return $this->render('backoffice/index/index.html.twig');
    }
}
