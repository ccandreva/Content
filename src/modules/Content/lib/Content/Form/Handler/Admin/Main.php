<?php

class Content_Form_Handler_Admin_Main extends Zikula_Form_Handler
{
    public function __construct($args)
    {
        $this->args = $args;
    }

    public function initialize(Zikula_Form_View $view)
    {
        if (!Content_Util::contentHasPageEditAccess()) {
            return $view->registerError(LogUtil::registerPermissionError());
        }

        $pages = ModUtil::apiFunc('Content', 'Page', 'getPages', array('editing' => true, 'filter' => array('checkActive' => false, 'expandedPageIds' => Content_Util::contentMainEditExpandGet()), 'enableEscape' => true, 'translate' => false, 'includeLanguages' => true,
            'orderBy' => 'setLeft'));
        if ($pages === false) {
            return $view->registerError(null);
        }

        PageUtil::setVar('title', $this->__('Page list and content structure'));
        $csssrc = ThemeUtil::getModuleStylesheet('admin', 'admin.css');
        PageUtil::addVar('stylesheet', $csssrc);

        $view->assign('pages', $pages);
        $view->assign('multilingual', ModUtil::getVar(ModUtil::CONFIG_MODULE, 'multilingual'));
        $view->assign('enableVersioning', $this->getVar('enableVersioning'));
        $view->assign('language', ZLanguage::getLanguageCode());
        Content_Util::contentAddAccess($view, null);

        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        $url = ModUtil::url('Content', 'admin', 'main');

        if ($args['commandName'] == 'edit') {
            $url = ModUtil::url('Content', 'admin', 'editpage', array('pid' => $args['commandArgument']));
        } else if ($args['commandName'] == 'newSubPage') {
            $url = ModUtil::url('Content', 'admin', 'newPage', array('pid' => $args['commandArgument'], 'loc' => 'sub'));
        } else if ($args['commandName'] == 'newPage') {
            $url = ModUtil::url('Content', 'admin', 'newPage', array('pid' => $args['commandArgument']));
        } else if ($args['commandName'] == 'clonePage') {
            $url = ModUtil::url('Content', 'admin', 'clonepage', array('pid' => $args['commandArgument']));
        } else if ($args['commandName'] == 'pageDrop') {
            $srcId = FormUtil::getPassedValue('contentTocDragSrcId', null, 'POST');
            $dstId = FormUtil::getPassedValue('contentTocDragDstId', null, 'POST');
            list ($dummy, $srcId) = explode('_', $srcId);
            list ($dummy, $dstId) = explode('_', $dstId);
            
            $ok = ModUtil::apiFunc('Content', 'Page', 'pageDrop', array('srcId' => $srcId, 'dstId' => $dstId));
            if (!$ok) {
                return $view->registerError(null);
            }
        } else if ($args['commandName'] == 'decIndent') {
            $pageId = (int) $args['commandArgument'];
            $ok = ModUtil::apiFunc('Content', 'Page', 'decreaseIndent', array('pageId' => $pageId));
            if (!$ok) {
                return $view->registerError(null);
            }
        } else if ($args['commandName'] == 'incIndent') {
            $pageId = (int) $args['commandArgument'];
            $ok = ModUtil::apiFunc('Content', 'Page', 'increaseIndent', array('pageId' => $pageId));
            if (!$ok) {
                return $view->registerError(null);
            }
        } else if ($args['commandName'] == 'deletePage') {
            $pageId = (int) $args['commandArgument'];
            $ok = ModUtil::apiFunc('Content', 'Page', 'deletePage', array('pageId' => $pageId));
            if ($ok === false) {
                return $view->registerError(null);
            }
        } else if ($args['commandName'] == 'history') {
            $pageId = (int) $args['commandArgument'];
            $url = ModUtil::url('Content', 'admin', 'history', array('pid' => $pageId));
        } else if ($args['commandName'] == 'toggleExpand') {
            $pageId = FormUtil::getPassedValue('contentTogglePageId', null, 'POST');
            Content_Util::contentMainEditExpandToggle($pageId);
        }
        $view->redirect($url);
        return true;
    }
}
