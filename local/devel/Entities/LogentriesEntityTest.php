<?php

use OcTest\Modules\AbstractModuleTest;

class LogentriesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new LogentriesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->module = md5(time());
        $entity->eventid = mt_rand(0, 100);
        $entity->userid = mt_rand(0, 100);
        $entity->objectid1 = mt_rand(0, 100);
        $entity->objectid2 = mt_rand(0, 100);
        $entity->logtext = md5(time());
        $entity->details = md5(time());
        $newEntity = new LogentriesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
