<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeoCacheNpaAreasEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeoCacheNpaAreasEntity();
		        self::assertTrue($entity->isNew());
		    $entity->cacheId = mt_rand(0, 100);$entity->npaId = mt_rand(0, 100);$entity->calculated = mt_rand(0, 100);
		        $newEntity = new GeoCacheNpaAreasEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
