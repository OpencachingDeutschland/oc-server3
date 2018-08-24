<?php

use OcTest\Modules\AbstractModuleTest;

class UserDelegatesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new UserDelegatesEntity();
        self::assertTrue($entity->isNew());
        $entity->userId = mt_rand(0, 100);
        $entity->node = mt_rand(0, 100);
        $newEntity = new UserDelegatesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
