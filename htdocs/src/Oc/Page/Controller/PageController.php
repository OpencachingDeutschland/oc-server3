<?php

namespace Oc\Page\Controller;

use Oc\AbstractController;
use Oc\Page\BlockService;
use Oc\Page\PageService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PageController
 */
class PageController extends AbstractController
{
    /**
     * @var PageService
     */
    private $pageService;
    /**
     * @var BlockService
     */
    private $blockService;

    /**
     * PageController constructor.
     *
     * @param PageService $pageService
     * @param BlockService $blockService
     */
    public function __construct(PageService $pageService, BlockService $blockService)
    {
        $this->pageService = $pageService;
        $this->blockService = $blockService;
    }

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

        $page = $this->pageService->fetchOneBy([
            'slug' => $slug,
            'active' => 1,
        ]);

        if (!$page) {
            throw $this->createNotFoundException();
        }

        $pageBlocks = $this->blockService->fetchBy([
            'page_id' => $page->id,
            'locale' => $this->getGlobalContext()->getLocale(),
            'active' => 1,
        ]);

        if (count($pageBlocks) === 0) {
            return $this->render('page/fallback.html.twig');
        }

        return $this->render('page/index.html.twig', [
            'page' => $page,
            'pageBlocks' => $pageBlocks,
        ]);
    }
}
