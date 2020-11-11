<?php

use OcTest\Modules\AbstractModuleTest;

class UserEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new UserEntity();
        self::assertTrue($entity->isNew());
        $entity->userId = mt_rand(0, 100);
        $entity->uuid = md5(time());
        $entity->node = mt_rand(0, 100);
        $entity->username = md5(time());
        $entity->password = md5(time());
        $entity->adminPassword = md5(time());
        $entity->roles = md5(time());
        $entity->email = md5(time());
        $entity->emailProblems = mt_rand(0, 100);
        $entity->mailingProblems = mt_rand(0, 100);
        $entity->acceptMailing = mt_rand(0, 100);
        $entity->usermailSendAddr = mt_rand(0, 100);
        $entity->isActiveFlag = mt_rand(0, 100);
        $entity->lastName = md5(time());
        $entity->firstName = md5(time());
        $entity->country = md5(time());
        $entity->pmrFlag = mt_rand(0, 100);
        $entity->newPwCode = md5(time());
        $entity->newEmailCode = md5(time());
        $entity->newEmail = md5(time());
        $entity->permanentLoginFlag = mt_rand(0, 100);
        $entity->watchmailMode = mt_rand(0, 100);
        $entity->watchmailHour = mt_rand(0, 100);
        $entity->watchmailDay = mt_rand(0, 100);
        $entity->activationCode = md5(time());
        $entity->statpicLogo = mt_rand(0, 100);
        $entity->statpicText = md5(time());
        $entity->noHtmleditFlag = mt_rand(0, 100);
        $entity->notifyRadius = mt_rand(0, 100);
        $entity->notifyOconly = mt_rand(0, 100);
        $entity->language = md5(time());
        $entity->languageGuessed = mt_rand(0, 100);
        $entity->domain = md5(time());
        $entity->dataLicense = mt_rand(0, 100);
        $entity->description = md5(time());
        $entity->descHtmledit = mt_rand(0, 100);
        $newEntity = new UserEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
