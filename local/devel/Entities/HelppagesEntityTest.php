<?php

use OcTest\Modules\AbstractModuleTest;

class HelppagesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new HelppagesEntity();
        self::assertTrue($entity->isNew());
        $entity->ocpage = md5(time());
        $entity->language = md5(time());
        $entity->helppage = md5(time());
        $newEntity = new HelppagesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
