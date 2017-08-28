<?php

namespace AppBundle\Controller;

use Oc\GeoCache\Reports;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiGeocacheController extends Controller
{

    /**
     * @Route("/api/geocache/getReports")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getReportsAction(Request $request)
    {
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
