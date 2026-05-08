<?php

namespace Oc\Import\Logs\Controller;

use Exception;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route(path: '/import/logs', name: 'importlogs_index')]
class ImportLogsController extends AbstractController
{
    /**
     * @var Environment
     */
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/import/logs')]
    public function indexAction(): Response
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 'on');

        $xmlContent = file_get_contents(__DIR__ . '/../../../../../../tests/fixtures/ImportLogs/geocaching.gpx');
        $xmlContent = str_replace(
            ['<groundspeak:', '</groundspeak:'],
            ['<', '</'],
            $xmlContent
        );
        $xml = new SimpleXMLElement($xmlContent);

        $response = new Response();
        $response->setContent(
            $this->twig->render(
                'importer/logs.html.twig',
                ['imports' => $xml]
            )
        );

        return $response;
    }
}
