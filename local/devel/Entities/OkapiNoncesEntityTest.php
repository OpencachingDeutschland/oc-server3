<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiNoncesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new OkapiNoncesEntity();
        self::assertTrue($entity->isNew());
        $entity->consumerKey = md5(time());
        $entity->nonceHash = md5(time());
        $entity->timestamp = mt_rand(0, 100);
        $newEntity = new OkapiNoncesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
