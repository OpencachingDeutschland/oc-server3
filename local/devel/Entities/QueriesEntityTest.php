<?php

use OcTest\Modules\AbstractModuleTest;

class QueriesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new QueriesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $entity->name = md5(time());
        $entity->options = md5(time());
        $newEntity = new QueriesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
