<?php

declare(strict_types=1);

namespace Oc\Entity;

/**
 * Simple data object for the `cache_desc` table.
 */
class GeoCacheDescEntity
{
    public int $id = 0;
    public string $uuid = '';
    public int $node = 0;
    public string $dateCreated = '';
    public string $lastModified = '';
    public int $cacheId = 0;
    public string $language = '';
    public string $desc = '';
    public bool $descHtml = true;
    public bool $descHtmledit = true;
    public string $hint = '';
    public string $shortDesc = '';
    public bool $descDarkUnsafe = false;
}
