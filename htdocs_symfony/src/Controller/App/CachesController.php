<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Oc\Form\CachesFormType;
use Oc\Repository\CachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CachesController extends AbstractController
{
    private Connection $connection;

    private CachesRepository $cachesRepository;

    public function __construct(Connection $connection, CachesRepository $cachesRepository)
    {
        $this->connection = $connection;
        $this->cachesRepository = $cachesRepository;
    }

    /**
     * @throws Exception
     * @Route("/caches", name="caches_index")
     */
    public function cachesController_index(Request $request): Response
    {
        $fetchedCaches = '';

        // create input field for caches_by_searchfield
        $form = $this->createForm(CachesFormType::class);

        // see: https://symfonycasts.com/screencast/symfony-forms/form-submit
        // handles the request (submit-button of the form), but only if there is a POST request
        $form->handleRequest($request);
        // if is true only if there is a request submitted and it is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // read content of form input field
            $inputData = $form->getData();

            // send request to DB
            $fetchedCaches = $this->cachesRepository->getCachesForSearchField($inputData['content_searchfield']);
        }

        return $this->render(
                'app/caches/basicview.html.twig', [
                        'cachesForm' => $form->createView(),
                        'caches_by_searchfield' => $fetchedCaches
                ]
        );
    }

    /**
     * @Route("/cache/{wpID}", name="cache_by_wp_oc_gc")
     */
    public function search_by_cache_wp(string $wpID): Response
    {
        $fetchedCaches = $this->cachesRepository->search_by_cache_wp($wpID);

        return $this->render('app/caches/detailview.html.twig', ['cache_by_id' => $fetchedCaches]
        ); //+ status_not_found + abfragen in twig, Z.B.
    }
}