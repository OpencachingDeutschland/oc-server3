<?php

use OcTest\Modules\AbstractModuleTest;

class GnsSearchEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GnsSearchEntity();
        self::assertTrue($entity->isNew());
        $entity->uniId = mt_rand(0, 100);
        $entity->sort = md5(time());
        $entity->simple = md5(time());
        $entity->simplehash = mt_rand(0, 100);
        $newEntity = new GnsSearchEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
