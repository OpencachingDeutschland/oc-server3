<?php

use OcTest\Modules\AbstractModuleTest;

class UserStatpicEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new UserStatpicEntity();
        self::assertTrue($entity->isNew());
        $entity->userId = mt_rand(0, 100);
        $entity->lang = md5(time());
        $newEntity = new UserStatpicEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
