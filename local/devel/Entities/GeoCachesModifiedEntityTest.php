<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeoCachesModifiedEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeoCachesModifiedEntity();
		        self::assertTrue($entity->isNew());
		    $entity->cacheId = mt_rand(0, 100);$entity->name = md5(time());$entity->type = mt_rand(0, 100);$entity->size = mt_rand(0, 100);$entity->difficulty = mt_rand(0, 100);$entity->terrain = mt_rand(0, 100);$entity->wpGc = md5(time());$entity->wpNc = md5(time());$entity->restoredBy = mt_rand(0, 100);
		        $newEntity = new GeoCachesModifiedEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
