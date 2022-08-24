<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to add impressum and tos to the page table.
 */
final class Version20170526212910 extends AbstractMigration
{
    public function up(Schema $schema): void
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
    
    public function down(Schema $schema): void
    {
    }
}
