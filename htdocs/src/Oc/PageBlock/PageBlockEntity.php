<?php

namespace Oc\PageBlock;

class PageBlockEntity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $pageGroupId;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $html;

    /**
     * @var int
     */
    public $position;

    /**
     * @var \DateTime
     */
    public $updatedAt;

    /**
     * @var bool
     */
    public $active;

    /**
     * @return bool
     */
    public function isNew()
    {
        return !(bool) $this->id;
    }

    /**
     * @return array
     */
    public function toDatabaseArray()
    {
        return [
            'id' => (int) $this->id,
            'page_group_id' => (int) $this->pageGroupId,
            'title' => $this->title,
            'html' => $this->html,
            'position' => $this->position,
            'updated_at' => $this->updatedAt->format('Y-m-d h:i:s'),
            'active' => (int) $this->active,
        ];
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fromDatabaseArray(array $data)
    {
        $this->id = (int) $data['id'];
        $this->pageGroupId = (int) $data['page_group_id'];
        $this->title = $data['title'];
        $this->html = $data['html'];
        $this->position = $data['position'];
        $this->updatedAt = new \DateTime($data['updated_at']);
        $this->active = (bool) $data['active'];

        return $this;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }

            $this->{$key} = $value;
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
