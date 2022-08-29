<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to add column is_translated to the languages table and set all translated languages to 1.
 */
final class Version20170830005500 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `languages` ADD `is_translated` TINYINT DEFAULT 0 NOT NULL;');
        $this->addSql('UPDATE `languages` SET `is_translated` = 1 WHERE short IN (\'DE\', \'EN\', \'FR\', \'IT\', \'ES\');');
    }
    
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `languages` DROP `is_translated`');
    }
}
