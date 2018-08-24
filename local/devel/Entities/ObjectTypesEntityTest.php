<?php

use OcTest\Modules\AbstractModuleTest;

class ObjectTypesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new ObjectTypesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->name = md5(time());
        $newEntity = new ObjectTypesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
