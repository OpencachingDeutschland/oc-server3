<?php

use OcTest\Modules\AbstractModuleTest;

class SearchIndexEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new SearchIndexEntity();
        self::assertTrue($entity->isNew());
        $entity->objectType = mt_rand(0, 100);
        $entity->cacheId = mt_rand(0, 100);
        $entity->hash = mt_rand(0, 100);
        $entity->count = mt_rand(0, 100);
        $newEntity = new SearchIndexEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
