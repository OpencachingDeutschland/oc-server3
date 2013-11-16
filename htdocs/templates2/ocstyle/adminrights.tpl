{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}

<div class="content2-pagetitle">
    <img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="World" />
    {t}OC-Admins{/t}
</div>

<div class="content2-container">

    <p>&nbsp;</p>

    <form method="post" action="adminrights.php">
        <input type="hidden" name="action" value="searchuser" />
        <p style="line-height: 1.6em;"><strong>{t}Username or email address{/t}:</strong> &nbsp;<input type="text" name="username" size="30" value="{$username|escape}" /></p>

        {if $error=='userunknown'}
            <p style="line-height: 1.6em; color: red; font-weight: bold;">{t}Username unknown{/t}</p>
        {/if}


        <p style="line-height: 1.6em;"><input type="submit" name="find" value="{t}Submit{/t}" class="formbutton" onclick="submitbutton('find')" /></p>


    </form>
    {if $showdetails==true}
        <form method="post" action="adminrights.php">
            <div class="content2-pagetitle">
                <img src="resource2/{$opt.template.style}/images/misc/32x32-tools.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="World" />
                {t}Useraccount details{/t}
            </div>

            <table class="narrowtable">
                <tr>
                    <td>{t}Username:{/t}</td>
                    <td><a href="viewprofile.php?userid={$user.user_id|escape}" target="_blank">{$user.username|escape}</a></td>
                </tr>
                <tr>
                    <td>{t}User-ID:{/t}</td>
                    <td><a href="viewprofile.php?userid={$user.user_id|escape}" target="_blank">{$user.user_id|escape}</a></td>
                </tr>

                <tr>
                    <td>{t}User rights{/t}</td>
                    {if !$havenoperms}
                    {foreach from=$user.rights item=right key=permid}
                        <td>{$right}  <a href="adminrights.php?action=removeperms&userid={$user.user_id|escape}&permid={$permid}" > {t}remove{/t}</a> </td>
                    </tr>
                    <tr>
                        <td></td>
                    {/foreach}
                    {else}
                        <td>{t}The selected user does not have any administrative rights !{/t}</td>
                    {/if}
                </tr>

            </table>

        </form> 
        <br> </br>
        <form method="post" action="adminrights.php?userid={$user.user_id|escape}">
            <input type="hidden" name="action" value="addperm" />
            {if !$haveallperms}
            <table class="narrowtable">
                <tr>
                    <td>
                        {t}Add Permission: {/t} 
                    </td>
                    <td>
                        <select name = "permid" >
                            {foreach from=$user.norights item=right key=permid}
                                <option value={$permid}>{$right}</option> 
                            {/foreach}
                    </td>
                    <td>
                    <p style="line-height: 1.6em;"><input type="submit" name="add" value="{t}Add{/t}" class="formbutton" onclick="submitbutton('add')" /></p>
                    
                    </td>
                        
                    </select>
                </tr>
            </table>
                    {/if}
        </form>
    {/if}
</div>