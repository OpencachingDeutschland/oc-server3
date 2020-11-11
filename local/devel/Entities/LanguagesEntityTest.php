<?php

use OcTest\Modules\AbstractModuleTest;

class LanguagesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new LanguagesEntity();
        self::assertTrue($entity->isNew());
        $entity->short = md5(time());
        $entity->name = md5(time());
        $entity->transId = mt_rand(0, 100);
        $entity->nativeName = md5(time());
        $entity->de = md5(time());
        $entity->en = md5(time());
        $entity->listDefaultDe = mt_rand(0, 100);
        $entity->listDefaultEn = mt_rand(0, 100);
        $entity->isTranslated = mt_rand(0, 100);
        $newEntity = new LanguagesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
