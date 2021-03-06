{include file="admin/menu.tpl"}

<div class="z-admincontainer z-clearfix">
    <div class="z-adminpageicon">{icon type="view" size="large"}</div>
    <h2>{gt text="Module settings"}</h2>

    <p>{gt text="This is where you configure various parts of the content module."}</p>

    {form cssClass='z-form'}

    {formsetinitialfocus inputId='shorturlsuffix'}

    {contentformframe}
    {formtabbedpanelset}
    {formtabbedpanel __title='Settings'}
    <fieldset>
        <legend>{gt text="Settings"}</legend>
        <div class="z-formrow">
            {formlabel for='shorturlsuffix' __text='Short URL suffix'}
            {formtextinput id='shorturlsuffix' group="config" maxLength='255'}
            <em class="z-sub z-formnote">{gt text='File suffix of short URLs (including a dot - for instance ".html"). This is only used if you enable short URLs in Zikula\'s admin section.'}</em>
        </div>
        <div class="z-formrow">
            {formlabel for='newPageState' __text='State of new pages'}
            {formdropdownlist id='newPageState' items=$activeoptions group="config"}
        </div>
        <div class="z-formrow">
            {formlabel for='countViews' __text='Count page views'}
            {formcheckbox id='countViews' group="config"}
        </div>
        <div class="z-formrow">
            {formlabel for='googlemapApiKey' __text='Google maps API key'}
            {formtextinput id='googlemapApiKey' group="config" maxLength='255'}
            <em class="z-sub z-formnote">{gt text='A Google Maps API key is no longer needed for including maps with the Javascript API v3. More information at <a href="http://code.google.com/apis/maps/documentation/javascript/basics.html">Google</a>.'}</em>
        </div>
        <div class="z-formrow">
            {formlabel for='flickrApiKey' __text='Flickr API key'}
            {formtextinput id='flickrApiKey' group="config" maxLength='255'}
            <em class="z-sub z-formnote">{gt text='If your want to add Flickr photos to your content then you need a Flickr API key. You can get this from <a href="http://www.flickr.com/api">flickr.com</a>.'}</em>
        </div>
        <div class="z-formrow">
            {formlabel for='styleClasses' __text='Styling'}
            {formtextinput id='styleClasses' group="config" textMode='multiline' rows='6' cols='40'}
            <em class="z-sub z-formnote">{gt text='A list of CSS class names available for styling of content elements. The end user can select one class for each element on a page - for instance "note" for an element styled as a note. Write one class name on each line. Please separate the CSS classes and displaynames with | - eg. "note | Memo".'}</em>
        </div>
        <div class="z-formrow">
            {formlabel for='enableVersioning' __text='Enable version history'}
            {formcheckbox id='enableVersioning' group="config"}
        </div>
        <div class="z-formrow">
            {formlabel for='categoryUsage' __text='Category usage'}
            {formdropdownlist id='categoryUsage' items=$catoptions group="config"}
        </div>
        {modurl modname="Categories" type="admin" func="editregistry" assign=urleditregistry}
        <div class="z-formrow">
            {formlabel for='categoryPropPrimary' __text='Category Property for the first category selection'}
            {formtextinput id='categoryPropPrimary' group="config" maxLength='255'}
            <em class="z-sub z-formnote">{gt text='(In the <a href="%s">category registry</a> you can set the property to use for the first category)' tag1=$urleditregistry|safetext}</em>
        </div>
        <div class="z-formrow">
            {formlabel for='categoryPropSecondary' __text='Category Property for the second category selection'}
            {formtextinput id='categoryPropSecondary' group="config" maxLength='255'}
            <em class="z-sub z-formnote">{gt text='(In the <a href="%s">category registry</a> you can set the property to use for the second category if being used)' tag1=$urleditregistry|safetext}</em>
        </div>
    </fieldset>
    {/formtabbedpanel}

    {formtabbedpanel __title='Module Dependencies'}
    <p class="z-informationmsg">{gt text="Content includes an API that other modules can use to provide their content. Moreover some modules introduce extra functions to plugins delivered with Content."}</p>

    <table id="modules" class="z-admintable">
        <thead>
            <tr>
                <th>{gt text="Installed"}</th>
                <th>{gt text="Active"}</th>
                <th>{gt text="Module"}</th>
                <th>{gt text="Plugin"}</th>
                <th>{gt text="Description"}</th>
            </tr>
        </thead>
        <tbody>
            <tr class="z-odd">
                <td>
                    {modavailable modname="scribite" assign="scribite"}
                    {if !$scribite}
                    {img src=redled.png modname=core set=icons/extrasmall __alt='Not installed' }
                    {else}
                    {img src=greenled.png modname=core set=icons/extrasmall __alt='Installed' }
                    {/if}
                </td>
                <td>
                    {modapifunc modname="scribite" type="user" func="getModuleConfig" modulename="Content" assign="scribite_config"}
                    {if !isset($scribite_config)}
                    {if !$scribite}
                    {img src=redled.png modname=core set=icons/extrasmall __alt='Inactive' }
                    {else}
                    <a href="{modurl modname="scribite" type="admin" func="newmodule"}">{img src=redled.png modname=core set=icons/extrasmall __alt='Inactive' }</a>
                    {/if}
                    {else}
                    {img src=greenled.png modname=core set=icons/extrasmall __alt='Active' }
                    {/if}
                </td>
                <td><strong>{gt text="Scribite"}</strong></td>
                <td>{gt text="WYSIWYG"}</td>
                <td>{gt text='Scribite integrated WYSIWYG JavaScript editors like Xinha, TinyMCE, FCKeditor, openWYSIWYG or NicEdit into Zikula.<br /><a href="http://code.zikula.org/scribite">Download</a>'}</td>
            </tr>
            <tr class="z-even">
                <td>
                    {modavailable modname="bbcode" assign="bbcode"}
                    {if !$bbcode}
                    {img src=redled.png modname=core set=icons/extrasmall __alt='Not installed' }
                    {else}
                    {img src=greenled.png modname=core set=icons/extrasmall __alt='Installed' }
                    {/if}
                </td>
                <td>
                    {modishooked tmodname="bbcode" smodname="Content" assign="bbcode_config"}
                    {if !$bbcode_config}
                    {if !$bbcode}
                    {img src=redled.png modname=core set=icons/extrasmall __alt='Inactive' }
                    {else}
                    {modgetinfo modname='Content' info=all assign=module}
                    <a href="{modurl modname="modules" type="admin" func="hooks" id=$module.id}">{img src=redled.png modname=core set=icons/extrasmall __alt='Inactive' }</a>
                    {/if}
                    {else}
                    {img src=greenled.png modname=core set=icons/extrasmall __alt='Active' }
                    {/if}
                </td>
                <td><strong>{gt text="BBCode"}</strong></td>
                <td>{gt text="Buttons"}</td>
                <td>{gt text='BBCode is an abbreviation for Bulletin Board Code, the lightweight markup language used to format posts in many message boards. (<a href="http://en.wikipedia.org/wiki/Bbcode">Wikipedia</a>)<br /><a href="http://code.zikula.org/bbcode">Download</a>'}</td>
            </tr>
            {modapifunc modname="Content" type="Content" func="getContentTypes" includeInactive=1 assign="plugins"}
            {foreach from=$plugins item=plugin}
            <tr class="{cycle values="z-odd,z-even"}">
                <td>{img src=greenled.png modname=core set=icons/extrasmall __alt='Installed' }</td>
                <td>
                    {if $plugin.isActive}
                    {img src=greenled.png modname=core set=icons/extrasmall __alt='Active' }
                    {else}
                    {img src=redled.png modname=core set=icons/extrasmall __alt='Inactive' }
                    {/if}
                </td>
                <td><strong>{$plugin.module}</strong></td>
                <td>{$plugin.title}</td>
                <td>
                    {$plugin.description}
                    {if !empty($plugin.adminInfo)}<br />{$plugin.adminInfo}{/if}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
    {/formtabbedpanel}
    {/formtabbedpanelset}

    <div class="z-formbuttons z-buttons">
        {formbutton class="z-bt-ok" commandName="save" __text="Save"}
        {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
    </div>

    {/contentformframe}

    {/form}

    <p class="z-center">
        <a href="http://code.zikula.org/content/" title="Content">{$modinfo.name} v{$modinfo.version}</a>
    </p>

</div>
