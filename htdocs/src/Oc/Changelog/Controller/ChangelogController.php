<?php

namespace Oc\Changelog\Controller;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route(path: '/changelog', name: 'changelog_index')]
class ChangelogController extends AbstractController
{
    /**
     * @var CommonMarkConverter
     */
    private CommonMarkConverter $markConverter;

    /**
     * @var Environment
     */
    private Environment $twig;

    public function __construct(CommonMarkConverter $markConverter, Environment $twig)
    {
        $this->markConverter = $markConverter;
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws CommonMarkException
     */
    #[Route(path: '/changelog', name: 'changelog.index')]
    public function indexAction(): Response
    {
        $changelog = $this->markConverter
            ->convert(file_get_contents(__DIR__ . '/../../../../../ChangeLog-3.1.md'))->getContent();

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
