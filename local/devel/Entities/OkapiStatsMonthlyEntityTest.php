<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiStatsMonthlyEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new OkapiStatsMonthlyEntity();
        self::assertTrue($entity->isNew());
        $entity->consumerKey = md5(time());
        $entity->userId = mt_rand(0, 100);
        $entity->serviceName = md5(time());
        $entity->totalCalls = mt_rand(0, 100);
        $entity->httpCalls = mt_rand(0, 100);
        $newEntity = new OkapiStatsMonthlyEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
