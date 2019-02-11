<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiAuthorizationsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new OkapiAuthorizationsEntity();
        self::assertTrue($entity->isNew());
        $entity->consumerKey = md5(time());
        $entity->userId = mt_rand(0, 100);
        $newEntity = new OkapiAuthorizationsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
