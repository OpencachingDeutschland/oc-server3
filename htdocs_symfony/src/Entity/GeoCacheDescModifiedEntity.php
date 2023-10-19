<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class GeoCacheDescModifiedEntity extends AbstractEntity
{
    public int $cacheId = 0;

    public string $language;

    public DateTime $dateModified;

    public DateTime $dateCreated;

    public string $desc;

    public int $descHtml;

    public int $descHtmledit;

    public string $hint;

    public string $shortDesc;

    public int $restoredBy;

    public function isNew(): bool
    {
        return $this->cacheId === 0;
    }
}
