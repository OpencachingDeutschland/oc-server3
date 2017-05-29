<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170526221753 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO page_blocks (page_group_id, title, html, position, updated_at, active) VALUES (1, \'Impressum\', \'<div style="float: left; width: 50%">
            <h2>Angaben gem&auml;&szlig; &sect; 5 TMG:</h2>
            <address>
                Opencaching Deutschland e. V.<br/>
                c/o Mirco Baumann<br/>
                Am Sternbusch 7<br/>
                46562 Voerde
            </address>

            <h2>Vertreten durch:</h2>
            <dl>
                <dt>
                    <del>Michael Vaahsen</del>
                </dt>
                <dd>1. Vorsitzender<br>
                    <i>Amtsniederlegung 31.12.2016*</i></dd>

                <dt>Mirco Baumann</dt>
                <dd>2. Vorsitzender</dd>

                <dt>Christoph Convent</dt>
                <dd>Kassenwart</dd>
            </dl>
        </div>

        <div style="float: left; width: 50%">
            <h2>Registereintrag:</h2>
            <p>Eingetragen im Vereinsregister.<br/>
            <dl>
                <dt>Registergericht Bad Homburg v. d. H.</dt>
                <dt>Registernummer 2054</dt>
            </dl>

            <h2>Kontakt:</h2>
            <dl>
                <dt>Telefon +49 (0)172-2092626</dd>

                <dt>E-Mail verein@opencaching.de</dd>
            </dl>
        </div>\', 1, \'2017-05-23 17:32:32\', 1);');
        $this->addSql('INSERT INTO page_blocks (page_group_id, title, html, position, updated_at, active) VALUES (1, \'Haftung für Inhalte\', \'<blockquote>Als Diensteanbieter sind wir gem&auml;&szlig; &sect; 7 Abs.1 TMG f&uuml;r eigene Inhalte auf diesen Seiten
                nach den allgemeinen Gesetzen
                verantwortlich. Nach &sect;&sect; 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet,
                &uuml;bermittelte oder gespeicherte fremde
                Informationen zu &uuml;berwachen oder nach Umst&auml;nden zu forschen, die auf eine rechtswidrige T&auml;tigkeit
                hinweisen.
    </blockquote>
    <blockquote>Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen
                bleiben
                hiervon unber&uuml;hrt.
                Eine diesbez&uuml;gliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten
                Rechtsverletzung m&ouml;glich.
                Bei Bekanntwerden von
                entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.
    </blockquote>\', 2, \'2017-05-23 17:33:12\', 1);');
        $this->addSql('INSERT INTO page_blocks (page_group_id, title, html, position, updated_at, active) VALUES (1, \'Haftung für Links\', \'<blockquote>Unser Angebot enth&auml;lt Links zu externen Webseiten Dritter, auf deren
                Inhalte wir keinen Einfluss haben. Deshalb k&ouml;nnen wir f&uuml;r diese
                fremden Inhalte auch keine Gew&auml;hr &uuml;bernehmen. F&uuml;r die Inhalte
                der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der
                Seiten verantwortlich. Die verlinkten Seiten wurden zum Zeitpunkt der
                Verlinkung auf m&ouml;gliche Rechtsverst&ouml;&szlig;e &uuml;berpr&uuml;ft.
                Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar.
    </blockquote>
    <blockquote>Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch
                ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei
                Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend
                entfernen.
    </blockquote>\', 3, \'2017-05-23 17:35:19\', 1);');
        $this->addSql('INSERT INTO page_blocks (page_group_id, title, html, position, updated_at, active) VALUES (1, \'Urheberrecht\', \'<blockquote>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen
                Urheberrecht. Die Vervielf&auml;ltigung, Bearbeitung, Verbreitung und jede
                Art der Verwertung au&szlig;erhalb der Grenzen des Urheberrechtes
                bed&uuml;rfen der schriftlichen Zustimmung des jeweiligen Autors bzw.
                Erstellers. Downloads und Kopien dieser Seite sind nur f&uuml;r den privaten,
                nicht kommerziellen Gebrauch gestattet.
    </blockquote>
    <blockquote>Soweit die Inhalte auf dieser Seite nicht vom Betreiber
                erstellt wurden, werden die Urheberrechte Dritter
                beachtet. Insbesondere werden Inhalte Dritter als solche
                gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten wir um einen
                entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend
                entfernen.
    </blockquote>

    <p>Quelle:
        <a href="https://www.erecht24.de">https://www.e-recht24.de</a>
    </p>\', 4, \'2017-05-23 17:35:56\', 1);');
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
