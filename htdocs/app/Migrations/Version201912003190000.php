<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version201912003190000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user	ADD gdpr_deletion TINYINT(1) DEFAULT 0 NOT NULL;');
        $this->addSql('ALTER TABLE caches ADD gdpr_deletion TINYINT(1) DEFAULT 0 NOT NULL;');
        $this->addSql('ALTER TABLE cache_logs ADD gdpr_deletion TINYINT(1) DEFAULT 0 NOT NULL;');

        $this->addSql('UPDATE user SET gdpr_deletion = 1 WHERE username like \'delete_%\'');
    }

    public function down(Schema $schema): void
    {
    }
}
