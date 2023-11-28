<?php

declare(strict_types=1);

namespace Oc\Controller\App;

use Doctrine\DBAL\Exception;
use Oc\Form\CachesFormType;
use Oc\Repository\CachesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CachesController extends AbstractController
{
    private CachesRepository $cachesRepository;

    public function __construct(CachesRepository $cachesRepository)
    {
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
        // is true only if there is a request submitted, and it is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // read content of form input field
            $inputData = $form->getData();

            // send request to DB
            $fetchedCaches = $this->cachesRepository->getCachesForSearchField($inputData['content_searchfield']);

            // extra search round in case someone is too lazy and just inputs OC/GC waypoint without prefixed 'OC' or 'GC' (example: 100f instead of OC100F)
            if (empty($fetchedCaches)) {
                if (preg_match('/[a-zA-Z0-9]{3,5}/', $inputData['content_searchfield'])
                    && !str_starts_with($inputData['content_searchfield'], 'OC')
                    && !str_starts_with($inputData['content_searchfield'], 'GC')
                ) {
                    $fetchedCaches = $this->cachesRepository->getCachesForSearchFieldWPOnly($inputData['content_searchfield']);
                }
            }
        }

        return $this->render(
                'app/caches/search.html.twig', [
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
        $fetchedCache = $this->cachesRepository->search_by_cache_wp($wpID);

        return $this->render('app/caches/view_listing.html.twig', ['cache' => $fetchedCache]
        );
    }
}
