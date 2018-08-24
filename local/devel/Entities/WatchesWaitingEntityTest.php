<?php 

 use OcTest\Modules\AbstractModuleTest; 

class WatchesWaitingEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new WatchesWaitingEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->userId = mt_rand(0, 100);$entity->objectId = mt_rand(0, 100);$entity->objectType = mt_rand(0, 100);$entity->watchtext = md5(time());$entity->watchtype = mt_rand(0, 100);
		        $newEntity = new WatchesWaitingEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
