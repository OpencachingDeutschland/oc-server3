<?php

namespace OcTest\Modules\Oc\Postfix;

use Oc\Postfix\LogEntity;
use OcTest\Modules\AbstractModuleTest;

class LogEntityTest extends AbstractModuleTest
{
    public function testLogEntity(): void
    {
        $entityArray = [
            'id' => 123,
            'email' => 'test@test.de',
            'status' => 'bounced',
            'created' => '0000',
        ];

        $entity = (new LogEntity())->fromDatabaseArray($entityArray);
        self::assertEquals($entityArray, $entity->toDatabaseArray());

        $entityArray['notValid'] = true;

        $entity = new LogEntity();
        $entity->setData($entityArray);

        unset($entityArray['notValid']);

        self::assertEquals($entityArray, $entity->toArray());
    }
}
