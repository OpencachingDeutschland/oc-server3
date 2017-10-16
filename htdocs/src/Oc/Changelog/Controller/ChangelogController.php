<?php

namespace Oc\Changelog\Controller;

use League\CommonMark\CommonMarkConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;

/**
 * Class ChangelogController
 *
 * @package Oc\Changelog\Controller
 *
 * @Route(service="Oc\Changelog\Controller\ChangelogController")
 */
class ChangelogController extends Controller
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
     * @Route(path="/changelog")
     */
    public function indexAction()
    {
        $changelog = $this->markConverter->convertToHtml(file_get_contents(__DIR__ . '/../../../../../ChangeLog-3.1.md'));

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
