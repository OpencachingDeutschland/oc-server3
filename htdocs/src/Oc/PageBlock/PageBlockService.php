<?php

namespace Oc\PageBlock;

use Oc\Util\DbalConnection;

class PageBlockService
{
    /**
     * @var DbalConnection
     */
    private $connection;

    public function __construct(DbalConnection $connection)
    {
        $this->connection = $connection;
    }


    public function fetchOneById($id)
    {

    }

    public function addStaticPage(PageEntity $entity)
    {

    }

    public function updateStaticPage(PageEntity $entity)
    {

    }
}
