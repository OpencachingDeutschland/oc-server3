<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oc\Repository\AbstractEntity;
use Oc\Repository\CacheDescRepository;

#[ORM\Entity(repositoryClass: CacheDescRepository::class)]
#[ORM\Table(name: 'cache_desc')]
class GeoCacheDescEntity extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    public int $id = 0;

    #[ORM\Column(type: 'string', length: 36, unique: true)]
    public string $uuid = '';

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    public int $node = 0;

    #[ORM\Column(type: 'datetime')]
    public DateTime $dateCreated;

    #[ORM\Column(type: 'datetime')]
    public DateTime $lastModified;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    public int $cacheId = 0;

    #[ORM\Column(type: 'string', length: 2)]
    public string $language = '';

    #[ORM\Column(type: 'text')]
    public string $desc = '';

    #[ORM\Column(type: 'boolean')]
    public bool $descHtml = true;

    #[ORM\Column(type: 'boolean')]
    public bool $descHtmledit = true;

    #[ORM\Column(type: 'text')]
    public string $hint = '';

    #[ORM\Column(type: 'string', length: 120)]
    public string $shortDesc = '';

    #[ORM\Column(type: 'boolean')]
    public bool $descDarkUnsafe = false;

    public function __construct(array $data = [])
    {
        $this->dateCreated = new DateTime();
        $this->lastModified = new DateTime();

        if (!empty($data)) {
            $this->fromArray($data);
        }
    }

    public function isNew(): bool
    {
        return $this->id === 0;
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): static { $this->id = $id; return $this; }

    public function getUuid(): ?string { return $this->uuid; }
    public function setUuid(string $uuid): static { $this->uuid = $uuid; return $this; }

    public function getNode(): ?int { return $this->node; }
    public function setNode(int $node): static { $this->node = $node; return $this; }

    public function getDateCreated(): ?DateTime { return $this->dateCreated; }
    public function setDateCreated(DateTime $dateCreated): static { $this->dateCreated = $dateCreated; return $this; }

    public function getLastModified(): ?DateTime { return $this->lastModified; }
    public function setLastModified(DateTime $lastModified): static { $this->lastModified = $lastModified; return $this; }

    public function getCacheId(): ?int { return $this->cacheId; }
    public function setCacheId(int $cacheId): static { $this->cacheId = $cacheId; return $this; }

    public function getLanguage(): ?string { return $this->language; }
    public function setLanguage(string $language): static { $this->language = $language; return $this; }

    public function getDesc(): ?string { return $this->desc; }
    public function setDesc(string $desc): static { $this->desc = $desc; return $this; }

    public function isDescHtml(): ?bool { return $this->descHtml; }
    public function setDescHtml(bool $descHtml): static { $this->descHtml = $descHtml; return $this; }

    public function isDescHtmledit(): ?bool { return $this->descHtmledit; }
    public function setDescHtmledit(bool $descHtmledit): static { $this->descHtmledit = $descHtmledit; return $this; }

    public function getHint(): ?string { return $this->hint; }
    public function setHint(string $hint): static { $this->hint = $hint; return $this; }

    public function getShortDesc(): ?string { return $this->shortDesc; }
    public function setShortDesc(string $shortDesc): static { $this->shortDesc = $shortDesc; return $this; }

    public function isDescDarkUnsafe(): ?bool { return $this->descDarkUnsafe; }
    public function setDescDarkUnsafe(bool $descDarkUnsafe): static { $this->descDarkUnsafe = $descDarkUnsafe; return $this; }
}
