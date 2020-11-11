<?php

use OcTest\Modules\AbstractModuleTest;

class GnsLocationsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GnsLocationsEntity();
        self::assertTrue($entity->isNew());
        $entity->rc = mt_rand(0, 100);
        $entity->ufi = mt_rand(0, 100);
        $entity->uni = mt_rand(0, 100);
        $entity->dmsLat = mt_rand(0, 100);
        $entity->dmsLon = mt_rand(0, 100);
        $entity->utm = md5(time());
        $entity->jog = md5(time());
        $entity->fc = md5(time());
        $entity->dsg = md5(time());
        $entity->pc = mt_rand(0, 100);
        $entity->cc1 = md5(time());
        $entity->adm1 = md5(time());
        $entity->adm2 = md5(time());
        $entity->dim = mt_rand(0, 100);
        $entity->cc2 = md5(time());
        $entity->nt = md5(time());
        $entity->lc = md5(time());
        $entity->sHORTFORM = md5(time());
        $entity->gENERIC = md5(time());
        $entity->sORTNAME = md5(time());
        $entity->fULLNAME = md5(time());
        $entity->fULLNAMEND = md5(time());
        $entity->admtxt1 = md5(time());
        $entity->admtxt3 = md5(time());
        $entity->admtxt4 = md5(time());
        $entity->admtxt2 = md5(time());
        $newEntity = new GnsLocationsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
