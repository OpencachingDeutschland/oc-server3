{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}
    <div class="content2-pagetitle">
        <img src="resource2/{$opt.template.style}/images/misc/32x32-checked.png" style="margin-right: 10px;" width="32" height="32" alt="" />Gemeinsam Opencaching.de erhalten
    </div>
    <div class="content-txtbox-noshade" style="padding-right: 25px;">

        <p>Vielen Dank, dass du dich dafür interessierst, wie du Opencaching unterstützen kannst. Unser Verein betreibt diese Plattform rein ehrenamtlich. Unser Ziel ist es, ein werbefreies, kostenloses und unabhängiges Angebot für die Caching-Community zu erhalten.</p>

        <p>Da wir keine Einnahmen aus Werbung oder bezahlten Funktionen generieren, finanzieren sich alle laufenden Kosten – von den Servern bis zur Verwaltung – ausschließlich durch Spenden und Mitgliedsbeiträge. Deine Unterstützung ist daher nicht nur willkommen, sondern essenziell für das Weiterbestehen der Seite.</p>

        <h2>Wie du Opencaching unterstützen kannst:</h2>
        <ul>
            <li><b>Spenden:</b> Du kannst uns einmalig oder mit einem Dauerauftrag unterstützen.</li>
            <li><b><a href="/articles.php?page=verein">Mitglied werden</a>:</b> Werde neues Aktiv- oder Fördermitglied im Verein und unterstütze uns aktiv bei der Zukunft von Opencaching.</li>
        </ul>

        {if $donation_active}
        <h2>Spendenfortschritt {$donation_year}</h2>
        <p>Um einen stabilen und langfristigen Betrieb von OpenCaching.de sicherzustellen, werden für das Jahr {$donation_year} Spenden über {$donation_target} &euro; benötigt.</p>
        <p>Der aktuelle Stand per {$donation_last_updated|date_format:$opt.format.datelong}:</p>

        {include file="articles/donation_progress.tpl"}
        {/if}

        <table>
            <tr>
                <td>
                    <h2>Per &Uuml;berweisung spenden:</h2>
                    <p>Bank: Deutsche Skatbank<br />
                        Kontoinhaber: Opencaching Deutschland<br />
                        Inhaberadresse: 46562 Voerde (Niederrhein), Deutschland<br />
                        IBAN: DE91 8306 5408 0007 0075 31<br />
                        BIC (SWIFT): GEN0DEF1SLR</p>

                    <p>Oder scanne diesen QR-Code mit deiner Banking-App, um die<br>&Uuml;berweisungsdaten automatisch auszuf&uuml;llen:</p>
                    <div style="display: inline-block; text-align: center;">
                        <img src="resource2/misc/donation/oc-epc-qr-code2.png" alt="EPC-QR-Code für Überweisung" style="width: 215px; height: 215px;" />
                        <br /><small>EPC-QR-Code</small>
                    </div>
                </td>
                <td>
                    <h2>Per Wero spenden:</h2>
                    <p>Kontoinhaber: Opencaching Deutschland<br />
                        Email: verein@opencaching.de<br />
                        Jetzt mit Wero überweisen: <a href="https://share.weropay.eu/p/1/c/M5TWKJJcFx" target="_blank">Wero</a><br /><br /><br />
                    </p>

                    <p>Oder scanne diesen QR-Code mit deiner Wero-App, um die<br>&Uuml;berweisungsdaten automatisch auszuf&uuml;llen:</p>
                    <div style="display: inline-block; text-align: center;">
                        <img src="resource2/misc/donation/oc-wero-qr-code.png" alt="Wero-QR-Code für Überweisung" style="width: 215px; height: 215px;" />
                        <br /><small>Wero QR-Code</small>
                    </div>
                </td>
            </tr>
        </table>

        <h2>Verwendung deiner Spende</h2>
        <p>Alle Spenden fließen hauptsächlich in den Betrieb der Webseiten und in geringem Umfang in die Verwaltung des Vereins.</p>
    </div>
