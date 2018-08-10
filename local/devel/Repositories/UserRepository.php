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
     * @param array $where
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
     * @param array $where
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
     * @param UserEntity $entity
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
     * @param UserEntity $entity
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
     * @param UserEntity $entity
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
     * @param UserEntity $entity
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
     * @param array $data
     * @return UserEntity
     */
    public function getEntityFromDatabaseArray(array $data)
    {
        $entity = new UserEntity();
        $entity->userId = $data['user_id'];
        $entity->uuid = $data['uuid'];
        $entity->node = $data['node'];
        $entity->dateCreated = $data['date_created'];
        $entity->lastModified = $data['last_modified'];
        $entity->lastLogin = $data['last_login'];
        $entity->username = $data['username'];
        $entity->password = $data['password'];
        $entity->adminPassword = $data['admin_password'];
        $entity->roles = $data['roles'];
        $entity->email = $data['email'];
        $entity->emailProblems = $data['email_problems'];
        $entity->firstEmailProblem = $data['first_email_problem'];
        $entity->lastEmailProblem = $data['last_email_problem'];
        $entity->mailingProblems = $data['mailing_problems'];
        $entity->acceptMailing = $data['accept_mailing'];
        $entity->usermailSendAddr = $data['usermail_send_addr'];
        $entity->latitude = $data['latitude'];
        $entity->longitude = $data['longitude'];
        $entity->isActiveFlag = $data['is_active_flag'];
        $entity->lastName = $data['last_name'];
        $entity->firstName = $data['first_name'];
        $entity->country = $data['country'];
        $entity->pmrFlag = $data['pmr_flag'];
        $entity->newPwCode = $data['new_pw_code'];
        $entity->newPwDate = $data['new_pw_date'];
        $entity->newEmailCode = $data['new_email_code'];
        $entity->newEmailDate = $data['new_email_date'];
        $entity->newEmail = $data['new_email'];
        $entity->permanentLoginFlag = $data['permanent_login_flag'];
        $entity->watchmailMode = $data['watchmail_mode'];
        $entity->watchmailHour = $data['watchmail_hour'];
        $entity->watchmailNextmail = $data['watchmail_nextmail'];
        $entity->watchmailDay = $data['watchmail_day'];
        $entity->activationCode = $data['activation_code'];
        $entity->statpicLogo = $data['statpic_logo'];
        $entity->statpicText = $data['statpic_text'];
        $entity->noHtmleditFlag = $data['no_htmledit_flag'];
        $entity->notifyRadius = $data['notify_radius'];
        $entity->notifyOconly = $data['notify_oconly'];
        $entity->language = $data['language'];
        $entity->languageGuessed = $data['language_guessed'];
        $entity->domain = $data['domain'];
        $entity->admin = $data['admin'];
        $entity->dataLicense = $data['data_license'];
        $entity->description = $data['description'];
        $entity->descHtmledit = $data['desc_htmledit'];

        return $entity;
    }
}
