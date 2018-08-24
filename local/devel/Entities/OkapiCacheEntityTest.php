<?php 

 use OcTest\Modules\AbstractModuleTest; 

class OkapiCacheEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new OkapiCacheEntity();
		        self::assertTrue($entity->isNew());
		    $entity->key = md5(time());$entity->value = md5(time());
		        $newEntity = new OkapiCacheEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
