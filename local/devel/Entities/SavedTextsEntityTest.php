<?php

use OcTest\Modules\AbstractModuleTest;

class SavedTextsEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new SavedTextsEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->objectType = mt_rand(0, 100);
        $entity->objectId = mt_rand(0, 100);
        $entity->subtype = mt_rand(0, 100);
        $entity->text = md5(time());
        $newEntity = new SavedTextsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
