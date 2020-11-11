<?php

class UserEntity extends Oc\Repository\AbstractEntity
{
    /** @var int */
    public $userId;

    /** @var string */
    public $uuid;

    /** @var int */
    public $node;

    /** @var DateTime */
    public $dateCreated;

    /** @var DateTime */
    public $lastModified;

    /** @var DateTime */
    public $lastLogin;

    /** @var string */
    public $username;

    /** @var string */
    public $password;

    /** @var string */
    public $adminPassword;

    /** @var string */
    public $roles;

    /** @var string */
    public $email;

    /** @var int */
    public $emailProblems;

    /** @var DateTime */
    public $firstEmailProblem;

    /** @var DateTime */
    public $lastEmailProblem;

    /** @var int */
    public $mailingProblems;

    /** @var int */
    public $acceptMailing;

    /** @var int */
    public $usermailSendAddr;

    /** @var float */
    public $latitude;

    /** @var float */
    public $longitude;

    /** @var int */
    public $isActiveFlag;

    /** @var string */
    public $lastName;

    /** @var string */
    public $firstName;

    /** @var string */
    public $country;

    /** @var int */
    public $pmrFlag;

    /** @var string */
    public $newPwCode;

    /** @var DateTime */
    public $newPwDate;

    /** @var string */
    public $newEmailCode;

    /** @var DateTime */
    public $newEmailDate;

    /** @var string */
    public $newEmail;

    /** @var int */
    public $permanentLoginFlag;

    /** @var int */
    public $watchmailMode;

    /** @var int */
    public $watchmailHour;

    /** @var DateTime */
    public $watchmailNextmail;

    /** @var int */
    public $watchmailDay;

    /** @var string */
    public $activationCode;

    /** @var int */
    public $statpicLogo;

    /** @var string */
    public $statpicText;

    /** @var int */
    public $noHtmleditFlag;

    /** @var int */
    public $notifyRadius;

    /** @var int */
    public $notifyOconly;

    /** @var string */
    public $language;

    /** @var int */
    public $languageGuessed;

    /** @var string */
    public $domain;

    /** @var smallint */
    public $admin;

    /** @var int */
    public $dataLicense;

    /** @var string */
    public $description;

    /** @var int */
    public $descHtmledit;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->userId === null;
    }
}
