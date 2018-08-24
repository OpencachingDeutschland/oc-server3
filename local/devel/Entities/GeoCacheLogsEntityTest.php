<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheLogsEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCacheLogsEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->uuid = md5(time());
        $entity->node = mt_rand(0, 100);
        $entity->okapiSyncbase = md5(time());
        $entity->cacheId = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $entity->type = mt_rand(0, 100);
        $entity->ocTeamComment = mt_rand(0, 100);
        $entity->needsMaintenance = mt_rand(0, 100);
        $entity->listingOutdated = mt_rand(0, 100);
        $entity->text = md5(time());
        $entity->textHtml = mt_rand(0, 100);
        $entity->textHtmledit = mt_rand(0, 100);
        $entity->ownerNotified = mt_rand(0, 100);
        $newEntity = new GeoCacheLogsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
