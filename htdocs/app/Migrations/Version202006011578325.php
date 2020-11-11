<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version202006011578325 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cache_logs_archived ADD gdpr_deletion TINYINT(1) DEFAULT 0 NOT NULL AFTER picture;');
    }

    public function down(Schema $schema): void
    {
    }
}
