<?php
/**
 * Content 3 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class content_layouttypesapi_column3_502525_headerPlugin extends contentLayoutBase
{
    var $contentAreaTitles = array();

    function __construct()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        $this->contentAreaTitles = array(__('Header', $dom), __('Left column', $dom), __('Centre column', $dom), __('Right column', $dom), __('Footer', $dom));
    }
    function getName()
    {
        return 'column3_502525_header';
    }
    function getTitle()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('3 columns (50|25|25)', $dom);
    }
    function getDescription()
    {
        $dom = ZLanguage::getModuleDomain('Content');
        return __('Header + three columns (50|25|25) + footer', $dom);
    }
    function getNumberOfContentAreas()
    {
        return 5;
    }
    function getContentAreaTitle($areaIndex)
    {
        return $this->contentAreaTitles[$areaIndex];
    }
	function getImage()
    {
    	return System::getBaseUrl().'/modules/Content/images/layout/column3_502525_header.png';
    }
}

function content_layouttypesapi_column3_502525_header($args)
{
    return new content_layouttypesapi_column3_502525_headerPlugin();
}
