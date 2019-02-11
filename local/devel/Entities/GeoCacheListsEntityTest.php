<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheListsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheListsEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->uuid = md5(time());
        $entity->node = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $entity->name = md5(time());
        $entity->isPublic = mt_rand(0, 100);
        $entity->description = md5(time());
        $entity->descHtmledit = mt_rand(0, 100);
        $entity->password = md5(time());
        $newEntity = new GeoCacheListsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
