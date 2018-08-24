<?php 

 use OcTest\Modules\AbstractModuleTest; 

class AttributeGroupsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new AttributeGroupsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->categoryId = mt_rand(0, 100);$entity->name = md5(time());$entity->transId = mt_rand(0, 100);
		        $newEntity = new AttributeGroupsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
