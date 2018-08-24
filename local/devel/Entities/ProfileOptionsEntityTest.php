<?php

use OcTest\Modules\AbstractModuleTest;

class ProfileOptionsEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new ProfileOptionsEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->name = md5(time());
        $entity->transId = mt_rand(0, 100);
        $entity->internalUse = mt_rand(0, 100);
        $entity->defaultValue = md5(time());
        $entity->checkRegex = md5(time());
        $entity->optionOrder = mt_rand(0, 100);
        $entity->optionInput = md5(time());
        $entity->optionset = mt_rand(0, 100);
        $newEntity = new ProfileOptionsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
