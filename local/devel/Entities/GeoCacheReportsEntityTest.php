<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeoCacheReportsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeoCacheReportsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->cacheid = mt_rand(0, 100);$entity->userid = mt_rand(0, 100);$entity->reason = mt_rand(0, 100);$entity->note = md5(time());$entity->status = mt_rand(0, 100);$entity->adminid = mt_rand(0, 100);$entity->lastmodified = md5(time());$entity->comment = md5(time());
		        $newEntity = new GeoCacheReportsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
