<br class="z-clearer" />
{if !$page.nohooks}
{modurl modname="Content" func="view" pid=$pid assign="viewUrl"}
<div class="content-hooks">
{notifydisplayhooks eventname='content.hook.pages.ui.view' area='modulehook_area.content.pages' subject=$page id=$pid returnurl=$viewUrl}
</div>
{/if}
