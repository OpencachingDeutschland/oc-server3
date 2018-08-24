<?php 

 use OcTest\Modules\AbstractModuleTest; 

class CoordinatesEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new CoordinatesEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->type = mt_rand(0, 100);$entity->subtype = mt_rand(0, 100);$entity->cacheId = mt_rand(0, 100);$entity->userId = mt_rand(0, 100);$entity->logId = mt_rand(0, 100);$entity->description = md5(time());
		        $newEntity = new CoordinatesEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
