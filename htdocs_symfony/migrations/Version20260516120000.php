<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260516120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add coordinates.logpw — per-user remembered log password for a cache';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "ALTER TABLE coordinates
             ADD COLUMN logpw VARCHAR(20) NOT NULL DEFAULT ''
             COMMENT 'Per-user remembered log password for this cache (type=2 rows)'"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE coordinates DROP COLUMN logpw');
    }
}
