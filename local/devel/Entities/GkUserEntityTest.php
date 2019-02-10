<?php

use OcTest\Modules\AbstractModuleTest;

class GkUserEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GkUserEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->name = md5(time());
        $newEntity = new GkUserEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
