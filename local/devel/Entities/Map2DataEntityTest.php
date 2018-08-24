<?php

use OcTest\Modules\AbstractModuleTest;

class Map2DataEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new Map2DataEntity();
        self::assertTrue($entity->isNew());
        $entity->resultId = mt_rand(0, 100);
        $entity->cacheId = mt_rand(0, 100);
        $newEntity = new Map2DataEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
