<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210929190700 extends AbstractMigration
{
    public function getDescription()
    : string
    {
        return 'modify existing support_listing_infos table to match current requirements';
    }

    public function up(Schema $schema)
    : void {
        // table support_listing_info: switch columns lon and lat (lat is north and should come first)
        $this->addSql(
            'ALTER TABLE support_listing_infos MODIFY node_listing_coordinates_lat double DEFAULT 0 NULL COMMENT \'Ankerkoordinaten Nord\' AFTER node_listing_terrain;'
        );

        // table support_listing_info: change column node_listing_size to string (Groundspeak GPX import information are string, not int)
        $this->addSql(
            'ALTER TABLE support_listing_infos MODIFY node_listing_size varchar(36) NULL COMMENT \'Behältergröße. Wird soweit möglich auf die OC-Behältergrößen umgemünzt oder als other deklariert. Siehe Tabelle cache_size\' AFTER node_listing_terrain;'
        );

        // table support_listing_info: add column node_owner_name (alternative for _owner_id, which is missing in Groundspeak GPX import)
        $this->addSql(
            'ALTER TABLE support_listing_infos ADD node_owner_name varchar(60) NULL COMMENT \'Name des Users auf GC/Fremdnode.\' AFTER node_owner_id;'
        );

        // table support_listing_info: add column node_listing_deactivated (Deactivated info is content of Groundspeak GPX import)
        $this->addSql(
            'ALTER TABLE support_listing_infos ADD node_listing_available tinyint(1) DEFAULT 0 NOT NULL COMMENT \'Ist das Listing noch aktiv?\' AFTER node_listing_coordinates_lon;'
        );
    }

    public function down(Schema $schema)
    : void {
    }
}
