<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiCacheReadsEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new OkapiCacheReadsEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheKey = md5(time());
        $newEntity = new OkapiCacheReadsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
