<?php

declare(strict_types=1);

namespace Oc\Entity;


class CountriesEntity
{
    public string $short = '';

    public string $name;

    public int $transId;

    public string $de;

    public string $en;

    public int $listDefaultDe;

    public string $sortDe;

    public int $listDefaultEn;

    public string $sortEn;

    public int $admDisplay2;

    public int $admDisplay3;

    public function isNew(): bool
    {
        return $this->short === '';
    }

    public function getShort(): ?string
    {
        return $this->short;
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

    public function getListDefaultDe(): ?int
    {
        return $this->listDefaultDe;
    }

    public function setListDefaultDe(int $listDefaultDe): static
    {
        $this->listDefaultDe = $listDefaultDe;

        return $this;
    }

    public function getSortDe(): ?string
    {
        return $this->sortDe;
    }

    public function setSortDe(string $sortDe): static
    {
        $this->sortDe = $sortDe;

        return $this;
    }

    public function getListDefaultEn(): ?int
    {
        return $this->listDefaultEn;
    }

    public function setListDefaultEn(int $listDefaultEn): static
    {
        $this->listDefaultEn = $listDefaultEn;

        return $this;
    }

    public function getSortEn(): ?string
    {
        return $this->sortEn;
    }

    public function setSortEn(string $sortEn): static
    {
        $this->sortEn = $sortEn;

        return $this;
    }

    public function getAdmDisplay2(): ?int
    {
        return $this->admDisplay2;
    }

    public function setAdmDisplay2(int $admDisplay2): static
    {
        $this->admDisplay2 = $admDisplay2;

        return $this;
    }

    public function getAdmDisplay3(): ?int
    {
        return $this->admDisplay3;
    }

    public function setAdmDisplay3(int $admDisplay3): static
    {
        $this->admDisplay3 = $admDisplay3;

        return $this;
    }
}
