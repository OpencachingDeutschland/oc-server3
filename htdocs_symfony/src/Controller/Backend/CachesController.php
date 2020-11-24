<?php

declare(strict_types=1);

namespace Oc\Controller\Backend;

use Oc\Entity\CachesEntity;
use Oc\Repository\CachesRepository;
use Oc\Repository\SecurityRolesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CachesController extends AbstractController
{
    /**
     * @Route("/caches", name="caches_index")
     */
    public function index(): Response
    {
        $cachesx = "4";
        
        

//        $cachesy = new CachesRepository($sedd, $secc);
//        $cachesy = $cachesRepo->fetchOneById(1);

//        return $this->render('backend/caches/index.html.twig', [array(]'cachesx' => $caches]);

// ---
        
//        $this->denyAccessUnlessGranted('CAN_VIEW', CachesEntity::class);

        return $this->render('backend/caches/index.html.twig', ['cachesx' => $cachesx]);
    }

    /**
     * @Route("/caches", name="caches_list")
     */
    public function list(CachesRepository $cachesRepo)
    {
        $caches = $cachesRepo->findAll();

        return $this->render('backend/caches/index.html.twig', array('cachesx' => $caches));
    }
}
