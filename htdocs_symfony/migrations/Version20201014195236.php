<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201014195236 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create security tables';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
            create table security_roles
            (
                id int auto_increment,
                role varchar(100) not null,
                constraint security_roles_pk
                    primary key (id)
            );
            
            create unique index security_roles_role_uindex
                on security_roles (role);
        ');

        $this->addSql('
            create table security_role_hierarchy
            (
                role_id int not null,
                sub_role_id int not null,
                constraint security_role_hierarchy_pk
                    primary key (role_id, sub_role_id),
                constraint security_role_hierarchy_pk_2
                    unique (role_id, sub_role_id),
                constraint security_role_hierarchy_security_roles_id_fk
                    foreign key (role_id) references security_roles (id),
                constraint security_role_hierarchy_security_roles_id_fk_2
                    foreign key (sub_role_id) references security_roles (id)
            );
        ');

        $this->addSql('
            INSERT INTO security_roles (role)
            VALUES (\'ROLE_USER\'),
                   (\'ROLE_TEAM\'),
                   (\'ROLE_SUPPORT_CLEAN\'),
                   (\'ROLE_SUPPORT_MAINTAIN\'),
                   (\'ROLE_SUPPORT_HEAD\'),
                   (\'ROLE_SOCIAL\'),
                   (\'ROLE_SOCIAL_HEAD\'),
                   (\'ROLE_DEVELOPER_CORE\'),
                   (\'ROLE_DEVELOPER_CONTRIBUTE\'),
                   (\'ROLE_DEVELOPER_HEAD\'),
                   (\'ROLE_ADMIN\'),
                   (\'ROLE_SUPER_ADMIN\');
        ');

        $this->addSql('
            INSERT INTO security_role_hierarchy (role_id, sub_role_id)
            VALUES (2, 1),
                   (3, 2),
                   (4, 2),
                   (6, 2),
                   (8, 2),
                   (5, 3),
                   (5, 4),
                   (10, 8),
                   (10, 9),
                   (11, 5),
                   (11, 7),
                   (11, 10),
                   (12, 11);
        ');

        $this->addSql('
            create table user_roles
            (
                id int auto_increment,
                user_id int(10) unsigned not null,
                role_id int(11) not null,
                constraint user_roles_pk
                    primary key (id),
                constraint user_roles_pk_2
                    unique (user_id),
                constraint user_roles_pk_3
                    unique (role_id),
                constraint user_roles_security_roles_id_fk
                    foreign key (role_id) references security_roles (id),
                constraint user_roles_user_user_id_fk
                    foreign key (user_id) references user (user_id)
            );
            
            create unique index user_roles_user_id_role_id_uindex
                on user_roles (user_id, role_id);
        ');
    }
}
