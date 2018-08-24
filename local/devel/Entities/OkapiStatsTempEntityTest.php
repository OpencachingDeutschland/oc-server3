<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiStatsTempEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new OkapiStatsTempEntity();
        self::assertTrue($entity->isNew());
        $entity->consumerKey = md5(time());
        $entity->userId = mt_rand(0, 100);
        $entity->serviceName = md5(time());
        $newEntity = new OkapiStatsTempEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
