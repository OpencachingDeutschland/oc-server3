<?php

use OcTest\Modules\AbstractModuleTest;

class SysTransEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new SysTransEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->text = md5(time());
        $newEntity = new SysTransEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
