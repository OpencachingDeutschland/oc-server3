<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheLocationEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCacheLocationEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $entity->adm1 = md5(time());
        $entity->adm2 = md5(time());
        $entity->adm3 = md5(time());
        $entity->adm4 = md5(time());
        $entity->code1 = md5(time());
        $entity->code2 = md5(time());
        $entity->code3 = md5(time());
        $entity->code4 = md5(time());
        $newEntity = new GeoCacheLocationEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
