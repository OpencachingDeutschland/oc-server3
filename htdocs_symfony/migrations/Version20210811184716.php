<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811184716 extends AbstractMigration
{
    public function getDescription()
    : string
    {
        return 'create tables existing in test-DB,but not in live-DB';
    }

    public function up(Schema $schema)
    : void {
        // add mapresult
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS mapresult (
                    query_id     int(10)  UNSIGNED NOT NULL,
                    date_created datetime NOT NULL,
                    PRIMARY KEY (query_id)
                );
                CREATE INDEX date_created ON mapresult(date_created);'
        );

        // add mapresult_data
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS mapresult_data (
                    query_id int(10) UNSIGNED NOT NULL,
                    cache_id int(10) UNSIGNED NOT NULL,
                    PRIMARY KEY (query_id, cache_id)
                );'
        );

        // add oc_migrations
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS oc_migrations (
                    version        varchar(191) NOT NULL PRIMARY KEY,
                    executed_at    datetime     NULL,
                    execution_time int(11)      NULL
                );'
        //                COLLATE = utf8_unicode_ci;' //?
        );

        // add security_role_hierarchy
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS security_role_hierarchy (
                    role_id     int(11) NOT NULL,
                    sub_role_id int(11) NOT NULL,
                    CONSTRAINT security_role_hierarchy_pk_2 UNIQUE (role_id, sub_role_id),
                    CONSTRAINT security_role_hierarchy_security_roles_id_fk FOREIGN KEY (role_id) REFERENCES security_roles(id),
                    CONSTRAINT security_role_hierarchy_security_roles_id_fk_2 FOREIGN KEY (sub_role_id) REFERENCES security_roles(id),
                    PRIMARY KEY (role_id, sub_role_id)
                );'
        );

        // add security_roles
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS security_roles (
                    id   int(11) AUTO_INCREMENT,
                    role varchar(100) NOT NULL,
                    CONSTRAINT security_roles_role_uindex UNIQUE (role),
                    PRIMARY KEY (id)
                );'
        );

        // add user_roles
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS user_roles (
                    id      int(11) AUTO_INCREMENT,
                    user_id int(10) UNSIGNED NOT NULL,
                    role_id int(11) NOT NULL,
                    CONSTRAINT user_roles_user_id_role_id_uindex UNIQUE (user_id, role_id),
                    CONSTRAINT user_roles_security_roles_id_fk FOREIGN KEY (role_id) REFERENCES security_roles(id),
                    CONSTRAINT user_roles_user_user_id_fk FOREIGN KEY (user_id) REFERENCES user(user_id),
                    PRIMARY KEY (id)
                );'
        );
    }

    public function down(Schema $schema)
    : void {
    }
}
