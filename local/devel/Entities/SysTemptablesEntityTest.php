<?php

use OcTest\Modules\AbstractModuleTest;

class SysTemptablesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new SysTemptablesEntity();
        self::assertTrue($entity->isNew());
        $entity->threadid = mt_rand(0, 100);
        $entity->name = md5(time());
        $newEntity = new SysTemptablesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
