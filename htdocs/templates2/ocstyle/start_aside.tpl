<div class="col-3">
    <aside>
        <div class="nav3">
            <ul>
                <li class="title">
                    {if $submenu.0.parent==1}{* start page hack *}{t}News{/t}{else}{t}Main menu{/t}{/if}
                </li>
                {nocache}
                    {include file="sys_submenu.tpl" items="$submenu"}
                {/nocache}
            </ul>
        </div>
    </aside>
</div>
