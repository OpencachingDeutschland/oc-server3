<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160607213541 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE field_note (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, geocache_id INT DEFAULT NULL, type SMALLINT NOT NULL, date DATETIME NOT NULL, text VARCHAR(255) DEFAULT NULL, INDEX IDX_DC7193AEA76ED395 (user_id), INDEX IDX_DC7193AE67030974 (geocache_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE = MyISAM');
        $this->addSql('ALTER TABLE field_note ADD CONSTRAINT FK_DC7193AEA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE field_note ADD CONSTRAINT FK_DC7193AE67030974 FOREIGN KEY (geocache_id) REFERENCES caches (cache_id) ON DELETE CASCADE');

        // add menu item in legacy template
        $this->addSql("INSERT INTO `sys_menu` (`id`, `id_string`, `title`, `title_trans_id`, `menustring`, `menustring_trans_id`, `access`, `href`, `visible`, `parent`, `position`, `color`, `sitemap`, `only_if_parent`)
                       VALUES (109, 'MNU_MYPROFILE_FIELD_NOTES', 'Field Notes', 0, 'Field Notes', 0, 0, '/field-notes/', 1, 9, 11, '', 1, NULL)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE field_note');
    }
}
