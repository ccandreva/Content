{ajaxheader modname='Content' filename='ajax.js'}
{form cssClass='z-form'}

<h2>{gt text="Page list and content structure"}</h2>

{if $access.pageCreateAllowed}
<ul class="z-menulinks">
    <li><a class="z-icon-es-new" href="{modurl modname=Content type=edit func=newpage}">{gt text="Add new page"}</a></li>
    <li><a class="z-icon-es-delete" href="{modurl modname=Content type=edit func=deletedpages}">{gt text="Restore pages"}</a></li>
    {checkpermissionblock component='Content::' instance='::' level=ACCESS_ADMIN}
    <li><a class="z-icon-es-cubes" href="{modurl modname=Content type=admin func=main}">{gt text="Administration"}</a></li>
    <li><a class="z-icon-es-config" href="{modurl modname=Content type=admin func=settings}">{gt text="Settings"}</a></li>
    {/checkpermissionblock}
</ul>
{/if}

<p>{gt text="This is where you manage all your pages. You can add/edit/delete/translate pages (depending on permission rights) as well as drag them around to get the content structure right."}</p>

{include file=content_include_tocedit.tpl}

{if $access.pageEditAllowed}
<p class="z-sub">{gt text="Click on the arrow right of a page title for the options of that page. To drag and drop a page you must select the folder icon left of the title and drop it on the title of another page. A page with sub-pages can be expanded or contracted by clicking on the plus or minus left of the page title. To activate/deactive pages or make pages (not) available in the menu click on the led icons."}</p>
{/if}

<script type="text/javascript">
{{foreach from=$pages item=page}}
  content.addDraggablePage({{$page.id}});
{{/foreach}}
</script>

<div>
    <input type="hidden" name="contentTocDragSrcId" id="contentTocDragSrcId"/>
    <input type="hidden" name="contentTocDragDstId" id="contentTocDragDstId"/>
</div>

{formpostbackfunction function="content.pageListDrag" commandName='pageDrop'}

{/form}