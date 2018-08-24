<?php

use OcTest\Modules\AbstractModuleTest;

class SysTransTextEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new SysTransTextEntity();
        self::assertTrue($entity->isNew());
        $entity->transId = mt_rand(0, 100);
        $entity->lang = md5(time());
        $entity->text = md5(time());
        $newEntity = new SysTransTextEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
