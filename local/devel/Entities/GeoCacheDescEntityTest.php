<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeoCacheDescEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeoCacheDescEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->uuid = md5(time());$entity->node = mt_rand(0, 100);$entity->cacheId = mt_rand(0, 100);$entity->language = md5(time());$entity->desc = md5(time());$entity->descHtml = mt_rand(0, 100);$entity->descHtmledit = mt_rand(0, 100);$entity->hint = md5(time());$entity->shortDesc = md5(time());
		        $newEntity = new GeoCacheDescEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
