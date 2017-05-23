<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170516084212 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
            CREATE TABLE page_blocks(
                id INT AUTO_INCREMENT NOT null,
                page_group_id INT NOT null,
                title VARCHAR(255) NOT null,
                html LONGTEXT NOT null,
                position INT DEFAULT null,
                last_changed DATETIME NOT null,
                active TINYINT(1) NOT null,
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ');

        $this->addSql('
            CREATE TABLE page_groups(
                id INT AUTO_INCREMENT NOT null,
                slug VARCHAR(80) NOT null,
                meta_keywords VARCHAR(255) NOT null,
                meta_description VARCHAR(255) DEFAULT null,
                meta_social LONGTEXT DEFAULT null,
                last_changed DATETIME NOT null,
                active TINYINT(1) NOT null,
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ');

        $this->addSql('ALTER TABLE page_blocks ADD FOREIGN KEY  (page_group_id) REFERENCES page_groups(id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
