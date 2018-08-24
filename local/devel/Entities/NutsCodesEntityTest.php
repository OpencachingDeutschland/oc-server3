<?php 

 use OcTest\Modules\AbstractModuleTest; 

class NutsCodesEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new NutsCodesEntity();
		        self::assertTrue($entity->isNew());
		    $entity->code = md5(time());$entity->name = md5(time());
		        $newEntity = new NutsCodesEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
