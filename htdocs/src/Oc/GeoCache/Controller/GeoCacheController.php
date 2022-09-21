<?php

namespace Oc\GeoCache\Controller;

use Doctrine\DBAL\Exception;
use Oc\GeoCache\Reports;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class GeoCacheController extends AbstractController
{
    /**
     * @var Reports
     */
    private Reports $reports;

    /**
     * @var string
     */
    private string $apiSecret;

    public function __construct(Reports $reports, string $apiSecret)
    {
        $this->reports = $reports;
        $this->apiSecret = $apiSecret;
    }

    /**
     * @Route("/api/geocache/getReports")
     * @throws Exception
     */
    public function getReportsAction(Request $request): Response
    {
        if ($this->apiSecret === 'ThisTokenIsNotSoSecretChangeIt') {
            return new JsonResponse(['please change your api_secret to a secure one!']);
        }

        if ($request->get('key') !== $this->apiSecret) {
            return new JsonResponse([]);
        }

        $geoCachesArray = explode('|', $request->get('geocaches'));

        $geoCaches = $this->reports->getReportStatus($geoCachesArray);

        return new JsonResponse($geoCaches);
    }
}
