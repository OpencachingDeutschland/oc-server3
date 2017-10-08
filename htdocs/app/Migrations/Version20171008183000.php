<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20171008183000
 *
 * @package Application\Migrations
 */
class Version20171008183000 extends AbstractMigration
{
    /**
     * Sets the engine of the table user to INNODB.
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE user ENGINE = INNODB;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
