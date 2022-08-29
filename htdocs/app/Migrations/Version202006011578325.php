<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202006011578325 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cache_logs_archived ADD gdpr_deletion TINYINT(1) DEFAULT 0 NOT NULL AFTER picture;');
    }

    public function down(Schema $schema): void
    {
    }
}
