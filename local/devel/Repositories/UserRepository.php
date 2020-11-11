<?php

use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException;

class UserRepository
{
    const TABLE = 'user';

    /** @var Connection */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return UserEntity[]
     */
    public function fetchAll()
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records found');
        }

        $records = [];

        foreach ($result as $item) {
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return $records;
    }

    /**
     * @return UserEntity
     */
    public function fetchOneBy(array $where = [])
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->setMaxResults(1);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException('Record with given where clause not found');
        }

        return $this->getEntityFromDatabaseArray($result);
    }

    /**
     * @return UserEntity[]
     */
    public function fetchBy(array $where = [])
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . ' = ' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException('No records with given where clause found');
        }

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;
    }

    /**
     * @return UserEntity
     */
    public function create(UserEntity $entity)
    {
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException('The entity does already exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->userId = (int) $this->connection->lastInsertId();

        return $entity;
    }

    /**
     * @return UserEntity
     */
    public function update(UserEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            ['user_id' => $entity->userId]
        );

        return $entity;
    }

    /**
     * @return UserEntity
     */
    public function remove(UserEntity $entity)
    {
        if ($entity->isNew()) {
            throw new RecordNotPersistedException('The entity does not exist.');
        }

        $this->connection->delete(
            self::TABLE,
            ['user_id' => $entity->userId]
        );

        $entity->cacheId = null;

        return $entity;
    }

    /**
     * @return []
     */
    public function getDatabaseArrayFromEntity(UserEntity $entity)
    {
        return [
            'user_id' => $entity->userId,
            'uuid' => $entity->uuid,
            'node' => $entity->node,
            'date_created' => $entity->dateCreated,
            'last_modified' => $entity->lastModified,
            'last_login' => $entity->lastLogin,
            'username' => $entity->username,
            'password' => $entity->password,
            'admin_password' => $entity->adminPassword,
            'roles' => $entity->roles,
            'email' => $entity->email,
            'email_problems' => $entity->emailProblems,
            'first_email_problem' => $entity->firstEmailProblem,
            'last_email_problem' => $entity->lastEmailProblem,
            'mailing_problems' => $entity->mailingProblems,
            'accept_mailing' => $entity->acceptMailing,
            'usermail_send_addr' => $entity->usermailSendAddr,
            'latitude' => $entity->latitude,
            'longitude' => $entity->longitude,
            'is_active_flag' => $entity->isActiveFlag,
            'last_name' => $entity->lastName,
            'first_name' => $entity->firstName,
            'country' => $entity->country,
            'pmr_flag' => $entity->pmrFlag,
            'new_pw_code' => $entity->newPwCode,
            'new_pw_date' => $entity->newPwDate,
            'new_email_code' => $entity->newEmailCode,
            'new_email_date' => $entity->newEmailDate,
            'new_email' => $entity->newEmail,
            'permanent_login_flag' => $entity->permanentLoginFlag,
            'watchmail_mode' => $entity->watchmailMode,
            'watchmail_hour' => $entity->watchmailHour,
            'watchmail_nextmail' => $entity->watchmailNextmail,
            'watchmail_day' => $entity->watchmailDay,
            'activation_code' => $entity->activationCode,
            'statpic_logo' => $entity->statpicLogo,
            'statpic_text' => $entity->statpicText,
            'no_htmledit_flag' => $entity->noHtmleditFlag,
            'notify_radius' => $entity->notifyRadius,
            'notify_oconly' => $entity->notifyOconly,
            'language' => $entity->language,
            'language_guessed' => $entity->languageGuessed,
            'domain' => $entity->domain,
            'admin' => $entity->admin,
            'data_license' => $entity->dataLicense,
            'description' => $entity->description,
            'desc_htmledit' => $entity->descHtmledit,
        ];
    }

    /**
     * @return UserEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new UserEntity();
        $entity->userId = (int) $data['user_id'];
        $entity->uuid = (string) $data['uuid'];
        $entity->node = (int) $data['node'];
        $entity->dateCreated = new DateTime($data['date_created']);
        $entity->lastModified = new DateTime($data['last_modified']);
        $entity->lastLogin = new DateTime($data['last_login']);
        $entity->username = (string) $data['username'];
        $entity->password = (string) $data['password'];
        $entity->adminPassword = (string) $data['admin_password'];
        $entity->roles = (string) $data['roles'];
        $entity->email = (string) $data['email'];
        $entity->emailProblems = (int) $data['email_problems'];
        $entity->firstEmailProblem = new DateTime($data['first_email_problem']);
        $entity->lastEmailProblem = new DateTime($data['last_email_problem']);
        $entity->mailingProblems = (int) $data['mailing_problems'];
        $entity->acceptMailing = (int) $data['accept_mailing'];
        $entity->usermailSendAddr = (int) $data['usermail_send_addr'];
        $entity->latitude = $data['latitude'];
        $entity->longitude = $data['longitude'];
        $entity->isActiveFlag = (int) $data['is_active_flag'];
        $entity->lastName = (string) $data['last_name'];
        $entity->firstName = (string) $data['first_name'];
        $entity->country = (string) $data['country'];
        $entity->pmrFlag = (int) $data['pmr_flag'];
        $entity->newPwCode = (string) $data['new_pw_code'];
        $entity->newPwDate = new DateTime($data['new_pw_date']);
        $entity->newEmailCode = (string) $data['new_email_code'];
        $entity->newEmailDate = new DateTime($data['new_email_date']);
        $entity->newEmail = (string) $data['new_email'];
        $entity->permanentLoginFlag = (int) $data['permanent_login_flag'];
        $entity->watchmailMode = (int) $data['watchmail_mode'];
        $entity->watchmailHour = (int) $data['watchmail_hour'];
        $entity->watchmailNextmail = new DateTime($data['watchmail_nextmail']);
        $entity->watchmailDay = (int) $data['watchmail_day'];
        $entity->activationCode = (string) $data['activation_code'];
        $entity->statpicLogo = (int) $data['statpic_logo'];
        $entity->statpicText = (string) $data['statpic_text'];
        $entity->noHtmleditFlag = (int) $data['no_htmledit_flag'];
        $entity->notifyRadius = (int) $data['notify_radius'];
        $entity->notifyOconly = (int) $data['notify_oconly'];
        $entity->language = (string) $data['language'];
        $entity->languageGuessed = (int) $data['language_guessed'];
        $entity->domain = (string) $data['domain'];
        $entity->admin = $data['admin'];
        $entity->dataLicense = (int) $data['data_license'];
        $entity->description = (string) $data['description'];
        $entity->descHtmledit = (int) $data['desc_htmledit'];

        return $entity;
    }
}
