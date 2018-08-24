<?php 

 use OcTest\Modules\AbstractModuleTest; 

class PageEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new PageEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->slug = md5(time());$entity->metaKeywords = md5(time());$entity->metaDescription = md5(time());$entity->metaSocial = md5(time());$entity->active = mt_rand(0, 100);
		        $newEntity = new PageEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
