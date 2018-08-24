<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

use Doctrine\DBAL\Connection;
use Nette\PhpGenerator\PhpLiteral;

require_once __DIR__ . '/../../htdocs/app/autoload.php';

function lowerCamelCase($string)
{
    $string = ucwords($string, '_');
    $string[0] = strtolower($string[0]);
    $string = str_replace('_', '', $string);

    return $string;
}

function mapDataBaseTypesForPhpDoc($type)
{
    if (mapDataBaseTypesForPhP($type)) {
        return mapDataBaseTypesForPhP($type);
    }

    return $type;
}

function mapDataBaseTypesForPhP($type)
{
    switch ($type) {
        case 'tinyint':
        case 'mediumint':
        case 'small':
        case 'int':
        case 'bigint':
            return 'int';
        case 'varchar':
        case 'text':
        case 'longtext':
        case 'mediumtext':
        case 'binary':
        case 'blob':
        case 'char':
        case 'timestamp':
        case 'mediumblob':
            return 'string';
        case 'date':
        case 'datetime':
            return 'DateTime';
    }
}

/** @var Connection $connection */
$kernel = new AppKernel('dev', false);
$kernel->boot();
$connection = $kernel::Container()->get(Connection::class);

$tables = $connection->fetchAll(
    'SELECT DISTINCT(TABLE_NAME)
     FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = :databaseName',
    [':databaseName' => 'opencaching']
);

