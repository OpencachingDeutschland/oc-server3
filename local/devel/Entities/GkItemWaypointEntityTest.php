<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GkItemWaypointEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GkItemWaypointEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->wp = md5(time());
		        $newEntity = new GkItemWaypointEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
