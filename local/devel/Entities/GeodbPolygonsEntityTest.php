<?php

use OcTest\Modules\AbstractModuleTest;

class GeodbPolygonsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeodbPolygonsEntity();
        self::assertTrue($entity->isNew());
        $entity->polygonId = mt_rand(0, 100);
        $entity->seqNo = mt_rand(0, 100);
        $newEntity = new GeodbPolygonsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
