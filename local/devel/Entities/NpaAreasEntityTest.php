<?php 

 use OcTest\Modules\AbstractModuleTest; 

class NpaAreasEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new NpaAreasEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->typeId = md5(time());$entity->exclude = mt_rand(0, 100);$entity->name = md5(time());
		        $newEntity = new NpaAreasEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
