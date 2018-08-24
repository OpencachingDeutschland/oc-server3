<?php 

 use OcTest\Modules\AbstractModuleTest; 

class EmailUserEntityTest extends AbstractModuleTest
{
	public function testEntity()
	{
		$entity = new EmailUserEntity();
		        self::assertTrue($entity->isNew());
		    $entity->id = mt_rand(0, 100);$entity->ipaddress = md5(time());$entity->fromUserId = mt_rand(0, 100);$entity->fromEmail = md5(time());$entity->toUserId = mt_rand(0, 100);$entity->toEmail = md5(time());
		        $newEntity = new EmailUserEntity();
		        $newEntity->fromArray($entity->toArray());

		        self::assertEquals($entity, $newEntity);
		        self::assertFalse($entity->isNew());
	}
}
