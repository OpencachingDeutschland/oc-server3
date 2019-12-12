<h1>GDPR-Deletion</h1>

{if $executed}
<h2 style="color: darkgreen">Benutzer wurde erfolgreich anonymisiert!</h2>
{/if}

{if $error}
    <h2 style="color: darkgreen">Fehler beim anonymieren des Benutzers!</h2>
    <pre>{$error}</pre>
{/if}

<textarea style="width: 90%;" rows="25" readonly>
Benutzer-ID: {$userId}
Benutzername: {$username}
E-Mail:  {$email}

Persönliche Daten entfernt und Benutzer deaktiviert!

Anonymisierte Caches: {$cacheCount}
Anonymisierte Cache Logs: {$cacheLogCount}
Gelöschte Cache Bilder: {$cachePicturesCount}
Gelöschte Log Bilder: {$cacheLogPicturesCount}
</textarea>
<br/>
<br/>
{if $executed}
    <a href="adminuser.php">
        <input type="button" name="back" value="{t}Zurück{/t}" class="formbutton"/>
    </a>
{else}
    <form method="post" action="adminuser.php?action=gdpr-deletion&userid={$userId}">
        <input type="submit" name="execute" value="{t}Submit{/t}" class="formbutton"/>
    </form>
{/if}

