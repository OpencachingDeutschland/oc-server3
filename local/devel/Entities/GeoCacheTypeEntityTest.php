<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheTypeEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCacheTypeEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->name = md5(time());
        $entity->transId = mt_rand(0, 100);
        $entity->ordinal = mt_rand(0, 100);
        $entity->short = md5(time());
        $entity->de = md5(time());
        $entity->en = md5(time());
        $entity->iconLarge = md5(time());
        $entity->short2 = md5(time());
        $entity->short2TransId = mt_rand(0, 100);
        $entity->kmlName = md5(time());
        $newEntity = new GeoCacheTypeEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
