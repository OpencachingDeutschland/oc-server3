<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCachesAttributesModifiedEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCachesAttributesModifiedEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $entity->attribId = mt_rand(0, 100);
        $entity->wasSet = mt_rand(0, 100);
        $entity->restoredBy = mt_rand(0, 100);
        $newEntity = new GeoCachesAttributesModifiedEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
