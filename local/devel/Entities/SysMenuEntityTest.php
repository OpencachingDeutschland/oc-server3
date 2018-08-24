<?php 

 use OcTest\Modules\AbstractModuleTest; 

class SysMenuEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new SysMenuEntity();
		        self::assertTrue($entity->isNew());
		    $entity->idString = md5(time());$entity->title = md5(time());$entity->titleTransId = mt_rand(0, 100);$entity->menustring = md5(time());$entity->menustringTransId = mt_rand(0, 100);$entity->access = mt_rand(0, 100);$entity->href = md5(time());$entity->visible = mt_rand(0, 100);$entity->position = mt_rand(0, 100);$entity->color = md5(time());$entity->sitemap = mt_rand(0, 100);$entity->onlyIfParent = mt_rand(0, 100);
		        $newEntity = new SysMenuEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
