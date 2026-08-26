<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260519160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Flag cache_desc rows with hard-coded colors/backgrounds (dark-mode unsafe)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            "ALTER TABLE cache_desc
             ADD COLUMN desc_dark_unsafe TINYINT(1) NOT NULL DEFAULT 0
             COMMENT 'Listing HTML carries hard-coded colors/backgrounds; render in light-island under dark theme'"
        );

        // Backfill. Pattern is broad on purpose — false positives render in a
        // light card (harmless), false negatives leave a broken page (bad).
        $this->addSql(
            "UPDATE cache_desc
             SET desc_dark_unsafe = 1
             WHERE `desc` REGEXP '<font[^>]*color=|<font[^>]*bgcolor=|style=.{0,300}color:|style=.{0,300}background|bgcolor='"
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cache_desc DROP COLUMN desc_dark_unsafe');
    }
}
