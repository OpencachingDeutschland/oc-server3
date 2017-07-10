<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PageGroup;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class StaticPageController extends AbstractController
{
    /**
     * Index action to show given page by slug.
     *
     * @param string $slug
     *
     * @return Response
     *
     * @Route("/page/{slug}/", name="page")
     */
    public function indexAction($slug)
    {
        $slug = strtolower($slug);
        $this->setMenu(MNU_START);

        $repository = $this->getDoctrine()->getRepository('AppBundle:PageGroup');
        $pageBlocksQueryBuilder = $repository->getPageBlocksBySlugQueryBuilder(
            $slug,
            'pageGroup.active = 1 AND pageBlocks.active = 1'
        );

        /** @var PageGroup $pageGroup */
        $pageGroup = $pageBlocksQueryBuilder->getQuery()->getOneOrNullResult();

        if (!$pageGroup || $pageGroup->getPageBlocks()->count() === 0) {
            throw $this->createNotFoundException('Page not found.');
        }

        return $this->render('@App/Pages/index.html.twig', [
            'pageGroup' => $pageGroup
        ]);
    }

}
