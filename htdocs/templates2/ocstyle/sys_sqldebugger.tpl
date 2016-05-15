{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* no - OCSTYLE *}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>
            {t}SQL Debugger{/t} -
            {if ($opt.template.title=="")}
                {$opt.page.name|escape}
            {else}
                {$opt.template.title|escape} - {$opt.page.name|escape}
            {/if}
        </title>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta http-equiv="Content-Style-Type" content="text/css" />
        <meta http-equiv="Content-Language" content="{$opt.template.locale}" />
        <meta http-equiv="gallerimg" content="no" />
        <link rel="SHORTCUT ICON" href="favicon.ico" />
        <style type="text/css">
        <!--
            {literal}
                .sqlno
                {
                    font-size:medium;
                }

                .sqlcommand
                {
                }

                .selrows
                {
                    margin-bottom:15px;
                }

                .firstresultrow
                {
                }

                .result
                {
                    display:none;
                }

                .explain
                {
                }

                .runtime
                {
                }

                .affectedrows
                {
                }

                .allruntime
                {
                    font-size:medium;
                }

                .comments
                {
                 color:gray;
                }

                .white
                {
                    color:white;
                }

                .error
                {
                    color:red;
                    font-size:medium;
                }

                .errormsg
                {
                }

                table
                {
                 margin-bottom:15px;
                }

                td
                {
                    color:gray;
                    font-size:x-small;
                    white-space:nowrap;
                    padding:1px 5px 1px 5px;
                }

                .allruntime td
                {
                    color:black;
                    font-size:medium;
                    white-space:nowrap;
                }

                th
                {
                    color:gray;
                }

                .framework
                {
                    display:none;
                }

                .businesslayer
                {
                    display:none;
                }

                .slave_title
                {
                    font-style:italic;
                    color:blue;
                }

                .slave_sql
                {
                    color:blue;
                }
            {/literal}
        -->
        </style>
        <script type="text/javascript">
        <!--
            {literal}
                function switchOpt(id)
                {
                    var cssRules = "";
                    if (document.all)
                        cssRules = "rules";
                    else
                        cssRules = "cssRules";

                    var value = document.getElementById(id).checked ? "" : "none";

                    for (var i = 0; i < document.styleSheets.length; i++)
                        for (var j = 0; j < document.styleSheets[i][cssRules].length; j++)
                            if (document.styleSheets[i][cssRules][j].selectorText == "." + id)
                                document.styleSheets[i][cssRules][j].style["display"] = value;
                }
            {/literal}
        //-->
        </script>
    </head>
    <body>
        <div class="white">/*</div>
        <table class="table">
            <tr>
                <td><input checked="checked" onclick="switchOpt('sqlno')" id="sqlno" type="checkbox" /><label for="sqlno">Command number</label></td>
                <td><input checked="checked" onclick="switchOpt('sqlcommand')" id="sqlcommand" type="checkbox" /><label for="sqlcommand">Sql command</label></td>
                <td><input checked="checked" onclick="switchOpt('selrows')" id="selrows" type="checkbox" /><label for="selrows">Selected rows</label></td>
                <td><input checked="checked" onclick="switchOpt('firstresultrow')" id="firstresultrow" type="checkbox" /><label for="firstresultrow">First result row</label></td>
            </tr>
            <tr>
                <td><input onclick="switchOpt('result')" id="result" type="checkbox" /><label for="result">Result rows 2-25</label></td>
                <td><input checked="checked" onclick="switchOpt('explain')" id="explain" type="checkbox" /><label for="explain">Explain query</label></td>
                <td><input checked="checked" onclick="switchOpt('runtime')" id="runtime" type="checkbox" /><label for="runtime">Runtime</label></td>
                <td><input checked="checked" onclick="switchOpt('affectedrows')" id="affectedrows" type="checkbox" /><label for="affectedrows">Affected rows</label></td>
            </tr>
            <tr>
                <td><input checked="checked" onclick="switchOpt('allruntime')" id="allruntime" type="checkbox" /><label for="allruntime">Runtime sum</label></td>
                <td><input checked="checked" onclick="switchOpt('comments')" id="comments" type="checkbox" /><label for="comments">Comments</label></td>
                <td><input onclick="switchOpt('framework')" id="framework" type="checkbox" /><label for="framework">Framework</label></td>
                <td><input onclick="switchOpt('businesslayer')" id="businesslayer" type="checkbox" /><label for="businesslayer">Business layer</label></td>
            </tr>
        </table>
        <div class="white">*/</div>

        {assign var=framwork_count value=0}
        {assign var=businesslayer_count value=0}
        {assign var=user_count value=0}
        {assign var=total_count value=0}

        {if $cancel}
            <div class="error">SQL Debugger canceled analyzing after 1000 sql commands</div>
        {/if}

        {foreach from=$commands item=command}
            {if $command.mode==DB_MODE_FRAMEWORK}
                <div class="framework">
            {elseif $command.mode==DB_MODE_BUSINESSLAYER}
                <div class="businesslayer">
            {else}
                <div>
            {/if}
                <p class="sqlno">
                    <span class="white">/*</span>
                        <b>SQL command {counter}</b>
                        {if $command.slave}
                            <span class="slave_title">(slave query executed on {$command.server|escape}, {$command.dblink|escape})
                        {/if}
                        {if $command.mode==DB_MODE_FRAMEWORK}
                            (framework)
                        {elseif $command.mode==DB_MODE_BUSINESSLAYER}
                            (business layer)
                        {/if}
                        {if $command.slave}
                            </span>
                        {/if}
                    <span class="white">*/</span>
                </p>
                <p class="sqlcommand">
                    {if $command.slave}
                        <span class="slave_sql">
                    {/if}

                    {$command.sql|replace:'*/':'* /'|escape} ;

                    {if $command.slave}
                        </span>
                    {/if}
                </p>

                <div class="comments">
                    <div class="white">/*</div>
                    <br />
                    {if $command.count!=-1}
                        <div class="selrows">Number of selected rows: {$command.count}</div>
                    {/if}

                    {*  output result
                     *}
                    {foreach from=$command.result item=row name=result}
                        {if $smarty.foreach.result.first}
                            <table class="firstresultrow" border="1">
                                <tr>
                                    {foreach from=$row key=column item=value}
                                        <th>{$column}</th>
                                    {/foreach}
                                </tr>
                                <tr>
                        {else}
                                <tr class="result">
                        {/if}

                                    {foreach from=$row key=column item=value}
                                        <td>
                                            {if $value==null}
                                                NULL
                                            {else}
                                                {$value|escape}
                                            {/if}
                                        </td>
                                    {/foreach}
                                </tr>

                        {if $smarty.foreach.result.last}
                            </table>
                        {/if}
                    {/foreach}

                    {*  output explain
                     *}
                    {foreach from=$command.explain item=row name=explain}
                        {if $smarty.foreach.explain.first}
                            <table class="explain" border="1">
                                <tr>
                                    {foreach from=$row key=column item=value}
                                        <th>{$column}</th>
                                    {/foreach}
                                </tr>
                        {/if}

                            <tr>
                                {foreach from=$row key=column item=value}
                                    <td>
                                        {if $value==null}
                                            NULL
                                        {else}
                                            {$value|escape}
                                        {/if}
                                    </td>
                                {/foreach}
                            </tr>

                        {if $smarty.foreach.explain.last}
                            </table>
                        {/if}
                    {/foreach}

                    {*  output warnings
                     *}
                    {foreach from=$command.warnings item=row name=warnings}
                        {if $smarty.foreach.warnings.first}
                            <div class="error">MySQL warning:</div>
                            <div class="errormsg">
                                <table class="table">
                        {/if}

                            <tr><td style="font-size:1em">{$row|escape}</td></tr>

                        {if $smarty.foreach.warnings.last}
                                </table>
                            </div>
                        {/if}
                    {/foreach}

                    {if $command.mode==DB_MODE_FRAMEWORK}
                        {assign var=framwork_runtime value=`$framwork_runtime+$command.runtime`}
                        {assign var=framwork_count value=`$framwork_count+1`}
                    {elseif $command.mode==DB_MODE_BUSINESSLAYER}
                        {assign var=businesslayer_runtime value=`$businesslayer_runtime+$command.runtime`}
                        {assign var=businesslayer_count value=`$businesslayer_count+1`}
                    {else}
                        {assign var=user_runtime value=`$user_runtime+$command.runtime`}
                        {assign var=user_count value=`$user_count+1`}
                    {/if}
                    {assign var=total_runtime value=`$total_runtime+$command.runtime`}
                    {assign var=total_count value=`$total_count+1`}
                    <div class="runtime">Runtime: {$command.runtime|sprintf:"%1.5f"} sek.</div>
                    <div class="affectedrows">Number of affected rows: {$command.affected}</div>
                    <div class="white">*/</div>
                </div>
            </div>
        {/foreach}

        <span class="white">/*</span>
        <div class="allruntime">
            <hr />
            <table class="table">
                <tr>
                    <td>{t}User runtime:{/t}</td>
                    <td>{$user_runtime|sprintf:"%1.5f"} {t}sec{/t} ({t 1=$user_count}%1 commands{/t})</td>
                </tr>
                <tr>
                    <td>{t}Framework runtime:{/t}</td>
                    <td>{$framwork_runtime|sprintf:"%1.5f"} {t}sec{/t} ({t 1=$framwork_count}%1 commands{/t})</td>
                </tr>
                <tr>
                    <td style="border-bottom:1px black solid">{t}Business layer runtime:{/t}</td>
                    <td style="border-bottom:1px black solid">{$businesslayer_runtime|sprintf:"%1.5f"} {t}sec{/t} ({t 1=$businesslayer_count}%1 commands{/t})</td>
                </tr>
                <tr>
                    <td>{t}Total runtime:{/t}</td>
                    <td>{$total_runtime|sprintf:"%1.5f"} {t}sec{/t} ({t 1=$total_count}%1 commands{/t})</td>
                </tr>
                <tr>
                    <td>{t}Created at:{/t}</td>
                    <td>{"0"|date_format:$opt.format.datetimesec}</td>
                </tr>
            </table>
        </div>
        <span class="white">*/</span>
    </body>
</html>
