{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}
    <div class="content2-pagetitle">
        <img src="resource2/{$opt.template.style}/images/misc/32x32-checked.png" style="margin-right: 10px;" width="32" height="32" alt="" />Ensemble sauvons Opencaching.de
    </div>
    <div class="content-txtbox-noshade" style="padding-right: 25px;">

        <p>Merci de votre intérêt pour la manière dont vous pouvez soutenir Opencaching. Notre association exploite cette plateforme entièrement sur une base bénévole. Notre objectif est de maintenir une offre gratuite, sans publicité et indépendante pour la communauté du geocaching.</p>

        <p>Comme nous ne générons aucun revenu provenant de publicités ou de fonctions payantes, tous les coûts courants — des serveurs à l'administration — sont financés exclusivement par les dons et les cotisations de membres. Votre soutien est donc non seulement bienvenu, mais essentiel pour la survie du site.</p>

        <h2>Soutenir opencaching :</h2>
        <ul>
            <li><b>Faire un don :</b> Vous pouvez nous soutenir par un don unique ou par virement récurrent.</li>
            <li><b><a href="/articles.php?page=verein">Devenir membre</a> :</b> Rejoignez l'association en tant que membre actif ou membre de soutien, et participez activement l'avenir d'Opencaching.</li>
        </ul>

        {if $donation_active}
        <h2>Progression des dons {$donation_year}</h2>
        <p>Pour assurer le fonctionnement stable et à long terme d'Opencaching.de, des dons de {$donation_target} &euro; sont nécessaires pour {$donation_year}.</p>
        <p>État actuel au {$donation_last_updated|date_format:$opt.format.datelong} :</p>

        {include file="articles/donation_progress.tpl"}
        {/if}

        <table>
            <tr>
                <td>
                    <h2>Faire un don par virement bancaire :</h2>
                    <p>Banque : Deutsche Skatbank<br />
                        Titulaire du compte : Opencaching Deutschland<br />
                        Adresse du titulaire : 46562 Voerde (Niederrhein), Germany<br />
                        IBAN: DE91 8306 5408 0007 0075 31<br />
                        BIC (SWIFT): GEN0DEF1SLR</p>

                    <p>Ou scannez ce code QR avec votre application bancaire pour<br>remplir automatiquement les détails du virement :</p>
                    <div style="display: inline-block; text-align: center;">
                        <img src="resource2/misc/donation/oc-epc-qr-code2.png" alt="Code QR EPC pour virement bancaire" style="width: 215px; height: 215px;" />
                        <br /><small>EPC QR Code</small>
                    </div>
                </td>
                <td>
                    <h2>Faire un don par Wero :</h2>
                    <p>Titulaire du compte : Opencaching Deutschland<br />
                        E-mail : verein@opencaching.de<br />
                        Transférer dès maintenant avec Wero : <a href="https://share.weropay.eu/p/1/c/M5TWKJJcFx" target="_blank">Wero</a><br /><br /><br />
                    </p>

                    <p>Ou scanner ce code QR avec votre application Wero pour<br>remplir automatiquement les détails du virement :</p>
                    <div style="display: inline-block; text-align: center;">
                        <img src="resource2/misc/donation/oc-wero-qr-code.png" alt="Code QR Wero pour le transfert" style="width: 215px; height: 215px;" />
                        <br /><small>Wero QR-Code</small>
                    </div>
                </td>
            </tr>
        </table>

        <h2>Utilisation de votre don</h2>
        <p>Tous les dons sont principalement utilisés pour l'exploitation des sites web et dans une moindre mesure pour l'administration de l'association.</p>
    </div>
