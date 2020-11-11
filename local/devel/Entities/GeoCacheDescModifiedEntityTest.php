<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheDescModifiedEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheDescModifiedEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $entity->language = md5(time());
        $entity->desc = md5(time());
        $entity->descHtml = mt_rand(0, 100);
        $entity->descHtmledit = mt_rand(0, 100);
        $entity->hint = md5(time());
        $entity->shortDesc = md5(time());
        $entity->restoredBy = mt_rand(0, 100);
        $newEntity = new GeoCacheDescModifiedEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
