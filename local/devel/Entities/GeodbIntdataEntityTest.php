<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeodbIntdataEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeodbIntdataEntity();
		        self::assertTrue($entity->isNew());
		    $entity->locId = mt_rand(0, 100);$entity->intVal = mt_rand(0, 100);$entity->intType = mt_rand(0, 100);$entity->intSubtype = mt_rand(0, 100);$entity->dateTypeSince = mt_rand(0, 100);$entity->dateTypeUntil = mt_rand(0, 100);
		        $newEntity = new GeodbIntdataEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
