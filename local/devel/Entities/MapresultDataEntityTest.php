<?php

use OcTest\Modules\AbstractModuleTest;

class MapresultDataEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new MapresultDataEntity();
        self::assertTrue($entity->isNew());
        $entity->queryId = mt_rand(0, 100);
        $entity->cacheId = mt_rand(0, 100);
        $newEntity = new MapresultDataEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
