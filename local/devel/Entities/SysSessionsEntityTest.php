<?php 

 use OcTest\Modules\AbstractModuleTest; 

class SysSessionsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new SysSessionsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->uuid = md5(time());$entity->userId = mt_rand(0, 100);$entity->permanent = mt_rand(0, 100);
		        $newEntity = new SysSessionsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
