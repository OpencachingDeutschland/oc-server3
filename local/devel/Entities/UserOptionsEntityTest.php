<?php

use OcTest\Modules\AbstractModuleTest;

class UserOptionsEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new UserOptionsEntity();
        self::assertTrue($entity->isNew());
        $entity->userId = mt_rand(0, 100);
        $entity->optionId = mt_rand(0, 100);
        $entity->optionVisible = mt_rand(0, 100);
        $entity->optionValue = md5(time());
        $newEntity = new UserOptionsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
