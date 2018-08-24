<?php

use OcTest\Modules\AbstractModuleTest;

class LanguagesListDefaultEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new LanguagesListDefaultEntity();
        self::assertTrue($entity->isNew());
        $entity->lang = md5(time());
        $entity->show = md5(time());
        $newEntity = new LanguagesListDefaultEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
