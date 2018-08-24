<?php 

 use OcTest\Modules\AbstractModuleTest; 

class MigrationVersionsEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new MigrationVersionsEntity();
		        self::assertTrue($entity->isNew());
		    $entity->version = md5(time());
		        $newEntity = new MigrationVersionsEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
