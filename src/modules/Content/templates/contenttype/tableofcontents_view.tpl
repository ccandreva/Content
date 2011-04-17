{if isset($toc.toc)}
<ul class="content-toc">
    {foreach from=$toc.toc item="item"}
    <li class="content-toc-level_<!--[$item.level]-->"><a href="{$item.url|safetext}">{$item.title|safetext}</a>
        {if !empty($item.toc)}
        {include file="contenttype/tableofcontents_view.tpl" toc=$item}
        {/if}
    </li>
    {/foreach}
</ul>
{/if}