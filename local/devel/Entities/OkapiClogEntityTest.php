<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiClogEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new OkapiClogEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->data = md5(time());
        $newEntity = new OkapiClogEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
