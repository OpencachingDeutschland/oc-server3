<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811184533 extends AbstractMigration
{
    public function getDescription()
    : string
    {
        return 'adjust column to match live-DB';
    }

    public function up(Schema $schema)
    : void {
        // change caches/wp_oc to varchar(7)
        $this->addSql(
            'ALTER TABLE caches MODIFY wp_oc varchar (7) UNIQUE ;'
        );
    }

    public function down(Schema $schema)
    : void {
    }
}
