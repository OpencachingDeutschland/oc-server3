<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiSearchResultsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new OkapiSearchResultsEntity();
        self::assertTrue($entity->isNew());
        $entity->setId = mt_rand(0, 100);
        $entity->cacheId = mt_rand(0, 100);
        $newEntity = new OkapiSearchResultsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
