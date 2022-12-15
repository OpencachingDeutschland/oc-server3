<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221213143312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create additional column in table cache_type for OC4 SVG image names';
    }

    public function up(Schema $schema): void
    {
        // remove constraint. Otherwise altering table is not possible.
        $this->addSql(
                'ALTER TABLE cache_type DROP KEY IF EXISTS name;'
        );

        $this->addSql(
                'ALTER TABLE cache_type ADD COLUMN IF NOT EXISTS svg_name varchar(11);'
        );

        $this->addSql('
            UPDATE cache_type SET svg_name = \'unknown\' WHERE id=1;
            UPDATE cache_type SET svg_name = \'traditional\' WHERE id=2;
            UPDATE cache_type SET svg_name = \'multi\' WHERE id=3;
            UPDATE cache_type SET svg_name = \'virtual\' WHERE id=4;
            UPDATE cache_type SET svg_name = \'webcam\' WHERE id=5;
            UPDATE cache_type SET svg_name = \'event\' WHERE id=6;
            UPDATE cache_type SET svg_name = \'mystery\' WHERE id=7;
            UPDATE cache_type SET svg_name = \'mathe\' WHERE id=8;
            UPDATE cache_type SET svg_name = \'moving\' WHERE id=9;
            UPDATE cache_type SET svg_name = \'drivein\' WHERE id=10;
        ');

        // re-add constraint.
        $this->addSql(
                'ALTER TABLE cache_type ADD KEY (name);'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
                'ALTER TABLE cache_type DROP svg_name;'
        );
    }
}
