<?php 

 use OcTest\Modules\AbstractModuleTest; 

class PageBlockEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new PageBlockEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->pageId = mt_rand(0, 100);$entity->locale = md5(time());$entity->title = md5(time());$entity->html = md5(time());$entity->position = mt_rand(0, 100);$entity->active = mt_rand(0, 100);
		        $newEntity = new PageBlockEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
