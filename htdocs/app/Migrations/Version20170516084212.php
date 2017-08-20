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
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('
            CREATE TABLE page_block(
                id INT AUTO_INCREMENT NOT null,
                page_group_id INT NOT null,
                title VARCHAR(255) NOT null,
                html LONGTEXT NOT null,
                position INT DEFAULT null,
                updated_at DATETIME NOT null,
                active TINYINT(1) NOT null,
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ');

        $this->addSql('
            CREATE TABLE page_group(
                id INT AUTO_INCREMENT NOT null,
                slug VARCHAR(80) NOT null UNIQUE ,
                meta_keywords VARCHAR(255) NOT null,
                meta_description VARCHAR(255) DEFAULT null,
                meta_social LONGTEXT DEFAULT null,
                updated_at DATETIME NOT null,
                active TINYINT(1) NOT null,
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ');

        $this->addSql('ALTER TABLE page_block ADD FOREIGN KEY  (page_group_id) REFERENCES page_group(id);');
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
