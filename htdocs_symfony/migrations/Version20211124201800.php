<?php

declare(strict_types=1);

namespace OcMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211124201800 extends AbstractMigration
{
    public function getDescription()
    : string
    {
        return 'add missing ROLEs';
    }

    public function up(Schema $schema)
    : void {
        // remove constraints which prevent deleting rows
        $this->addSql(
            'ALTER TABLE security_role_hierarchy DROP FOREIGN KEY IF EXISTS security_role_hierarchy_security_roles_id_fk_2;
                 ALTER TABLE security_role_hierarchy DROP FOREIGN KEY IF EXISTS security_role_hierarchy_security_roles_id_fk;
                 ALTER TABLE user_roles DROP FOREIGN KEY IF EXISTS user_roles_security_roles_id_fk;'
        );

        // table security_roles: add missing ROLEs
        $this->addSql(
            'DELETE FROM security_roles;
                 INSERT INTO security_roles (id, role) VALUES
                    (1, \'ROLE_USER\'),
                    (2, \'ROLE_TEAM\'),
                    (3, \'ROLE_SUPPORT_TRAINEE\'),
                    (4, \'ROLE_SUPPORT\'),
                    (5, \'ROLE_SUPPORT_MAINTAIN\'),
                    (6, \'ROLE_SUPPORT_HEAD\'),
                    (7, \'ROLE_SOCIAL_TRAINEE\'),
                    (8, \'ROLE_SOCIAL\'),
                    (9, \'ROLE_SOCIAL_HEAD\'),
                    (10, \'ROLE_DEVELOPER_CORE\'),
                    (11, \'ROLE_DEVELOPER_CONTRIBUTOR\'),
                    (12, \'ROLE_DEVELOPER_HEAD\'),
                    (13, \'ROLE_ADMIN\'),
                    (14, \'ROLE_SUPER_ADMIN\');'
        );

        // table security_role_hierarchy: add missing relations between roles
        $this->addSql(
            'DELETE FROM security_role_hierarchy;
                 INSERT INTO security_role_hierarchy (role_id, sub_role_id) VALUES
                    (2, 1),
                    (3, 2),
                    (4, 3),
                    (5, 4),
                    (6, 5),
                    (7, 2),
                    (8, 7),
                    (9, 8),
                    (10, 5),
                    (10, 8),
                    (10, 2),
                    (11, 2),
                    (12, 10),
                    (12, 11),
                    (13, 6),
                    (13, 9),
                    (13, 12),
                    (14, 13);'
        );

        // re-add constraints which prevented deleting rows
        $this->addSql(
            'ALTER TABLE security_role_hierarchy ADD CONSTRAINT security_role_hierarchy_security_roles_id_fk FOREIGN KEY (role_id) REFERENCES security_roles (id);
                 ALTER TABLE security_role_hierarchy ADD CONSTRAINT security_role_hierarchy_security_roles_id_fk_2 FOREIGN KEY (sub_role_id) REFERENCES security_roles (id);
                 ALTER TABLE user_roles ADD CONSTRAINT user_roles_security_roles_id_fk FOREIGN KEY (role_id) REFERENCES security_roles (id);'
        );
    }

    public function down(Schema $schema)
    : void {
    }
}
