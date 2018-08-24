<?php

use OcTest\Modules\AbstractModuleTest;

class SearchDoublesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new SearchDoublesEntity();
        self::assertTrue($entity->isNew());
        $entity->hash = mt_rand(0, 100);
        $entity->word = md5(time());
        $entity->simple = md5(time());
        $newEntity = new SearchDoublesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
