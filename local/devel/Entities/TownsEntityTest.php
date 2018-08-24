<?php

use OcTest\Modules\AbstractModuleTest;

class TownsEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new TownsEntity();
        self::assertTrue($entity->isNew());
        $entity->country = md5(time());
        $entity->name = md5(time());
        $entity->transId = mt_rand(0, 100);
        $entity->maplist = mt_rand(0, 100);
        $newEntity = new TownsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
