<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GkItemEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GkItemEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->name = md5(time());$entity->description = md5(time());$entity->userid = mt_rand(0, 100);$entity->typeid = mt_rand(0, 100);$entity->stateid = mt_rand(0, 100);
		        $newEntity = new GkItemEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
