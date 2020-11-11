<?php

use OcTest\Modules\AbstractModuleTest;

class SysReplTimestampEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new SysReplTimestampEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $newEntity = new SysReplTimestampEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
