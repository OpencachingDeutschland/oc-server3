<?php

use OcTest\Modules\AbstractModuleTest;

class StatpicsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new StatpicsEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->tplpath = md5(time());
        $entity->previewpath = md5(time());
        $entity->description = md5(time());
        $entity->transId = mt_rand(0, 100);
        $entity->maxtextwidth = mt_rand(0, 100);
        $newEntity = new StatpicsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
