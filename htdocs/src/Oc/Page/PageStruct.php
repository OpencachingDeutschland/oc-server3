<?php

namespace Oc\Page;

use Oc\Page\Persistence\BlockEntity;
use Oc\Page\Persistence\PageEntity;

/**
 * Class PageStruct
 *
 * @package Oc\Page
 */
class PageStruct
{
    /**
     * @var PageEntity
     */
    private $pageEntity;

    /**
     * @var BlockEntity[]
     */
    private $blockEntities;

    /**
     * @var bool
     */
    private $isFallback;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $fallbackLocale;

    /**
     * PageStruct constructor.
     *
     * @param PageEntity $pageEntity
     * @param BlockEntity[] $blockEntities
     * @param string $locale
     * @param string $fallbackLocale
     * @param bool $isFallback
     */
    public function __construct(PageEntity $pageEntity, array $blockEntities, $locale, $fallbackLocale, $isFallback)
    {
        $this->pageEntity = $pageEntity;
        $this->blockEntities = $blockEntities;
        $this->isFallback = $isFallback;
        $this->locale = $locale;
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * @return PageEntity
     */
    public function getPageEntity()
    {
        return $this->pageEntity;
    }

    /**
     * @return BlockEntity[]
     */
    public function getBlockEntities()
    {
        return $this->blockEntities;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
    }

    /**
     * @return bool
     */
    public function isFallback()
    {
        return $this->isFallback;
    }
}
