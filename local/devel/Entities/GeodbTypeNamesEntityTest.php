<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeodbTypeNamesEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeodbTypeNamesEntity();
		        self::assertTrue($entity->isNew());
		    $entity->typeId = mt_rand(0, 100);$entity->typeLocale = md5(time());$entity->name = md5(time());
		        $newEntity = new GeodbTypeNamesEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
