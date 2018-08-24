<?php

use OcTest\Modules\AbstractModuleTest;

class SysReplExcludeEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new SysReplExcludeEntity();
        self::assertTrue($entity->isNew());
        $entity->userId = mt_rand(0, 100);
        $newEntity = new SysReplExcludeEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
