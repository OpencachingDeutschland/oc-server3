<?php 

 use OcTest\Modules\AbstractModuleTest; 

class CountriesOptionsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new CountriesOptionsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->country = md5(time());$entity->display = mt_rand(0, 100);$entity->gmZoom = mt_rand(0, 100);$entity->nodeId = mt_rand(0, 100);
		        $newEntity = new CountriesOptionsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
