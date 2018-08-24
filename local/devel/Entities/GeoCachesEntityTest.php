<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCachesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCachesEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $entity->uuid = md5(time());
        $entity->node = mt_rand(0, 100);
        $entity->isPublishdate = mt_rand(0, 100);
        $entity->okapiSyncbase = md5(time());
        $entity->userId = mt_rand(0, 100);
        $entity->name = md5(time());
        $entity->type = mt_rand(0, 100);
        $entity->status = mt_rand(0, 100);
        $entity->country = md5(time());
        $entity->size = mt_rand(0, 100);
        $entity->difficulty = mt_rand(0, 100);
        $entity->terrain = mt_rand(0, 100);
        $entity->logpw = md5(time());
        $entity->wpGc = md5(time());
        $entity->wpGcMaintained = md5(time());
        $entity->wpNc = md5(time());
        $entity->wpOc = md5(time());
        $entity->descLanguages = md5(time());
        $entity->defaultDesclang = md5(time());
        $entity->needNpaRecalc = mt_rand(0, 100);
        $entity->showCachelists = mt_rand(0, 100);
        $entity->protectOldCoords = mt_rand(0, 100);
        $entity->needsMaintenance = mt_rand(0, 100);
        $entity->listingOutdated = mt_rand(0, 100);
        $newEntity = new GeoCachesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
