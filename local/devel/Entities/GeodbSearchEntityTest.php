<?php

use OcTest\Modules\AbstractModuleTest;

class GeodbSearchEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeodbSearchEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->locId = mt_rand(0, 100);
        $entity->sort = md5(time());
        $entity->simple = md5(time());
        $entity->simplehash = mt_rand(0, 100);
        $newEntity = new GeodbSearchEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
