<?php 

 use OcTest\Modules\AbstractModuleTest; 

class StatCacheLogsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new StatCacheLogsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->cacheId = mt_rand(0, 100);$entity->userId = mt_rand(0, 100);
		        $newEntity = new StatCacheLogsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
