<?php 

 use OcTest\Modules\AbstractModuleTest; 

class WatchesLogqueueEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new WatchesLogqueueEntity();
		        self::assertTrue($entity->isNew());
		    $entity->logId = mt_rand(0, 100);$entity->userId = mt_rand(0, 100);
		        $newEntity = new WatchesLogqueueEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
