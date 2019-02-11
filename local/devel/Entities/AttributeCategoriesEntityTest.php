<?php

use OcTest\Modules\AbstractModuleTest;

class AttributeCategoriesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new AttributeCategoriesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->name = md5(time());
        $entity->transId = mt_rand(0, 100);
        $entity->color = md5(time());
        $newEntity = new AttributeCategoriesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
