<?php

use OcTest\Modules\AbstractModuleTest;

class NpaTypesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new NpaTypesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = md5(time());
        $entity->name = md5(time());
        $entity->ordinal = mt_rand(0, 100);
        $entity->noWarning = mt_rand(0, 100);
        $newEntity = new NpaTypesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
