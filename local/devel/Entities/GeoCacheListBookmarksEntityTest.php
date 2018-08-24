<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheListBookmarksEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCacheListBookmarksEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheListId = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $entity->password = md5(time());
        $newEntity = new GeoCacheListBookmarksEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
