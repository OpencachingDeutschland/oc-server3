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
     * @param Request $request
     *
     * @return Response
     *
     * @Route("/api/geocache/getReports")
     */
    public function getReportsAction(Request $request)
    {
        //TODO: may 'api_secret' be 'secret'?
        if ($this->container->getParameter('api_secret') === 'ThisTokenIsNotSoSecretChangeIt') {
            return new JsonResponse(['please change your api_secret to a secure one!']);
        }

        if ($request->get('key') !== $this->container->getParameter('api_secret')) {
            return new JsonResponse([]);
        }

        /** @var Reports $reports */
        $reports = $this->container->get('oc.geo_cache.reports');
        $geoCachesArray = explode('|', $request->get('geocaches'));

        $geoCaches = $reports->getReportStatus($geoCachesArray);

        return new JsonResponse($geoCaches);
    }
}
