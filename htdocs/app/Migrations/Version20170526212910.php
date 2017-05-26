<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170526212910 extends AbstractMigration
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

        $this->addSql("
            UPDATE sys_menu SET href = 'page/impressum' WHERE menustring ='MNU_START_IMPRINT';
            UPDATE sys_menu SET href = 'page/tos' WHERE menustring ='MNU_START_TOS';
            
            INSERT INTO page_groups (slug, meta_keywords, meta_description, meta_social, last_changed, active) 
                VALUE ('impressum','','','',now(),1);
                
            INSERT INTO page_groups (slug, meta_keywords, meta_description, meta_social, last_changed, active) 
                VALUE ('tos','','','',now(),1);
        ");
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
