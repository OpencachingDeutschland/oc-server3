<?php

use OcTest\Modules\AbstractModuleTest;

class GkMoveTypeEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GkMoveTypeEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->name = md5(time());
        $newEntity = new GkMoveTypeEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
