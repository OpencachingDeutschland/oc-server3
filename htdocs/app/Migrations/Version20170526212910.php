<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration to add impressum and tos to the page table.
 */
class Version20170526212910 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql("            
            INSERT INTO page (slug, meta_keywords, meta_description, meta_social, updated_at, active) 
                VALUE ('impressum','','','', NOW(),1);
                
            INSERT INTO page (slug, meta_keywords, meta_description, meta_social, updated_at, active) 
                VALUE ('tos','','','',NOW(),1);
        ");
    }

    /**
     * @param Schema $schema
     *
     * @return void
     */
    public function down(Schema $schema)
    {
    }
}
