<?php

use OcTest\Modules\AbstractModuleTest;

class LogTypesTextEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new LogTypesTextEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->logTypesId = mt_rand(0, 100);
        $entity->lang = md5(time());
        $entity->textCombo = md5(time());
        $entity->textListing = md5(time());
        $newEntity = new LogTypesTextEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
