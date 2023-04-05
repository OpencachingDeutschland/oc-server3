<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230404180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create new table user_login_block';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
                '
            CREATE TABLE IF NOT EXISTS user_login_block (
                    id                int(10) UNSIGNED NOT NULL auto_increment,
                    user_id           int(10) UNSIGNED NOT NULL COMMENT \'Betroffener Nutzer\',
                    login_block_until datetime NULL COMMENT \'Zeitstempel, bis wann der Login des Nutzers blockiert wird\',
                    message           longtext COMMENT \'optionale Nachricht vom Verfasser der Blockierung an den Nutzer\',
                    PRIMARY KEY (id, user_id));
        '
        );

        // re-add constraint.
        $this->addSql(
                'ALTER TABLE cache_type ADD KEY (name);'
        );
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('user_login_block');
    }
}
