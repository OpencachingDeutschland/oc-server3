<?php 

 use OcTest\Modules\AbstractModuleTest; 

class OkapiTileCachesEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new OkapiTileCachesEntity();
		        self::assertTrue($entity->isNew());
		    $entity->z = mt_rand(0, 100);$entity->x = mt_rand(0, 100);$entity->y = mt_rand(0, 100);$entity->cacheId = mt_rand(0, 100);$entity->z21x = mt_rand(0, 100);$entity->z21y = mt_rand(0, 100);$entity->status = mt_rand(0, 100);$entity->type = mt_rand(0, 100);$entity->rating = mt_rand(0, 100);$entity->flags = mt_rand(0, 100);$entity->nameCrc = mt_rand(0, 100);
		        $newEntity = new OkapiTileCachesEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
