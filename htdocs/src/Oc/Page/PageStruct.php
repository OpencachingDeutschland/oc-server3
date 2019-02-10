<?php

namespace Oc\Page;

use Oc\Page\Persistence\BlockEntity;
use Oc\Page\Persistence\PageEntity;

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
     * @param BlockEntity[] $blockEntities
     */
    public function __construct(PageEntity $pageEntity, array $blockEntities, string $locale, string $fallbackLocale, bool $isFallback)
    {
        $this->pageEntity = $pageEntity;
        $this->blockEntities = $blockEntities;
        $this->isFallback = $isFallback;
        $this->locale = $locale;
        $this->fallbackLocale = $fallbackLocale;
    }

    public function getPageEntity(): PageEntity
    {
        return $this->pageEntity;
    }

    /**
     * @return BlockEntity[]
     */
    public function getBlockEntities(): array
    {
        return $this->blockEntities;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getFallbackLocale(): string
    {
        return $this->fallbackLocale;
    }

    public function isFallback(): bool
    {
        return $this->isFallback;
    }
}
