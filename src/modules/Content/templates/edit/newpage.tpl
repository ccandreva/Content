{ajaxheader modname='Content' filename='pagelayout.js' noscriptaculous=1}
{contentpageheading __header='Add new page' __subheader="Select layout"}
{if isset($page)}<div class="content-page-path">{$locationLabel}{contentpagepath pageId=$page.id}</div>{/if}

{insert name="getstatusmsg"}
{form cssClass='z-form'}
{formsetinitialfocus inputId='title'}
{formerrormessage id='error'}

<script type="text/javascript" >
    //<![CDATA[
    // store the image location and description in a JS array
    var images = [];
    var descs = [];
    {{foreach from=$layouts item=layout}}
    images.push('{{$layout.image}}');
    descs.push('{{$layout.description|safetext}}');
    {{/foreach}}
    //]]>
</script>

{contentformframe}
<fieldset>
    <div class="z-formrow">
        {formlabel for='title' __text='Page title'}
        {formtextinput id='title' mandatory='1' maxLength='255'}
    </div>
    <div class="z-formrow">
        {formlabel for='urlname' __text='Permalink URL name'}
        {formtextinput id='urlname' maxLength='255'}
        {contentlabelhelp __text='(Used in shorturl mode and generated automatically if left blank)'}
    </div>
</fieldset>

<fieldset>
    <legend>{gt text="Please select a specific layout for your page"}</legend>
    <div class="z-formrow">
        {formlabel for='layout' __text='Page layout'}
        {contentlayoutselector id='layout'}
    </div>
    <div class="z-formrow">
        {formlabel for='layout_preview' __text='Layout preview and description'}
        <div id="layout_preview">
            <img id="layout_preview_img" src="{$layouts.0.image}" alt="" />
        </div>
    </div>
    <p id="layout_preview_desc" class="z-formnote">{$layouts.0.description}</p>
</fieldset>

{notifydisplayhooks eventname='content.hook.pages.ui.edit' area='modulehook_area.content.pages' subject=null id=null caller="Content"}

<div class="z-buttons z-formbuttons">
    {formbutton class="z-bt-new" commandName="create" __text="Create"}
    {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
</div>

{/contentformframe}

{/form}
