<?php

use OcTest\Modules\AbstractModuleTest;

class XmlsessionEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new XmlsessionEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->users = mt_rand(0, 100);
        $entity->caches = mt_rand(0, 100);
        $entity->cachedescs = mt_rand(0, 100);
        $entity->cachelogs = mt_rand(0, 100);
        $entity->pictures = mt_rand(0, 100);
        $entity->removedobjects = mt_rand(0, 100);
        $entity->cleaned = mt_rand(0, 100);
        $entity->agent = md5(time());
        $newEntity = new XmlsessionEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
