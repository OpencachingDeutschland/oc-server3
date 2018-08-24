<?php

use OcTest\Modules\AbstractModuleTest;

class SysconfigEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new SysconfigEntity();
        self::assertTrue($entity->isNew());
        $entity->name = md5(time());
        $entity->value = md5(time());
        $newEntity = new SysconfigEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
