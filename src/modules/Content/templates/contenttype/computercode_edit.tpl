<div class="z-linear">
    <div class="z-formrow">
        {formlabel for='text' __text='Computer code lines'}
        {formtextinput id='text' textMode='multiline' group='data' cols='60' rows='20'}
    </div>
    {modishooked tmodname="bbcode" smodname="Content" assign="bbok"}
    {if !$bbok}
    <div class="z-formrow">
        <em class="z-sub">{gt text="If you register the module BBCode as a hook for Content then you can get your computer code better formatted."}</em>
    </div>
    {/if}
</div>
