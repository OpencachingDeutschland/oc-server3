<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190211224434 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE `sys_menu` SET `href` = `articles.php?page=terms` WHERE id_string = `MNU_START_TOS`;');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
