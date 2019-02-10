<?php

use OcTest\Modules\AbstractModuleTest;

class SysTransRefEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new SysTransRefEntity();
        self::assertTrue($entity->isNew());
        $entity->transId = mt_rand(0, 100);
        $entity->resourceName = md5(time());
        $entity->line = mt_rand(0, 100);
        $newEntity = new SysTransRefEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
