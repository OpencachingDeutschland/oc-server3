<?php 

 use OcTest\Modules\AbstractModuleTest; 

class WaypointReportsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new WaypointReportsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->reportId = mt_rand(0, 100);$entity->wpOc = md5(time());$entity->wpExternal = md5(time());$entity->source = md5(time());$entity->gcwpProcessed = mt_rand(0, 100);
		        $newEntity = new WaypointReportsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
