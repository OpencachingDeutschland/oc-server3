<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeoCacheReportReasonsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeoCacheReportReasonsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->name = md5(time());$entity->transId = mt_rand(0, 100);$entity->order = mt_rand(0, 100);
		        $newEntity = new GeoCacheReportReasonsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
