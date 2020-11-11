<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiTileStatusEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new OkapiTileStatusEntity();
        self::assertTrue($entity->isNew());
        $entity->z = mt_rand(0, 100);
        $entity->x = mt_rand(0, 100);
        $entity->y = mt_rand(0, 100);
        $entity->status = mt_rand(0, 100);
        $newEntity = new OkapiTileStatusEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
