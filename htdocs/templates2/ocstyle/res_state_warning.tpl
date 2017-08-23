{***************************************************************************
* You can find the license in the docs directory
***************************************************************************}

{if ($cache.status==3) || ($cache.status==6)}
    <div class="isarchived">
        <p><strong>{t 1=$cache.statusName|escape}Attention! This Geocache is &quot;<span class="errormsg">%1</span>&quot;!</strong> There is no physical container at the specified (or to be determined) coordinates. In the interest of the place it should not be necessarily to search!{/t}</p>
    </div>
{elseif $cache.status==2}
    <div class="isarchived">
        <p><strong>{t 1=$cache.statusName|escape}Attention! This Geocache is &quot;<span class="errormsg">%1</span>&quot;!</strong> The geocache itself or parts of it are missing or there are other issues that make a successful search impossible. In the interest of the place it should not be necessarily to search!{/t}</p>
    </div>
{elseif $cache.status==5}
    <div class="isannotated">
        {if $date_activate}
          <p style="margin-bottom:0">{t 1=$date_activate|date_format:$opt.format.datelong 2=$date_activate|date_format:$opt.format.time}This geocache will be published on <b>%1</b> at <b>%2</b>.{/t}</p>
        {else}
            <p><strong>{t}This Geocache has not been published yet.{/t}</strong></p>
            <p>{t 1="href='articles.php?page=cacheinfo' target='_blank'" 2=$cache.cacheid}Please verify that the geocache description is complete and all properties and attributes are set properly, according to the <a %1>instructions</a>. Then click <strong><a href="editcache.php?cacheid=%2&publish=now#others">here</a></strong> and "Save" to publish your geocache.{/t}
        {/if}
    </div>
{elseif $cache.listing_outdated>0}
    <div class="isannotated">
        <p><strong>{t}This geocache description may be outdated.{/t}</strong> {t 1=$smarty.server.PHP_SELF 2="?cacheid=" 3=$smarty.get.cacheid}See the <span style="white-space:nowrap"><img src="resource2/ocstyle/images/log/16x16-listing-outdated.png"> <a href="%1%2%3#logentries" class="systemlink">log entries</a></span> for more information.{/t}</p>
    </div>
{/if}
