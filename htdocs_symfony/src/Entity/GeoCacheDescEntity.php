<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheDescEntity extends AbstractEntity
{
    public int $id;

    public string $uuid;

    public int $node;

    public DateTime $dateCreated;

    public DateTime $lastModified;

    public int $cacheId;

    public string $language;

    public string $desc;

    public int $descHtml;

    public int $descHtmledit;

    public string $hint;

    public string $shortDesc;

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
