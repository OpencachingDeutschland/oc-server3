<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration to add column admin_password and roles to user table.
 */
class Version20170221215409 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `user` ADD `admin_password` BINARY(60)  NULL  DEFAULT NULL  AFTER `password`');
        $this->addSql('ALTER TABLE `user` ADD `roles` TEXT  NULL  AFTER `admin_password`;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
