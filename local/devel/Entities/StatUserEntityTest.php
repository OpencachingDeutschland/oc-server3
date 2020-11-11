<?php

use OcTest\Modules\AbstractModuleTest;

class StatUserEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new StatUserEntity();
        self::assertTrue($entity->isNew());
        $entity->userId = mt_rand(0, 100);
        $newEntity = new StatUserEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
