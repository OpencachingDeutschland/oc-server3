<?php

use OcTest\Modules\AbstractModuleTest;

class NutsLayerEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new NutsLayerEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->level = mt_rand(0, 100);
        $entity->code = md5(time());
        $newEntity = new NutsLayerEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
