<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220823170218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'There were new user roles added. Set root user to new highest role level.';
    }

    public function up(Schema $schema): void
    {
        // table user_roles: set higher role level for user root
        $this->addSql(
            'UPDATE user_roles
                 SET role_id = 14 WHERE user_id = 107469;'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
