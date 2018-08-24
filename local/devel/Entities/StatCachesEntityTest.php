<?php 

 use OcTest\Modules\AbstractModuleTest; 

class StatCachesEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new StatCachesEntity();
		        self::assertTrue($entity->isNew());
		    $entity->cacheId = mt_rand(0, 100);
		        $newEntity = new StatCachesEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
