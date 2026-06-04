<?php

declare(strict_types=1);

namespace Oc\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oc\Repository\AbstractEntity;
use Oc\Repository\WaypointsRepository;

#[ORM\Entity(repositoryClass: WaypointsRepository::class)]
#[ORM\Table(name: 'coordinates')]
class GeoCacheWaypointsEntity extends AbstractEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id = 0;

    #[ORM\Column(type: 'datetime')]
    public DateTime $dateCreated;

    #[ORM\Column(type: 'datetime')]
    public DateTime $lastModified;

    #[ORM\Column(type: 'integer')]
    public int $type = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $subtype = null;

    #[ORM\Column(type: 'float')]
    public float $latitude = 0.0;

    #[ORM\Column(type: 'float')]
    public float $longitude = 0.0;

    #[ORM\Column(type: 'integer')]
    public int $cacheId = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $userId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $logId = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $description = null;

    #[ORM\Column(type: 'string', length: 20)]
    public string $logpw = '';

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

    public function getDateCreated(): ?DateTime { return $this->dateCreated; }
    public function setDateCreated(DateTime $dateCreated): static { $this->dateCreated = $dateCreated; return $this; }

    public function getLastModified(): ?DateTime { return $this->lastModified; }
    public function setLastModified(DateTime $lastModified): static { $this->lastModified = $lastModified; return $this; }

    public function getType(): ?int { return $this->type; }
    public function setType(int $type): static { $this->type = $type; return $this; }

    public function getSubtype(): ?int { return $this->subtype; }
    public function setSubtype(?int $subtype): static { $this->subtype = $subtype; return $this; }

    public function getLatitude(): ?float { return $this->latitude; }
    public function setLatitude(float $latitude): static { $this->latitude = $latitude; return $this; }

    public function getLongitude(): ?float { return $this->longitude; }
    public function setLongitude(float $longitude): static { $this->longitude = $longitude; return $this; }

    public function getCacheId(): ?int { return $this->cacheId; }
    public function setCacheId(int $cacheId): static { $this->cacheId = $cacheId; return $this; }

    public function getUserId(): ?int { return $this->userId; }
    public function setUserId(?int $userId): static { $this->userId = $userId; return $this; }

    public function getLogId(): ?int { return $this->logId; }
    public function setLogId(?int $logId): static { $this->logId = $logId; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getLogpw(): ?string { return $this->logpw; }
    public function setLogpw(string $logpw): static { $this->logpw = $logpw; return $this; }
}
