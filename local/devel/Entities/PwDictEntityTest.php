<?php

use OcTest\Modules\AbstractModuleTest;

class PwDictEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new PwDictEntity();
        self::assertTrue($entity->isNew());
        $entity->pw = md5(time());
        $newEntity = new PwDictEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
