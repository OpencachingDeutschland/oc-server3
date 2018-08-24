<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeoCacheRatingEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeoCacheRatingEntity();
		        self::assertTrue($entity->isNew());
		    $entity->cacheId = mt_rand(0, 100);$entity->userId = mt_rand(0, 100);
		        $newEntity = new GeoCacheRatingEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
