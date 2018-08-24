<?php

use OcTest\Modules\AbstractModuleTest;

class FieldNoteEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new FieldNoteEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $entity->geocacheId = mt_rand(0, 100);
        $entity->text = md5(time());
        $newEntity = new FieldNoteEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
