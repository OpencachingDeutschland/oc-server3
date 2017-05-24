<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * PageBlock
 *
 * @ORM\Table(name="page_blocks")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 */
class PageBlock
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
     * @var int
     *
     * @ORM\Column(name="page_group_id", type="integer")
     */
    private $pageGroupId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="html", type="text")
     */
    private $html;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_changed", type="datetime")
     */
    private $lastChanged;

    /**
     * @var int
     *
     * @ORM\Column(name="active", type="integer")
     */
    private $active;

    /**
     * Many PageBlocks have one PageGroup.
     *
     * @var integer PageGroup
     *
     * @ManyToOne(targetEntity="PageGroup", inversedBy="pageBlocks")
     * @JoinColumn(name="page_group_id", referencedColumnName="id")
     */
    private $pageGroup;

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
     * Set pageGroupId
     *
     * @param integer $pageGroupId
     *
     * @return PageBlock
     */
    public function setPageGroupId($pageGroupId)
    {
        $this->pageGroupId = $pageGroupId;

        return $this;
    }

    /**
     * Get pageGroupId
     *
     * @return int
     */
    public function getPageGroupId()
    {
        return $this->pageGroupId;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return PageBlock
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set html
     *
     * @param string $html
     *
     * @return PageBlock
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Get html
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return PageBlock
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set lastChanged
     *
     * @param \DateTime $lastChanged
     *
     * @return PageBlock
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
     * @param integer $active
     *
     * @return PageBlock
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return int
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return PageGroup
     */
    public function getPageGroup()
    {
        return $this->pageGroup;
    }

    /**
     * @param PageGroup $pageGroup
     *
     * @return void
     */
    public function setPageGroup(PageGroup $pageGroup)
    {
        $this->pageGroup = $pageGroup;
    }
}
