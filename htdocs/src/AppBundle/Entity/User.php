<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"}), @ORM\UniqueConstraint(name="uuid", columns={"uuid"}), @ORM\UniqueConstraint(name="email", columns={"email"})}, indexes={@ORM\Index(name="notify_radius", columns={"notify_radius"})})
 * @ORM\Entity
 */
class User
{
    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=false)
     */
    private $uuid;

    /**
     * @var int
     *
     * @ORM\Column(name="node", type="smallint", nullable=false)
     */
    private $node = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=false)
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_modified", type="datetime", nullable=false)
     */
    private $lastModified;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="date", nullable=true)
     */
    private $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=60, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=128, nullable=true)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=true)
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="email_problems", type="integer", nullable=false)
     */
    private $emailProblems = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="first_email_problem", type="date", nullable=true)
     */
    private $firstEmailProblem;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_email_problem", type="datetime", nullable=true)
     */
    private $lastEmailProblem;

    /**
     * @var int
     *
     * @ORM\Column(name="mailing_problems", type="integer", nullable=false)
     */
    private $mailingProblems = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="accept_mailing", type="boolean", nullable=false)
     */
    private $acceptMailing = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="usermail_send_addr", type="boolean", nullable=false)
     */
    private $usermailSendAddr = false;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=false)
     */
    private $longitude;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active_flag", type="boolean", nullable=false)
     */
    private $isActiveFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=60, nullable=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=60, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2, nullable=true)
     */
    private $country;

    /**
     * @var bool
     *
     * @ORM\Column(name="pmr_flag", type="boolean", nullable=false)
     */
    private $pmrFlag;

    /**
     * @var string
     *
     * @ORM\Column(name="new_pw_code", type="string", length=13, nullable=true)
     */
    private $newPwCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="new_pw_date", type="datetime", nullable=true)
     */
    private $newPwDate;

    /**
     * @var string
     *
     * @ORM\Column(name="new_email_code", type="string", length=13, nullable=true)
     */
    private $newEmailCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="new_email_date", type="datetime", nullable=true)
     */
    private $newEmailDate;

    /**
     * @var string
     *
     * @ORM\Column(name="new_email", type="string", length=60, nullable=true)
     */
    private $newEmail;

    /**
     * @var bool
     *
     * @ORM\Column(name="permanent_login_flag", type="boolean", nullable=false)
     */
    private $permanentLoginFlag;

    /**
     * @var bool
     *
     * @ORM\Column(name="watchmail_mode", type="boolean", nullable=false)
     */
    private $watchmailMode = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="watchmail_hour", type="boolean", nullable=false)
     */
    private $watchmailHour = false;

    /**
     * @var \DateTime|string
     *
     * @ORM\Column(name="watchmail_nextmail", type="datetime", nullable=false)
     */
    private $watchmailNextmail = '0000-00-00 00:00:00';

    /**
     * @var bool
     *
     * @ORM\Column(name="watchmail_day", type="boolean", nullable=false)
     */
    private $watchmailDay = false;

    /**
     * @var string
     *
     * @ORM\Column(name="activation_code", type="string", length=13, nullable=false)
     */
    private $activationCode;

    /**
     * @var bool
     *
     * @ORM\Column(name="statpic_logo", type="boolean", nullable=false)
     */
    private $statpicLogo = false;

    /**
     * @var string
     *
     * @ORM\Column(name="statpic_text", type="string", length=30, nullable=false)
     */
    private $statpicText = 'Opencaching';

    /**
     * @var bool
     *
     * @ORM\Column(name="no_htmledit_flag", type="boolean", nullable=false)
     */
    private $noHtmleditFlag = false;

    /**
     * @var int
     *
     * @ORM\Column(name="notify_radius", type="integer", nullable=false)
     */
    private $notifyRadius = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="notify_oconly", type="boolean", nullable=false)
     */
    private $notifyOconly = true;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=2, nullable=true)
     */
    private $language;

    /**
     * @var bool
     *
     * @ORM\Column(name="language_guessed", type="boolean", nullable=false)
     */
    private $languageGuessed = false;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=40, nullable=true)
     */
    private $domain;

    /**
     * @var int
     *
     * @ORM\Column(name="admin", type="smallint", nullable=false)
     */
    private $admin = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="data_license", type="boolean", nullable=false)
     */
    private $dataLicense = false;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="desc_htmledit", type="boolean", nullable=false)
     */
    private $descHtmledit = true;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $userId;

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return \AppBundle\Entity\User
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set node
     *
     * @param int $node
     *
     * @return \AppBundle\Entity\User
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return bool
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return \AppBundle\Entity\User
     */
    public function setDateCreated(DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     *
     * @return \AppBundle\Entity\User
     */
    public function setLastModified(DateTime $lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return \AppBundle\Entity\User
     */
    public function setLastLogin(DateTime $lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return \AppBundle\Entity\User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return \AppBundle\Entity\User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return \AppBundle\Entity\User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set emailProblems
     *
     * @param int $emailProblems
     *
     * @return \AppBundle\Entity\User
     */
    public function setEmailProblems($emailProblems)
    {
        $this->emailProblems = $emailProblems;

        return $this;
    }

    /**
     * Get emailProblems
     *
     * @return int
     */
    public function getEmailProblems()
    {
        return $this->emailProblems;
    }

    /**
     * Set firstEmailProblem
     *
     * @param \DateTime $firstEmailProblem
     *
     * @return \AppBundle\Entity\User
     */
    public function setFirstEmailProblem(DateTime $firstEmailProblem)
    {
        $this->firstEmailProblem = $firstEmailProblem;

        return $this;
    }

    /**
     * Get firstEmailProblem
     *
     * @return \DateTime
     */
    public function getFirstEmailProblem()
    {
        return $this->firstEmailProblem;
    }

    /**
     * Set lastEmailProblem
     *
     * @param \DateTime $lastEmailProblem
     *
     * @return \AppBundle\Entity\User
     */
    public function setLastEmailProblem(DateTime $lastEmailProblem)
    {
        $this->lastEmailProblem = $lastEmailProblem;

        return $this;
    }

    /**
     * Get lastEmailProblem
     *
     * @return \DateTime
     */
    public function getLastEmailProblem()
    {
        return $this->lastEmailProblem;
    }

    /**
     * Set mailingProblems
     *
     * @param int $mailingProblems
     *
     * @return \AppBundle\Entity\User
     */
    public function setMailingProblems($mailingProblems)
    {
        $this->mailingProblems = $mailingProblems;

        return $this;
    }

    /**
     * Get mailingProblems
     *
     * @return int
     */
    public function getMailingProblems()
    {
        return $this->mailingProblems;
    }

    /**
     * Set acceptMailing
     *
     * @param bool $acceptMailing
     *
     * @return \AppBundle\Entity\User
     */
    public function setAcceptMailing($acceptMailing)
    {
        $this->acceptMailing = $acceptMailing;

        return $this;
    }

    /**
     * Get acceptMailing
     *
     * @return bool
     */
    public function getAcceptMailing()
    {
        return $this->acceptMailing;
    }

    /**
     * Set usermailSendAddr
     *
     * @param bool $usermailSendAddr
     *
     * @return \AppBundle\Entity\User
     */
    public function setUsermailSendAddr($usermailSendAddr)
    {
        $this->usermailSendAddr = $usermailSendAddr;

        return $this;
    }

    /**
     * Get usermailSendAddr
     *
     * @return bool
     */
    public function getUsermailSendAddr()
    {
        return $this->usermailSendAddr;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     *
     * @return \AppBundle\Entity\User
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     *
     * @return \AppBundle\Entity\User
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set isActiveFlag
     *
     * @param bool $isActiveFlag
     *
     * @return \AppBundle\Entity\User
     */
    public function setIsActiveFlag($isActiveFlag)
    {
        $this->isActiveFlag = $isActiveFlag;

        return $this;
    }

    /**
     * Get isActiveFlag
     *
     * @return bool
     */
    public function getIsActiveFlag()
    {
        return $this->isActiveFlag;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return \AppBundle\Entity\User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return \AppBundle\Entity\User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return \AppBundle\Entity\User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set pmrFlag
     *
     * @param bool $pmrFlag
     *
     * @return \AppBundle\Entity\User
     */
    public function setPmrFlag($pmrFlag)
    {
        $this->pmrFlag = $pmrFlag;

        return $this;
    }

    /**
     * Get pmrFlag
     *
     * @return bool
     */
    public function getPmrFlag()
    {
        return $this->pmrFlag;
    }

    /**
     * Set newPwCode
     *
     * @param string $newPwCode
     *
     * @return \AppBundle\Entity\User
     */
    public function setNewPwCode($newPwCode)
    {
        $this->newPwCode = $newPwCode;

        return $this;
    }

    /**
     * Get newPwCode
     *
     * @return string
     */
    public function getNewPwCode()
    {
        return $this->newPwCode;
    }

    /**
     * Set newPwDate
     *
     * @param \DateTime $newPwDate
     *
     * @return \AppBundle\Entity\User
     */
    public function setNewPwDate(DateTime $newPwDate)
    {
        $this->newPwDate = $newPwDate;

        return $this;
    }

    /**
     * Get newPwDate
     *
     * @return \DateTime
     */
    public function getNewPwDate()
    {
        return $this->newPwDate;
    }

    /**
     * Set newEmailCode
     *
     * @param string $newEmailCode
     *
     * @return \AppBundle\Entity\User
     */
    public function setNewEmailCode($newEmailCode)
    {
        $this->newEmailCode = $newEmailCode;

        return $this;
    }

    /**
     * Get newEmailCode
     *
     * @return string
     */
    public function getNewEmailCode()
    {
        return $this->newEmailCode;
    }

    /**
     * Set newEmailDate
     *
     * @param \DateTime $newEmailDate
     *
     * @return \AppBundle\Entity\User
     */
    public function setNewEmailDate(DateTime $newEmailDate)
    {
        $this->newEmailDate = $newEmailDate;

        return $this;
    }

    /**
     * Get newEmailDate
     *
     * @return \DateTime
     */
    public function getNewEmailDate()
    {
        return $this->newEmailDate;
    }

    /**
     * Set newEmail
     *
     * @param string $newEmail
     *
     * @return \AppBundle\Entity\User
     */
    public function setNewEmail($newEmail)
    {
        $this->newEmail = $newEmail;

        return $this;
    }

    /**
     * Get newEmail
     *
     * @return string
     */
    public function getNewEmail()
    {
        return $this->newEmail;
    }

    /**
     * Set permanentLoginFlag
     *
     * @param bool $permanentLoginFlag
     *
     * @return \AppBundle\Entity\User
     */
    public function setPermanentLoginFlag($permanentLoginFlag)
    {
        $this->permanentLoginFlag = $permanentLoginFlag;

        return $this;
    }

    /**
     * Get permanentLoginFlag
     *
     * @return bool
     */
    public function getPermanentLoginFlag()
    {
        return $this->permanentLoginFlag;
    }

    /**
     * Set watchmailMode
     *
     * @param bool $watchmailMode
     *
     * @return \AppBundle\Entity\User
     */
    public function setWatchmailMode($watchmailMode)
    {
        $this->watchmailMode = $watchmailMode;

        return $this;
    }

    /**
     * Get watchmailMode
     *
     * @return bool
     */
    public function getWatchmailMode()
    {
        return $this->watchmailMode;
    }

    /**
     * Set watchmailHour
     *
     * @param bool $watchmailHour
     *
     * @return \AppBundle\Entity\User
     */
    public function setWatchmailHour($watchmailHour)
    {
        $this->watchmailHour = $watchmailHour;

        return $this;
    }

    /**
     * Get watchmailHour
     *
     * @return bool
     */
    public function getWatchmailHour()
    {
        return $this->watchmailHour;
    }

    /**
     * Set watchmailNextmail
     *
     * @param \DateTime $watchmailNextmail
     *
     * @return \AppBundle\Entity\User
     */
    public function setWatchmailNextmail(DateTime $watchmailNextmail)
    {
        $this->watchmailNextmail = $watchmailNextmail;

        return $this;
    }

    /**
     * Get watchmailNextmail
     *
     * @return \DateTime|string
     */
    public function getWatchmailNextmail()
    {
        return $this->watchmailNextmail;
    }

    /**
     * Set watchmailDay
     *
     * @param bool $watchmailDay
     *
     * @return \AppBundle\Entity\User
     */
    public function setWatchmailDay($watchmailDay)
    {
        $this->watchmailDay = $watchmailDay;

        return $this;
    }

    /**
     * Get watchmailDay
     *
     * @return bool
     */
    public function getWatchmailDay()
    {
        return $this->watchmailDay;
    }

    /**
     * Set activationCode
     *
     * @param string $activationCode
     *
     * @return \AppBundle\Entity\User
     */
    public function setActivationCode($activationCode)
    {
        $this->activationCode = $activationCode;

        return $this;
    }

    /**
     * Get activationCode
     *
     * @return string
     */
    public function getActivationCode()
    {
        return $this->activationCode;
    }

    /**
     * Set statpicLogo
     *
     * @param bool $statpicLogo
     *
     * @return \AppBundle\Entity\User
     */
    public function setStatpicLogo($statpicLogo)
    {
        $this->statpicLogo = $statpicLogo;

        return $this;
    }

    /**
     * Get statpicLogo
     *
     * @return bool
     */
    public function getStatpicLogo()
    {
        return $this->statpicLogo;
    }

    /**
     * Set statpicText
     *
     * @param string $statpicText
     *
     * @return \AppBundle\Entity\User
     */
    public function setStatpicText($statpicText)
    {
        $this->statpicText = $statpicText;

        return $this;
    }

    /**
     * Get statpicText
     *
     * @return string
     */
    public function getStatpicText()
    {
        return $this->statpicText;
    }

    /**
     * Set noHtmleditFlag
     *
     * @param bool $noHtmleditFlag
     *
     * @return \AppBundle\Entity\User
     */
    public function setNoHtmleditFlag($noHtmleditFlag)
    {
        $this->noHtmleditFlag = $noHtmleditFlag;

        return $this;
    }

    /**
     * Get noHtmleditFlag
     *
     * @return bool
     */
    public function getNoHtmleditFlag()
    {
        return $this->noHtmleditFlag;
    }

    /**
     * Set notifyRadius
     *
     * @param int $notifyRadius
     *
     * @return \AppBundle\Entity\User
     */
    public function setNotifyRadius($notifyRadius)
    {
        $this->notifyRadius = $notifyRadius;

        return $this;
    }

    /**
     * Get notifyRadius
     *
     * @return int
     */
    public function getNotifyRadius()
    {
        return $this->notifyRadius;
    }

    /**
     * Set notifyOconly
     *
     * @param bool $notifyOconly
     *
     * @return \AppBundle\Entity\User
     */
    public function setNotifyOconly($notifyOconly)
    {
        $this->notifyOconly = $notifyOconly;

        return $this;
    }

    /**
     * Get notifyOconly
     *
     * @return bool
     */
    public function getNotifyOconly()
    {
        return $this->notifyOconly;
    }

    /**
     * Set language
     *
     * @param string $language
     *
     * @return \AppBundle\Entity\User
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set languageGuessed
     *
     * @param bool $languageGuessed
     *
     * @return \AppBundle\Entity\User
     */
    public function setLanguageGuessed($languageGuessed)
    {
        $this->languageGuessed = $languageGuessed;

        return $this;
    }

    /**
     * Get languageGuessed
     *
     * @return bool
     */
    public function getLanguageGuessed()
    {
        return $this->languageGuessed;
    }

    /**
     * Set domain
     *
     * @param string $domain
     *
     * @return \AppBundle\Entity\User
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set admin
     *
     * @param int $admin
     *
     * @return \AppBundle\Entity\User
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return int
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set dataLicense
     *
     * @param bool $dataLicense
     *
     * @return \AppBundle\Entity\User
     */
    public function setDataLicense($dataLicense)
    {
        $this->dataLicense = $dataLicense;

        return $this;
    }

    /**
     * Get dataLicense
     *
     * @return bool
     */
    public function getDataLicense()
    {
        return $this->dataLicense;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return \AppBundle\Entity\User
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set descHtmledit
     *
     * @param bool $descHtmledit
     *
     * @return \AppBundle\Entity\User
     */
    public function setDescHtmledit($descHtmledit)
    {
        $this->descHtmledit = $descHtmledit;

        return $this;
    }

    /**
     * Get descHtmledit
     *
     * @return bool
     */
    public function getDescHtmledit()
    {
        return $this->descHtmledit;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
