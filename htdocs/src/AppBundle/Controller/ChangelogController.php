<?php

namespace AppBundle\Controller;

use League\CommonMark\CommonMarkConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChangelogController extends Controller
{
    /**
     * @Route("/changelog")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {   $converter = new CommonMarkConverter();
        $changelog = $converter->convertToHtml(file_get_contents(__DIR__ . '/../../../../ChangeLog-3.1.md'));

        return $this->render('AppBundle:ChangelogController:index.html.twig', ['changelog' => $changelog]);
    }
}
