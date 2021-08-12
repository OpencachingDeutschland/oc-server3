<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210810190121 extends AbstractMigration
{
    public function getDescription()
    : string
    {
        return 'Create support related tables';
    }

    public function up(Schema $schema)
    : void {
        $this->addSql(
            '
                CREATE TABLE IF NOT EXISTS support_bonuscaches
                (
                    id                     int(10) UNSIGNED AUTO_INCREMENT,
                    wp_oc                  varchar(7) NOT NULL,
                    is_bonus_cache         tinyint(1) DEFAULT 0 NOT NULL COMMENT \'(ja oder nein. Vermerkt, ob dieser Cache vom Support als Bonuscache markiert wurde\',
                    belongs_to_bonus_cache varchar(7) NULL COMMENT \'ist leer oder enthält einen OC - Wegpunkt . Der Wegpunkt wird vom Support eingepflegt.\',
                    CONSTRAINT support_bonuscaches_id_uindex UNIQUE (id)
                )
                COMMENT \'Support kann OC-Listings als Bonuscaches bzw. als zu-Bonuscaches-zugehörig markieren\';
                
                ALTER TABLE support_bonuscaches
                    ADD PRIMARY KEY (id);
        '
        );

        $this->addSql(
            '
                        CREATE TABLE IF NOT EXISTS support_listing_comments
                        (
                            id                     int(10) UNSIGNED AUTO_INCREMENT,
                            wp_oc                  varchar(7) NOT NULL COMMENT \'OC Wegpunkt des Listings\',
                            comment                longtext COMMENT \'Feld für Notizen des Supports zu diesem OC-Listing\',
                            comment_created        datetime NULL COMMENT \'Datum der Erstellung\',
                            comment_last_modified  datetime NULL COMMENT \'Datum der letzten comment-Änderung\',
                            CONSTRAINT support_listing_comments_id_uindex UNIQUE (id)
                        )
                        COMMENT \'Kommentare seitens des Supports zu OC-Listings speichern\';
        
                        ALTER TABLE support_listing_comments
                            ADD PRIMARY KEY (id);
                '
        );

        $this->addSql(
            '
                        CREATE TABLE IF NOT EXISTS support_listing_infos
                        (
                            id                           int(10)     UNSIGNED AUTO_INCREMENT,
                            wp_oc                        varchar(7)  NOT NULL COMMENT \'ID des OC-Listings, auf die sich der Eintrag bezieht\',
                            node_id                      tinyint(3)  NOT NULL COMMENT \'Zeigt an, von welchem Node diese Infos stammen. Siehe Tabelle nodes\',
                            node_owner_id                varchar(36) NULL COMMENT \'generische User-ID des Nodes\',
                            node_listing_id              varchar(36) NULL COMMENT \'generische ID des Listings. Wird diese Info benötigt?\',
                            node_listing_wp              varchar(10) NULL COMMENT \'Wegpunkt des Listings\',
                            node_listing_name            varchar(255) NULL COMMENT \'Listingtitel\',
                            node_listing_size            tinyint(3)  DEFAULT 0 NULL COMMENT \'Behältergröße. Wird soweit möglich auf die OC-Behältergrößen umgemünzt oder als other deklariert. Siehe Tabelle cache_size\',
                            node_listing_difficulty      tinyint(3)  DEFAULT 0 NULL COMMENT \'D-Wertung\',
                            node_listing_terrain         tinyint(3)  DEFAULT 0 NULL COMMENT \'T-Wertung\',
                            node_listing_coordinates_lon double      DEFAULT 0 NULL COMMENT \'Ankerkoordinaten Nord\',
                            node_listing_coordinates_lat double      DEFAULT 0 NULL COMMENT \'Ankerkoordinaten Ost\',
                            node_listing_archived        tinyint(1)  DEFAULT 0 NOT NULL COMMENT \'Wurde das Listing auf dem Fremdnode bereits archiviert?\',
                            last_modified                datetime    NOT NULL COMMENT \'Datum der letzten Änderung dieses Tabelleneintrags\',
                            importstatus                 tinyint(3)  NULL COMMENT \'Status des Tabelleneintrags, z.B. ob der Import bereits verarbeitet wurde\',
                            CONSTRAINT support_listing_infos_id_uindex UNIQUE (id)
                        )
                        COMMENT \'Informationen zu Listings von Fremdnodes speichern\';
        
                        ALTER TABLE support_listing_infos
                            ADD PRIMARY KEY (id);
                '
        );

        $this->addSql(
            '
                        CREATE TABLE IF NOT EXISTS support_user_comments
                        (
                            id                           int(10)      UNSIGNED AUTO_INCREMENT,
                            oc_user_id                   int(10)      UNSIGNED NOT NULL COMMENT \'OC User-ID\',
                            comment                      longtext     NULL COMMENT \'Feld für Notizen des Supports zu diesem OC-User\',
                            comment_created              datetime     NOT NULL COMMENT \'Datum der Erstellung\',
                            comment_last_modified        datetime     NOT NULL COMMENT \'Datum der letzten comment-Änderung\',
                            CONSTRAINT support_user_comments_id_uindex UNIQUE (id)
                        )
                        COMMENT \'Kommentare zu OC-Usern speichern\';
        
                        ALTER TABLE support_user_comments
                            ADD PRIMARY KEY (id);
                '
        );

        $this->addSql(
            '
                        CREATE TABLE IF NOT EXISTS support_user_relations
                        (
                            id                           int(10)      UNSIGNED AUTO_INCREMENT,
                            oc_user_id                   int(10)      UNSIGNED NOT NULL COMMENT \'OC User-ID\',
                            node_id                      tinyint(3)   NOT NULL COMMENT \'Zeigt an, von welchem Node diese Infos stammen. Siehe Tabelle nodes\',
                            node_user_id                 varchar(36)  NULL COMMENT \'GC/Fremdnode User-ID, nicht der Username! Auf diese ID muss immer verlinkt werden können, auch wenn sich der Ownername ändert.\',
                            node_username                varchar(60)  NULL COMMENT \'Name des Users auf GC/Fremdnode.\',
                            CONSTRAINT support_user_relations_id_uindex UNIQUE (id)
                        )
                        COMMENT \'Kommentare zu OC-Usern speichern\';
        
                        ALTER TABLE support_user_relations
                            ADD PRIMARY KEY (id);
                '
        );
    }

    public
    function down(
        Schema $schema
    )
    : void {
        $schema->dropTable('support_bonuscaches');
        $schema->dropTable('support_listing_comments');
        $schema->dropTable('support_listing_infos');
        $schema->dropTable('support_user_comments');
        $schema->dropTable('support_user_relations');
    }
}
