<?php

namespace Oc\Page;

use Oc\GlobalContext\GlobalContext;
use Oc\Page\Exception\PageNotFoundException;
use Oc\Page\Exception\PageTranslationNotFoundException;
use Oc\Page\Persistence\BlockEntity;
use Oc\Page\Persistence\BlockService;
use Oc\Page\Persistence\PageEntity;
use Oc\Page\Persistence\PageService;

class PageProvider
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
     * @var GlobalContext
     */
    private $globalContext;

    public function __construct(PageService $pageService, BlockService $blockService, GlobalContext $globalContext)
    {
        $this->pageService = $pageService;
        $this->blockService = $blockService;
        $this->globalContext = $globalContext;
    }

    /**
     * Fetches the page by the slug.
     *
     * Page blocks are translated in the user language, if the translation does not exist the default language is used.
     * If the default language is also not available a exception is thrown.
     *
     * @throws PageNotFoundException Thrown if the page could not be found
     * @throws PageTranslationNotFoundException Thrown if no translation of the page could be found
     */
    public function getPageBySlug(string $slug): PageStruct
    {
        $slug = strtolower($slug);

        $page = $this->getPage($slug);

        $preferredLocale = $this->globalContext->getLocale();
        $defaultLocale = $this->globalContext->getDefaultLocale();

        $pageBlocks = $this->getPageBlocks(
            $page,
            $preferredLocale
        );

        $isFallback = false;

        $sameLocale = $preferredLocale === $defaultLocale;

        if (!$sameLocale && count($pageBlocks) === 0) {
            //Fetch fallback if blocks are empty
            $pageBlocks = $this->getPageBlocks(
                $page,
                $defaultLocale
            );

            $isFallback = true;
        }

        if (count($pageBlocks) === 0) {
            throw new PageTranslationNotFoundException('Translation for page "' . $slug . '" could not be found"');
        }

        return new PageStruct(
            $page,
            $pageBlocks,
            $preferredLocale,
            $defaultLocale,
            $isFallback
        );
    }

    /**
     * Fetches all page blocks by the given page and locale.
     *
     * @return BlockEntity[]
     */
    private function getPageBlocks(PageEntity $page, string $locale): array
    {
        return $this->blockService->fetchBy([
            'page_id' => $page->id,
            'locale' => $locale,
            'active' => 1,
        ]);
    }

    /**
     * Fetches the page by the given slug.
     *
     * @throws PageNotFoundException Thrown if the page could not be found
     */
    private function getPage(string $slug): PageEntity
    {
        $page = $this->pageService->fetchOneBy([
            'slug' => $slug,
            'active' => 1,
        ]);

        if ($page === null) {
            throw new PageNotFoundException('The page "' . $slug . '" could not be found"');
        }

        return $page;
    }
}
