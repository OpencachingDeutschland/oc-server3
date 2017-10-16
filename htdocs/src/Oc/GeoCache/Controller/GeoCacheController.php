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
 *
 * @package Oc\GeoCache\Controller
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
    private $secret;

    /**
     * GeoCacheController constructor.
     *
     * @param Reports $reports
     * @param string $secret
     */
    public function __construct(Reports $reports, $secret)
    {
        $this->reports = $reports;
        $this->secret = $secret;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/api/geocache/getReports")
     */
    public function getReportsAction(Request $request)
    {
        if ($this->secret === 'ThisTokenIsNotSoSecretChangeIt' || $request->get('key') !== $this->secret) {
            return new JsonResponse([]);
        }

        $geoCachesArray = explode('|', $request->get('geocaches'));

        $geoCaches = $this->reports->getReportStatus($geoCachesArray);

        return new JsonResponse($geoCaches);
    }
}
