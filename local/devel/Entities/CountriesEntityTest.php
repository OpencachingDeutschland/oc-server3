<?php

use OcTest\Modules\AbstractModuleTest;

class CountriesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new CountriesEntity();
        self::assertTrue($entity->isNew());
        $entity->short = md5(time());
        $entity->name = md5(time());
        $entity->transId = mt_rand(0, 100);
        $entity->de = md5(time());
        $entity->en = md5(time());
        $entity->listDefaultDe = mt_rand(0, 100);
        $entity->sortDe = md5(time());
        $entity->listDefaultEn = mt_rand(0, 100);
        $entity->sortEn = md5(time());
        $entity->admDisplay2 = mt_rand(0, 100);
        $entity->admDisplay3 = mt_rand(0, 100);
        $newEntity = new CountriesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
