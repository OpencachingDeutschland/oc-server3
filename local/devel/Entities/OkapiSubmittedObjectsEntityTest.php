<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiSubmittedObjectsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new OkapiSubmittedObjectsEntity();
        self::assertTrue($entity->isNew());
        $entity->objectType = mt_rand(0, 100);
        $entity->objectId = mt_rand(0, 100);
        $entity->consumerKey = md5(time());
        $newEntity = new OkapiSubmittedObjectsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
