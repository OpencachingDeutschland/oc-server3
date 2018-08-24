<?php

use OcTest\Modules\AbstractModuleTest;

class PicturesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new PicturesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->uuid = md5(time());
        $entity->node = mt_rand(0, 100);
        $entity->url = md5(time());
        $entity->title = md5(time());
        $entity->objectId = mt_rand(0, 100);
        $entity->objectType = mt_rand(0, 100);
        $entity->thumbUrl = md5(time());
        $entity->spoiler = mt_rand(0, 100);
        $entity->local = mt_rand(0, 100);
        $entity->unknownFormat = mt_rand(0, 100);
        $entity->display = mt_rand(0, 100);
        $entity->mappreview = mt_rand(0, 100);
        $newEntity = new PicturesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
