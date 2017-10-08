<?php

namespace AppBundle\Controller;

use League\CommonMark\CommonMarkConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

/**
 * @Route(service="app.controller.changelog_controller")
 * @package AppBundle\Controller
 */
class ChangelogController
{
    /**
     * @var CommonMarkConverter
     */
    private $markConverter;

    /**
     * @var Twig_Environment
     */
    private $twig;

    public function __construct(CommonMarkConverter $markConverter, Twig_Environment $twig)
    {
        $this->markConverter = $markConverter;
        $this->twig = $twig;
    }

    /**
     * @Route("/changelog")
     */
    public function indexAction()
    {
        $changelog = $this->markConverter->convertToHtml(file_get_contents(__DIR__ . '/../../../../ChangeLog-3.1.md'));

        $response = new Response();
        $response->setContent(
            $this->twig->render(
                'changelog/index.html.twig',
                ['changelog' => $changelog]
            )
        );

        return $response;
    }
}
