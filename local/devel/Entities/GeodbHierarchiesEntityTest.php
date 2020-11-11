<?php

use OcTest\Modules\AbstractModuleTest;

class GeodbHierarchiesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeodbHierarchiesEntity();
        self::assertTrue($entity->isNew());
        $entity->locId = mt_rand(0, 100);
        $entity->level = mt_rand(0, 100);
        $entity->idLvl1 = mt_rand(0, 100);
        $entity->idLvl2 = mt_rand(0, 100);
        $entity->idLvl3 = mt_rand(0, 100);
        $entity->idLvl4 = mt_rand(0, 100);
        $entity->idLvl5 = mt_rand(0, 100);
        $entity->idLvl6 = mt_rand(0, 100);
        $entity->idLvl7 = mt_rand(0, 100);
        $entity->idLvl8 = mt_rand(0, 100);
        $entity->idLvl9 = mt_rand(0, 100);
        $entity->dateTypeSince = mt_rand(0, 100);
        $entity->dateTypeUntil = mt_rand(0, 100);
        $newEntity = new GeodbHierarchiesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
