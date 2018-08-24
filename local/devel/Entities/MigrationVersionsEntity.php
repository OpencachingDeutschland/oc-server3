<?php 

class MigrationVersionsEntity extends Oc\Repository\AbstractEntity
{
    /** @var string */
    public $version;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->version === null;
    }
}
