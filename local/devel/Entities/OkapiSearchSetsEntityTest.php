<?php 

 use OcTest\Modules\AbstractModuleTest; 

class OkapiSearchSetsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new OkapiSearchSetsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->paramsHash = md5(time());
		        $newEntity = new OkapiSearchSetsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
