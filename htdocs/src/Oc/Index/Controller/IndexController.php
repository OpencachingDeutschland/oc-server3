<?php

namespace Oc\Index\Controller;

use Oc\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '', name: 'index_index')]
class IndexController extends AbstractController
{
    #[Route(path: '', name: 'index.index')]
    public function indexAction(): Response
    {
        return new Response('test');
    }
}
