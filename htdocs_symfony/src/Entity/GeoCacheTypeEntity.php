<?php

declare(strict_types=1);

namespace Oc\Entity;


class GeoCacheTypeEntity
{
    public int $id = 0;

    public string $name;

    public int $transId;

    public int $ordinal;

    public string $short;

    public string $de;

    public string $en;

    public string $iconLarge;

    public string $short2;

    public int $short2TransId;

    public string $kmlName;

    public string $svgName;

    public function isNew(): bool
    {
        return $this->id === 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTransId(): ?int
    {
        return $this->transId;
    }

    public function setTransId(int $transId): static
    {
        $this->transId = $transId;

        return $this;
    }

    public function getOrdinal(): ?int
    {
        return $this->ordinal;
    }

    public function setOrdinal(int $ordinal): static
    {
        $this->ordinal = $ordinal;

        return $this;
    }

    public function getShort(): ?string
    {
        return $this->short;
    }

    public function setShort(string $short): static
    {
        $this->short = $short;

        return $this;
    }

    public function getDe(): ?string
    {
        return $this->de;
    }

    public function setDe(string $de): static
    {
        $this->de = $de;

        return $this;
    }

    public function getEn(): ?string
    {
        return $this->en;
    }

    public function setEn(string $en): static
    {
        $this->en = $en;

        return $this;
    }

    public function getIconLarge(): ?string
    {
        return $this->iconLarge;
    }

    public function setIconLarge(string $iconLarge): static
    {
        $this->iconLarge = $iconLarge;

        return $this;
    }

    public function getShort2(): ?string
    {
        return $this->short2;
    }

    public function setShort2(string $short2): static
    {
        $this->short2 = $short2;

        return $this;
    }

    public function getShort2TransId(): ?int
    {
        return $this->short2TransId;
    }

    public function setShort2TransId(int $short2TransId): static
    {
        $this->short2TransId = $short2TransId;

        return $this;
    }

    public function getKmlName(): ?string
    {
        return $this->kmlName;
    }

    public function setKmlName(string $kmlName): static
    {
        $this->kmlName = $kmlName;

        return $this;
    }

    public function getSvgName(): ?string
    {
        return $this->svgName;
    }

    public function setSvgName(string $svgName): static
    {
        $this->svgName = $svgName;

        return $this;
    }
}
