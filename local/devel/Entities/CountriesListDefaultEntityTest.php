<?php 

 use OcTest\Modules\AbstractModuleTest; 

class CountriesListDefaultEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new CountriesListDefaultEntity();
		        self::assertTrue($entity->isNew());
		    $entity->lang = md5(time());$entity->show = md5(time());
		        $newEntity = new CountriesListDefaultEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