foreach ($tables as $table) {
    $tableName = $table['TABLE_NAME'];
    $tableColumns = $connection
        ->fetchAll(
            'SELECT * 
             FROM information_schema.COLUMNS 
             WHERE TABLE_SCHEMA = :databaseName 
             AND TABLE_NAME = :tableName;',
            [
                ':databaseName' => 'opencaching',
                ':tableName' => $tableName,
            ]
        );

    $isNewMethod = lowerCamelCase($tableColumns[0]['COLUMN_NAME']);
    $isNewColumnName = $tableColumns[0]['COLUMN_NAME'];

    $classNameRepository = str_replace('_', '', ucwords($tableName, '_') . 'Repository');
    $classNameRepositoryTest = str_replace('_', '', ucwords($tableName, '_') . 'RepositoryTest');
    $classNameEntity = str_replace('_', '', ucwords($tableName, '_') . 'Entity');
    $classNameEntityTest = str_replace('_', '', ucwords($tableName, '_') . 'EntityTest');

    if (strpos($classNameEntity, 'Cache') === 0) {
        $classNameEntity = str_replace('Cache', 'GeoCache', $classNameEntity);
    }

    $classEntity = new Nette\PhpGenerator\ClassType($classNameEntity);
    $classEntity->setExtends(Oc\Repository\AbstractEntity::class);

    $classEntityTest = new Nette\PhpGenerator\ClassType($classNameEntity . 'Test');
    $classEntityTest->setExtends('AbstractModuleTest');
    $classEntityTestBody = '
        $entity = new ' . $classNameEntity . '();
        self::assertTrue($entity->isNew());
    ';

    foreach ($tableColumns as $column) {
        if ($isNewMethod === false && $column['COLUMN_KEY'] === 'PRI') {
            $isNewMethod = lowerCamelCase($column['COLUMN_NAME']);
            $isNewColumnName = $column['COLUMN_NAME'];
        }

        $classEntity->addProperty(lowerCamelCase($column['COLUMN_NAME']))
            ->setVisibility('public')
            ->addComment('@var ' . mapDataBaseTypesForPhpDoc($column['DATA_TYPE']));

        if (mapDataBaseTypesForPhpDoc($column['DATA_TYPE']) === 'int') {
            $classEntityTestBody .= '$entity->' . lowerCamelCase($column['COLUMN_NAME']) . ' = mt_rand(0, 100);';
        } elseif (mapDataBaseTypesForPhpDoc($column['DATA_TYPE']) === 'string') {
            $classEntityTestBody .= '$entity->' . lowerCamelCase($column['COLUMN_NAME']) . ' = md5(time());';
        }
    }

    $classEntityTestBody .= '
        $newEntity = new ' . $classNameEntity . '();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    ';

    $classEntityTest->addMethod('testEntity')
        ->setVisibility('public')
        ->setBody($classEntityTestBody);

    if ($isNewMethod) {
        $classEntity->addMethod('isNew')
            ->addComment('@return bool')
            ->setVisibility('public')
            ->setBody('return $this->' . $isNewMethod . ' === null;');
    }

    $classRepository = new Nette\PhpGenerator\ClassType($classNameRepository);
    $classRepository->addConstant('TABLE', $tableName);
    $classRepository
        ->addProperty('connection')
        ->setVisibility('private')
        ->addComment('@var Connection');

    $classRepository
        ->addMethod('__construct')
        ->setVisibility('public')
        ->setBody('$this->connection = $connection;')
        ->addParameter('connection')
        ->setTypeHint('Connection');

    $classRepository
        ->addMethod('fetchAll')
        ->setVisibility('public')
        ->addComment('@return ' . $classNameEntity . '[]')
        ->setBody(
            '
        $statement = $this->connection->createQueryBuilder()
            ->select(\'*\')
            ->from(self::TABLE)
            ->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException(\'No records found\');
        }

        $records = [];

        foreach ($result as $item) {
            $records[] = $this->getEntityFromDatabaseArray($item);
        }

        return $records;
        '
        );

    $classRepository
        ->addMethod('fetchOneBy')
        ->setVisibility('public')
        ->addComment('@return ' . $classNameEntity)
        ->setBody(
            '
        $queryBuilder = $this->connection->createQueryBuilder()
             ->select(\'*\')
             ->from(self::TABLE)
             ->setMaxResults(1);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . \' = \' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetch();

        if ($statement->rowCount() === 0) {
            throw new RecordNotFoundException(\'Record with given where clause not found\');
        }

        return $this->getEntityFromDatabaseArray($result);
        '
        )
        ->addParameter('where', new PhpLiteral('[]'))
        ->setTypeHint('array');


    $classRepository
        ->addMethod('fetchBy')
        ->setVisibility('public')
        ->addComment('@return ' . $classNameEntity . '[]')
        ->setBody(
            '
        $queryBuilder = $this->connection->createQueryBuilder()
             ->select(\'*\')
             ->from(self::TABLE);

        if (count($where) > 0) {
            foreach ($where as $column => $value) {
                $queryBuilder->andWhere($column . \' = \' . $queryBuilder->createNamedParameter($value));
            }
        }

        $statement = $queryBuilder->execute();

        $result = $statement->fetchAll();

        if ($statement->rowCount() === 0) {
            throw new RecordsNotFoundException(\'No records with given where clause found\');
        }

        $entities = [];

        foreach ($result as $item) {
            $entities[] = $this->getEntityFromDatabaseArray($item);
        }

        return $entities;'
        )
        ->addParameter('where', new PhpLiteral('[]'))
        ->setTypeHint('array');


    $classRepository
        ->addMethod('create')
        ->setVisibility('public')
        ->addComment('@return ' . $classNameEntity)
        ->setBody(
            '
        if (!$entity->isNew()) {
            throw new RecordAlreadyExistsException(\'The entity does already exist.\');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->insert(
            self::TABLE,
            $databaseArray
        );

        $entity->' . $isNewMethod . ' = (int) $this->connection->lastInsertId();

        return $entity;
        '
        )
        ->addParameter('entity')
        ->setTypeHint($classNameEntity);

    $classRepository
        ->addMethod('update')
        ->setVisibility('public')
        ->addComment('@return ' . $classNameEntity)
        ->setBody(
            'if ($entity->isNew()) {
            throw new RecordNotPersistedException(\'The entity does not exist.\');
        }

        $databaseArray = $this->getDatabaseArrayFromEntity($entity);

        $this->connection->update(
            self::TABLE,
            $databaseArray,
            [\'' . $isNewColumnName . '\' => $entity->' . $isNewMethod . ']
        );
        
        return $entity;
        '
        )
        ->addParameter('entity')
        ->setTypeHint($classNameEntity);

    $classRepository
        ->addMethod('remove')
        ->setVisibility('public')
        ->addComment('@return ' . $classNameEntity)
        ->setBody(
            '
                if ($entity->isNew()) {
            throw new RecordNotPersistedException(\'The entity does not exist.\');
        }

        $this->connection->delete(
            self::TABLE,
            [\'' . $isNewColumnName . '\' => $entity->' . $isNewMethod . ']
        );

        $entity->cacheId = null;

        return $entity;
        '
        )
        ->addParameter('entity')
        ->setTypeHint($classNameEntity);

    $getDatabaseArrayFromEntityBody = "return [\n";

    foreach ($tableColumns as $column) {
        $getDatabaseArrayFromEntityBody .= "'" . $column['COLUMN_NAME'] . "' => " . '$entity->' .
            lowerCamelCase($column['COLUMN_NAME']) . ",\n";
    }

    $getDatabaseArrayFromEntityBody .= "];\n";

    $classRepository
        ->addMethod('getDatabaseArrayFromEntity')
        ->setVisibility('public')
        ->addComment('@return []')
        ->setBody($getDatabaseArrayFromEntityBody)
        ->addParameter('entity')
        ->setTypeHint($classNameEntity);

    $getEntityFromDatabaseArrayBody = '$entity = new ' . $classNameEntity . '();' . "\n";

    foreach ($tableColumns as $column) {
        $dataType = mapDataBaseTypesForPhP($column['DATA_TYPE']);
        if ($dataType === 'DateTime') {
            $dataType = ' new DateTime($data[\'' . $column['COLUMN_NAME'] . '\'])';
        } elseif ($dataType) {
            $dataType = '(' . $dataType . ') $data[\'' . $column['COLUMN_NAME'] . '\']';
        } else {
            $dataType = '$data[\'' . $column['COLUMN_NAME'] . '\']';
        }
        $getEntityFromDatabaseArrayBody .= '$entity->' .
            lowerCamelCase($column['COLUMN_NAME']) . ' = ' . $dataType . ';' . "\n";
    }

    $getEntityFromDatabaseArrayBody .= 'return $entity;' . "\n";

    $classRepository
        ->addMethod('getEntityFromDatabaseArray')
        ->setVisibility('public')
        ->addComment('@return ' . $classNameEntity)
        ->setBody($getEntityFromDatabaseArrayBody)
        ->addParameter('data')->setTypeHint('array');


    file_put_contents(
        __DIR__ . '/Entities/' . $classNameEntity . '.php',
        "<?php \n\n" . $classEntity->__toString(),
        LOCK_EX
    );

    file_put_contents(
        __DIR__ . '/Entities/' . $classNameEntity . 'Test.php',
        "<?php \n\n use OcTest\Modules\AbstractModuleTest; \n\n" . $classEntityTest->__toString(),
        LOCK_EX
    );


    file_put_contents(
        __DIR__ . '/Repositories/' . $classNameRepository . '.php',
        "<?php \n\n" .
        "use Doctrine\DBAL\Connection;
use Oc\Repository\Exception\RecordAlreadyExistsException;
use Oc\Repository\Exception\RecordNotFoundException;
use Oc\Repository\Exception\RecordNotPersistedException;
use Oc\Repository\Exception\RecordsNotFoundException; \n\n" .
        $classRepository->__toString(),
        LOCK_EX
    );
}
