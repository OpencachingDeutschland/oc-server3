<?php

use OcTest\Modules\AbstractModuleTest;

class NotifyWaitingEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new NotifyWaitingEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->cacheId = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $entity->type = mt_rand(0, 100);
        $newEntity = new NotifyWaitingEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
