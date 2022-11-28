<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Oc\Repository\AbstractEntity;

class PicturesEntity extends AbstractEntity
{
    public int $id = 0;

    public string $uuid;

    public int $node;

    public DateTime $dateCreated;

    public DateTime $lastModified;

    public string $url;

    public string $title;

    public DateTime $lastUrlCheck;

    public int $objectId;

    public int $objectType;

    public string $thumbUrl;

    public DateTime $thumbLastGenerated;

    public int $spoiler;

    public int $local;

    public int $unknownFormat;

    public int $display;

    public int $mappreview;

    public int $seq;

    public function isNew(): bool
    {
        return $this->id === 0;
    }
}
