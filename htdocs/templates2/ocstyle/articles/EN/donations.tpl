{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}
    <div class="content2-pagetitle">
        <img src="resource2/{$opt.template.style}/images/misc/32x32-checked.png" style="margin-right: 10px;" width="32" height="32" alt="" />Keep Opencaching.de Alive Together
    </div>
    <div class="content-txtbox-noshade" style="padding-right: 25px;">

        <p>Thank you for your interest in how you can support Opencaching. Our association operates this platform entirely on a volunteer basis. Our goal is to maintain a free, ad-free, and independent offering for the caching community.</p>

        <p>Since we generate no revenue from advertising or paid features, all ongoing costs – from servers to administration – are funded exclusively through donations and membership fees. Your support is therefore not only welcome but essential for the continued existence of the site.</p>

        <h2>How you can support Opencaching:</h2>
        <ul>
            <li><b>Donate:</b> You can support us with a one-time donation or via recurring transfer.</li>
            <li><b><a href="/articles.php?page=verein">Become a member</a>:</b> Become a new active or supporting member of the association and actively support the future of Opencaching.</li>
        </ul>

        {if $donation_active}
        <h2>Donation Progress {$donation_year}</h2>
        <p>To ensure the stable and long-term operation of Opencaching.de, donations of {$donation_target} &euro; are needed for {$donation_year}.</p>
        <p>Current status as of {$donation_last_updated|date_format:$opt.format.datelong}:</p>

        {include file="articles/donation_progress.tpl"}
        {/if}

        <table>
            <tr>
                <td>
                    <h2>Donate by bank transfer:</h2>
                    <p>Bank: Deutsche Skatbank<br />
                        Account owner: Opencaching Deutschland<br />
                        Owner address: 46562 Voerde (Niederrhein), Germany<br />
                        IBAN: DE91 8306 5408 0007 0075 31<br />
                        BIC (SWIFT): GEN0DEF1SLR</p>

                    <p>Or scan this QR code with your banking app to fill in the<br>transfer details automatically:</p>
                    <div style="display: inline-block; text-align: center;">
                        <img src="resource2/misc/donation/oc-epc-qr-code2.png" alt="EPC QR Code for bank transfer" style="width: 215px; height: 215px;" />
                        <br /><small>EPC QR Code</small>
                    </div>
                </td>
                <td>
                    <h2>Donate by Wero:</h2>
                    <p>Account owner: Opencaching Deutschland<br />
                        Email: verein@opencaching.de<br />
                        Donate now by Wero: <a href="https://share.weropay.eu/p/1/c/M5TWKJJcFx" target="_blank">Wero</a><br /><br /><br />
                    </p>

                    <p>Or scan this QR code with your Wero app to fill in the<br>transfer details automatically:</p>
                    <div style="display: inline-block; text-align: center;">
                        <img src="resource2/misc/donation/oc-wero-qr-code.png" alt="Wero-QR-Code with transfer data" style="width: 215px; height: 215px;" />
                        <br /><small>Wero QR-Code</small>
                    </div>
                </td>
            </tr>
        </table>

        <h2>Use of your donation</h2>
        <p>All donations go primarily to the operation of the websites and to a small extent to the administration of the association.</p>
    </div>
