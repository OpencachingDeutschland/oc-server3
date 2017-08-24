<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PageGroup;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PageController
 *
 * @package AppBundle\Controller
 */
class PageController extends AbstractController
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

        $pageService = $this->get('oc.page.page_service');
        $blockService = $this->get('oc.page.block_service');

        $page = $pageService->fetchOneBy([
            'slug' => $slug,
            'active' => 1
        ]);

        if (!$page) {
            throw $this->createNotFoundException();
        }

        $pageBlocks = $blockService->fetchBy([
            'page_id' => $page->id,
            'locale' => $this->getGlobalContext()->getLocale(),
            'active' => 1
        ]);

        if (count($pageBlocks) === 0) {
            return $this->render('@App/Page/fallback.html.twig');
        }

        return $this->render('@App/Page/index.html.twig', [
            'page' => $page,
            'pageBlocks' => $pageBlocks
        ]);
    }



}
