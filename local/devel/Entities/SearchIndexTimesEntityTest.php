<?php

use OcTest\Modules\AbstractModuleTest;

class SearchIndexTimesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new SearchIndexTimesEntity();
        self::assertTrue($entity->isNew());
        $entity->objectType = mt_rand(0, 100);
        $entity->objectId = mt_rand(0, 100);
        $newEntity = new SearchIndexTimesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
