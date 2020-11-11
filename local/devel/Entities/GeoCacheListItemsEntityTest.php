<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheListItemsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheListItemsEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheListId = mt_rand(0, 100);
        $entity->cacheId = mt_rand(0, 100);
        $newEntity = new GeoCacheListItemsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
