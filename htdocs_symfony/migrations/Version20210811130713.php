<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811130713 extends AbstractMigration
{
    public function getDescription()
    : string
    {
        return 'create tables existing in live-DB,but not in test-DB';
    }

    public function up(Schema $schema)
    : void {
        // add cache_logs/gpdr_deletion
        $this->addSql(
            'ALTER TABLE cache_logs ADD COLUMN IF NOT EXISTS gdpr_deletion tinyint(1) NOT NULL DEFAULT 0;'
        );

        // add cache_logs_archived/gdpr_deletion
        $this->addSql(
            'ALTER TABLE cache_logs_archived ADD COLUMN IF NOT EXISTS gdpr_deletion tinyint(1) NOT NULL DEFAULT 0;'
        );

        // add caches/gdpr_deletion
        $this->addSql(
            'ALTER TABLE caches ADD COLUMN IF NOT EXISTS gdpr_deletion tinyint(1) NOT NULL DEFAULT 0;'
        );

        // add user/gdpr_deletion
        $this->addSql(
            'ALTER TABLE user ADD COLUMN IF NOT EXISTS gdpr_deletion tinyint(1) NOT NULL DEFAULT 0;'
        );

        // add pictures_modified
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS pictures_modified (
                    id int(10) NOT NULL,
                    date_modified datetime NOT NULL,
                    operation char(1) NOT NULL,
                    date_created datetime NOT NULL,
                    url varchar(255) NOT NULL,
                    title varchar(250) NOT NULL,
                    object_id int(10) UNSIGNED NOT NULL,
                    object_type tinyint(3) UNSIGNED NOT NULL,
                    spoiler tinyint(1) NOT NULL,
                    unknown_format tinyint(1) NOT NULL,
                    display tinyint(1) NOT NULL,
                    original_id int(10) NOT NULL,
                    restored_by int(10) NOT NULL,
                    KEY object_type (object_type, object_id, date_modified),
                    UNIQUE KEY (id, operation)
                )
                ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
        );
    }

    public function down(Schema $schema)
    : void {
    }
}
