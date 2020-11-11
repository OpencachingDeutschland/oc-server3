<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheVisitsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheVisitsEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $entity->userIdIp = md5(time());
        $newEntity = new GeoCacheVisitsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
