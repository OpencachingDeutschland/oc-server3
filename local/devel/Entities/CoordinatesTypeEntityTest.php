<?php

use OcTest\Modules\AbstractModuleTest;

class CoordinatesTypeEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new CoordinatesTypeEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->name = md5(time());
        $entity->transId = mt_rand(0, 100);
        $entity->image = md5(time());
        $entity->preposition = md5(time());
        $entity->ppTransId = mt_rand(0, 100);
        $newEntity = new CoordinatesTypeEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
