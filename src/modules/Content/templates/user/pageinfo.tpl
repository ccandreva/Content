{insert name="getstatusmsg"}
{if $access.pageEditAllowed && !$preview}
{ajaxheader modname='Content' filename='ajax.js' nobehaviour=1}
{usergetvar name=uname uid=$page.cr_uid assign=authorName}
{usergetvar name=uname uid=$page.lu_uid assign=updaterName}
{modapifunc modname='Content' type=History func=getPageVersionNo pageId=$page.id assign=versionNo}
<div class="content-pageinfo">
    <a href="#" onclick="return content.pageInfo.toggle({$page.id})" class="info" >{img src=info.png modname=core set=icons/extrasmall alt=Properties }</a>
    <div class="content" id="contentPageInfo-{$page.id}" style="display: none" onmouseover="content.pageInfo.mouseover()" onmouseout="content.pageInfo.mouseout()">
        <h4>{$page.title|truncate:200|safetext}</h4>
        <ul>
            {if $editmode}
            <li><a class="con_image editoff" href="{modurl modname='Content' type=user func=view pid=$page.id editmode="0"}">{gt text="Edit-mode off" domain="module_content"}</a></li>
            {else}
            <li><a class="con_image editon" href="{modurl modname='Content' type=user func=view pid=$page.id editmode="1"}">{gt text="Edit-mode on" domain="module_content"}</a></li>
            {/if}
            <li><a class="con_image edit" href="{modurl modname='Content' type=admin func=editpage pid=$page.id back=1}">{gt text="Edit page" domain="module_content"}</a></li>
            {if $multilingual}
            <li><a class="con_image translate" href="{modurl modname='Content' type=admin func=translatepage pid=$page.id back=1}">{gt text="Translate page" domain="module_content"}</a></li>
            {/if}
            {if $access.pageCreateAllowed}
            <li><a class="con_image insertsub" href="{modurl modname='Content' type=admin func=newpage pid=$page.id loc=sub}">{gt text="New sub-page" domain="module_content"}</a></li>
            <li><a class="con_image insertpage" href="{modurl modname='Content' type=admin func=newpage pid=$page.id}">{gt text="Add new page" domain="module_content"}</a></li>
            {/if}
            <li><a class="con_image pagelist" href="{modurl modname='Content' type=admin func=main}">{gt text="Page list" domain="module_content"}</a></li>
        </ul>
        <ul>
            <li>{gt text='Created by %1$s on %2$s' tag1=$authorName|userprofilelink tag2=$page.cr_date|dateformat:"datebrief" domain="module_content"}</li>
            <li>{gt text='Last updated by %1$s on %2$s' tag1=$updaterName|userprofilelink tag2=$page.lu_date|dateformat:"datebrief" domain="module_content"}</li>
            {if $enableVersioning}<li>{gt text="Version" domain="module_content"} #{$versionNo}</li>{/if}
            {if $multilingual}<li>{gt text="Language" domain="module_content"}: {$page.language}</li>{/if}
        </ul>
    </div>
</div>
{/if}