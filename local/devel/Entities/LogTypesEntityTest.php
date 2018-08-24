<?php 

 use OcTest\Modules\AbstractModuleTest; 

class LogTypesEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new LogTypesEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->name = md5(time());$entity->transId = mt_rand(0, 100);$entity->permission = md5(time());$entity->cacheStatus = mt_rand(0, 100);$entity->de = md5(time());$entity->en = md5(time());$entity->iconSmall = md5(time());$entity->allowRating = mt_rand(0, 100);$entity->requirePassword = mt_rand(0, 100);$entity->maintenanceLogs = mt_rand(0, 100);
		        $newEntity = new LogTypesEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
