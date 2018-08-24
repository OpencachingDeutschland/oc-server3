<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeoCacheReportStatusEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeoCacheReportStatusEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->name = md5(time());$entity->transId = mt_rand(0, 100);
		        $newEntity = new GeoCacheReportStatusEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
