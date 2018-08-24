<?php 

 use OcTest\Modules\AbstractModuleTest; 

class OkapiVarsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new OkapiVarsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->var = md5(time());$entity->value = md5(time());
		        $newEntity = new OkapiVarsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
