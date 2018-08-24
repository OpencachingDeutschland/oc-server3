<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeodbChangelogEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeodbChangelogEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->beschreibung = md5(time());$entity->autor = md5(time());$entity->version = md5(time());
		        $newEntity = new GeodbChangelogEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
