<?php

namespace Oc\GeoCache\Controller;

use Oc\GeoCache\Reports;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GeoCacheController
 */
class GeoCacheController extends Controller
{
    /**
     * @var Reports
     */
    private $reports;

    /**
     * @var
     */
    private $apiSecret;

    /**
     * GeoCacheController constructor.
     *
     * @param Reports $reports
     * @param string $apiSecret
     */
    public function __construct(Reports $reports, $apiSecret)
    {
        $this->reports = $reports;
        $this->apiSecret = $apiSecret;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/geocache/getReports")
     */
    public function getReportsAction(Request $request)
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
