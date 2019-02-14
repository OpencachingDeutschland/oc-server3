<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190211224434 extends AbstractMigration
{
    public function up(Schema $schema)
    : void {
        $this->addSql('UPDATE "sys_menu" SET "href" = "articles.php?page=terms" WHERE id_string = "MNU_START_TOS";');

        $this->addSql(
            'CREATE TABLE core_hq_message (
           id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
           start TIMESTAMP NULL,
           end TIMESTAMP NULL,
           type VARCHAR(50) NULL,
           message TEXT NULL
           );'
        );

        $this->addSql(
            '
            INSERT INTO "core_hq_message"
            ("start", "end", "type", "message")
        VALUES (
            "2019-02-15 00:00:00.0",
            "2019-03-30 00:00:00.0",
            "primary",
            `<p class="lead">Hinweis:</p>
            <p style="text-justify: auto">Am <b>15.02.2019</b> haben wir unsere <a href="/article.php?page=terms">Nutzungsbedingungen</a> aktualisiert.
            Als Nutzer hat man das Recht, die neuen Nutzungsbedingungen abzulehnen. Dies hat dann zur Folge, das unsere
            Dienste nicht mehr genutzt werden können und ggfls. der Account gesperrt oder gelöscht werden kann.<br>
            Die Änderungen gelten als akzeptiert, wenn der Nutzer diese nicht innerhalb von 14 Tagen ablehnt und darüber
            hinaus das Angebot weiter nutzt. Wenn der Nutzer die Dienste im Rahmen der neuen Fassung der Vereinbarungen
            nicht weiter nutzen möchte, kann der Nutzer sein Konto jederzeit kündigen.
            Für Fragen steht der Support (<a href="https://opencaching.atlassian.net/servicedesk/customer/portal/2/group/-1" target="_blank" rel="nofollow">Kontakt</a>) und dieser
            <a hre="https://blog.opencaching.de/2019/02/aktualisierung-der-nutzungsbedingungen/" target="_blank" rel="nofollow">Blogbeitrag</a> als Erläuterung zur Verfügung.</p>`)
            '
        );
    }

    public function down(Schema $schema)
    : void {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
