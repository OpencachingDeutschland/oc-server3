<?php 

 use OcTest\Modules\AbstractModuleTest; 

class StatCacheListsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new StatCacheListsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->cacheListId = mt_rand(0, 100);$entity->entries = mt_rand(0, 100);$entity->watchers = mt_rand(0, 100);
		        $newEntity = new StatCacheListsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
