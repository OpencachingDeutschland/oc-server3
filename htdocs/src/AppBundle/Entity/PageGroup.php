<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * PageGroup
 *
 * @ORM\Table(name="page_groups")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 */
class PageGroup
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keywords", type="string", length=255, nullable=true)
     */
    private $metaKeywords;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="string", length=255, nullable=true)
     */
    private $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_social", type="text", nullable=true)
     */
    private $metaSocial;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_changed", type="datetime")
     */
    private $lastChanged;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * One PageGroup has many PageBlocks.
     *
     * @var ArrayCollection
     *
     * @OneToMany(targetEntity="PageBlock", mappedBy="pageGroup")
     */
    private $pageBlocks;

    /**
     * PageGroup constructor.
     */
    public function __construct()
    {
        $this->pageBlocks = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return PageGroup
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set metaKeywords
     *
     * @param string $metaKeywords
     *
     * @return PageGroup
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return PageGroup
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set metaSocial
     *
     * @param string $metaSocial
     *
     * @return PageGroup
     */
    public function setMetaSocial($metaSocial)
    {
        $this->metaSocial = $metaSocial;

        return $this;
    }

    /**
     * Get metaSocial
     *
     * @return string
     */
    public function getMetaSocial()
    {
        return $this->metaSocial;
    }

    /**
     * Set lastChanged
     *
     * @param \DateTime $lastChanged
     *
     * @return PageGroup
     */
    public function setLastChanged($lastChanged)
    {
        $this->lastChanged = $lastChanged;

        return $this;
    }

    /**
     * Get lastChanged
     *
     * @return \DateTime
     */
    public function getLastChanged()
    {
        return $this->lastChanged;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return PageGroup
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return ArrayCollection
     */
    public function getPageBlocks()
    {
        return $this->pageBlocks;
    }

    /**
     * @param ArrayCollection $pageBlocks
     *
     * @return void
     */
    public function setPageBlocks(ArrayCollection $pageBlocks)
    {
        $this->pageBlocks = $pageBlocks;
    }
}

