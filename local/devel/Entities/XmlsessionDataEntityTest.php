<?php 

 use OcTest\Modules\AbstractModuleTest; 

class XmlsessionDataEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new XmlsessionDataEntity();
		        self::assertTrue($entity->isNew());
		    $entity->sessionId = mt_rand(0, 100);$entity->objectType = mt_rand(0, 100);$entity->objectId = mt_rand(0, 100);
		        $newEntity = new XmlsessionDataEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
