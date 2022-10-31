<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\Form\CoordinatesFormType;
use Oc\Repository\CoordinatesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CoordinatesController
 *
 * @package Oc\Controller\App
 */
class CoordinatesController extends AbstractController
{
    /**
     * @var CoordinatesRepository
     */
    private CoordinatesRepository $coordinatesRepository;

    /**
     * CoordinatesControllerBackend constructor.
     *
     * @param CoordinatesRepository $coordinatesRepository
     */
    public function __construct(CoordinatesRepository $coordinatesRepository)
    {
        $this->coordinatesRepository = $coordinatesRepository;
    }

    /**
     * @param Request $request
     * @Route("/coordinates", name="coordinates_index")
     *
     * @return Response
     */
    public function coordinatesController_index(Request $request)
    : Response {
        $fetchedCoordinates = '';
        $form = $this->createForm(CoordinatesFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();

            $fetchedCoordinates = $this->coordinatesRepository->getCoordinatesForSearchField($inputData['content_coordinates_searchfield']);
        }

        return $this->render(
            'app/coordinates/index.html.twig', ['coordinatesForm' => $form->createView(), 'coordinates_by_searchfield' => $fetchedCoordinates]
        );
    }

    /**
     * @param string $lat
     * @param string $lon
     *
     * @return Response
     * @Route("/coordinate/{lat}+{lon}", name="coordinate_by_lat-lon")
     */
    public function convertCoordinates(string $lat, string $lon)
    : Response {
        return $this->render('app/coordinates/index.html.twig', ['converted_coordinates' => $this->coordinatesRepository->convertCoordinates($lat, $lon)]);
    }
}
