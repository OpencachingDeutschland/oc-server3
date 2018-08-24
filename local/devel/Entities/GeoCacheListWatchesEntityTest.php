<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeoCacheListWatchesEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeoCacheListWatchesEntity();
		        self::assertTrue($entity->isNew());
		    $entity->cacheListId = mt_rand(0, 100);$entity->userId = mt_rand(0, 100);
		        $newEntity = new GeoCacheListWatchesEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
