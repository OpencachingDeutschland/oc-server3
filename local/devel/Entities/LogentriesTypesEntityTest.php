<?php

use OcTest\Modules\AbstractModuleTest;

class LogentriesTypesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new LogentriesTypesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->module = md5(time());
        $entity->eventname = md5(time());
        $newEntity = new LogentriesTypesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
