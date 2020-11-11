<?php

class GkMoveEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $id;

    /** @var int */
    public $itemid;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    /** @var DateTime */
    public $datemoved;

    /** @var DateTime */
    public $datelogged;

    /** @var int */
    public $userid;

    /** @var string */
    public $comment;

    /** @var int */
    public $logtypeid;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->id === null;
    }
}
