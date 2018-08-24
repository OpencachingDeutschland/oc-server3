<?php 

 use OcTest\Modules\AbstractModuleTest; 

class GeoCacheAttribEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new GeoCacheAttribEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->name = md5(time());$entity->icon = md5(time());$entity->transId = mt_rand(0, 100);$entity->groupId = mt_rand(0, 100);$entity->selectable = mt_rand(0, 100);$entity->category = mt_rand(0, 100);$entity->searchDefault = mt_rand(0, 100);$entity->default = mt_rand(0, 100);$entity->iconLarge = md5(time());$entity->iconNo = md5(time());$entity->iconUndef = md5(time());$entity->htmlDesc = md5(time());$entity->htmlDescTransId = mt_rand(0, 100);$entity->hidden = mt_rand(0, 100);$entity->gcId = mt_rand(0, 100);$entity->gcInc = mt_rand(0, 100);$entity->gcName = md5(time());
		        $newEntity = new GeoCacheAttribEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
