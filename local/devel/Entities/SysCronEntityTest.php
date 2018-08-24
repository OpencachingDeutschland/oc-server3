<?php 

 use OcTest\Modules\AbstractModuleTest; 

class SysCronEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new SysCronEntity();
		        self::assertTrue($entity->isNew());
		    $entity->name = md5(time());
		        $newEntity = new SysCronEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
