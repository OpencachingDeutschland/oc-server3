<?php

use OcTest\Modules\AbstractModuleTest;

class Map2ResultEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new Map2ResultEntity();
        self::assertTrue($entity->isNew());
        $entity->resultId = mt_rand(0, 100);
        $entity->slaveId = mt_rand(0, 100);
        $entity->sqlchecksum = mt_rand(0, 100);
        $entity->sqlquery = md5(time());
        $entity->sharedCounter = mt_rand(0, 100);
        $entity->requestCounter = mt_rand(0, 100);
        $newEntity = new Map2ResultEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
