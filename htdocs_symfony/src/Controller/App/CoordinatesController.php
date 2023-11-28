<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Oc\Form\CoordinatesFormType;
use Oc\Form\SimpleForm;
use Oc\Repository\CoordinatesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoordinatesController extends AbstractController
{
    private CoordinatesRepository $coordinatesRepository;

    public function __construct(CoordinatesRepository $coordinatesRepository)
    {
        $this->coordinatesRepository = $coordinatesRepository;
    }

    /**
     * @Route("/coordinates", name="coordinates_index")
     */
    public function coordinatesController_index(Request $request): Response
    {
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
     * @Route("/coordinate/{lat}+{lon}", name="coordinate_by_lat-lon")
     */
    public function convertCoordinates(string $lat, string $lon): Response
    {
        return $this->render(
                'app/coordinates/index.html.twig',
                ['converted_coordinates' => $this->coordinatesRepository->convertCoordinates($lat, $lon)]
        );
    }

    /**
     * @Route("/coordinatesFormatIdentify", name="coordinates_format_identify")
     */
    public function coordinatesDetector(Request $request): Response
    {
        $matchedFormats = '';
        $form = $this->createForm(SimpleForm::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $inputData = $form->getData();

            $matchedFormats = $this->coordinatesRepository->identifyCoordinatesFormat($inputData['content_searchfield']);
        }

        return $this->render(
                'app/coordinates/coordinateMatcher.html.twig', [
                        'coordinatesForm' => $form->createView(),
                        'matched_coordinates_formats' => $matchedFormats
                ]
        );
    }

}
