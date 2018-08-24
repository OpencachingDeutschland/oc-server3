<?php

use OcTest\Modules\AbstractModuleTest;

class SearchIgnoreEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new SearchIgnoreEntity();
        self::assertTrue($entity->isNew());
        $entity->word = md5(time());
        $newEntity = new SearchIgnoreEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
