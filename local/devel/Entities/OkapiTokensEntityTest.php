<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiTokensEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new OkapiTokensEntity();
        self::assertTrue($entity->isNew());
        $entity->key = md5(time());
        $entity->secret = md5(time());
        $entity->timestamp = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $entity->consumerKey = md5(time());
        $entity->verifier = md5(time());
        $entity->callback = md5(time());
        $newEntity = new OkapiTokensEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
