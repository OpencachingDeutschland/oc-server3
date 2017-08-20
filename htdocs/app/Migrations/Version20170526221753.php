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
        $this->addSql('INSERT INTO page_block (page_group_id, title, html, position, updated_at, active) VALUES (1, \'Impressum\', \'<div style="float: left; width: 50%">
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
        $this->addSql(', 1);\'');
        $this->addSql(', 1);\'');
        $this->addSql(', 1);\'');
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
