<?php

use OcTest\Modules\AbstractModuleTest;

class WsTanEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new WsTanEntity();
        self::assertTrue($entity->isNew());
        $entity->session = md5(time());
        $entity->tan = md5(time());
        $newEntity = new WsTanEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
