<?php

use OcTest\Modules\AbstractModuleTest;

class MapresultEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new MapresultEntity();
        self::assertTrue($entity->isNew());
        $entity->queryId = mt_rand(0, 100);
        $newEntity = new MapresultEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
