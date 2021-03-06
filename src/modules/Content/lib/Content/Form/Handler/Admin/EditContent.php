<?php

class Content_Form_Handler_Admin_EditContent extends Zikula_Form_Handler
{
    protected $contentId;
    protected $pageId;
    protected $backref;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function getContentId()
    {
        return $this->contentId;
    }

    public function setContentId($contentId)
    {
        $this->contentId = $contentId;
    }

    public function getPageId()
    {
        return $this->pageId;
    }

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    public function getBackref()
    {
        return $this->backref;
    }

    public function setBackref($backref)
    {
        $this->backref = $backref;
    }

    public function initialize(Zikula_Form_View $view)
    {
        $this->contentId = (int) FormUtil::getPassedValue('cid', isset($this->args['cid']) ? $this->args['cid'] : -1);

        $content = ModUtil::apiFunc('Content', 'Content', 'getContent', array('id' => $this->contentId, 'translate' => false));
        if ($content === false) {
            return $view->registerError(null);
        }

        $this->contentType = ModUtil::apiFunc('Content', 'Content', 'getContentType', $content);
        if ($this->contentType === false) {
            return $view->registerError(null);
        }

        $this->contentType['plugin']->startEditing($view);
        $this->pageId = $content['pageId'];

        if (!Content_Util::contentHasPageEditAccess($this->pageId)) {
            return $view->registerError(LogUtil::registerPermissionError());
        }

        $page = ModUtil::apiFunc('Content', 'Page', 'getPage', array('id' => $this->pageId, 'includeContent' => false, 'filter' => array('checkActive' => false)));
        if ($page === false) {
            return $view->registerError(null);
        }

        $multilingual = ModUtil::getVar(ModUtil::CONFIG_MODULE, 'multilingual');
        if ($page['language'] == ZLanguage::getLanguageCode())
            $multilingual = false;

        PageUtil::setVar('title', $this->__("Edit content item") . ' : ' . $page['title']);

        $template = 'file:' . getcwd() . "/modules/$content[module]/templates/" . $this->contentType['plugin']->getEditTemplate();
        $view->assign('contentTypeTemplate', $template);
        $view->assign('page', $page);
        $view->assign('visiblefors', array(array('text' => $this->__('public (all)'), 'value' => '1'), array('text' => $this->__('only logged in members'), 'value' => '0'), array('text' => $this->__('only not logged in people'), 'value' => '2')));
        $view->assign('content', $content);
        $view->assign('data', $content['data']);
        $view->assign('contentType', $this->contentType);
        $view->assign('multilingual', $multilingual);
        $view->assign('enableVersioning',  $this->getVar('enableVersioning'));
        Content_Util::contentAddAccess($view, $this->pageId);

        if (!$this->view->isPostBack() && FormUtil::getPassedValue('back', 0)) {
            $this->backref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        }
        if ($this->backref != null) {
            $returnUrl = $this->backref;
        } else {
            $returnUrl = ModUtil::url('Content', 'admin', 'editpage', array('pid' => $this->pageId));
        }
        ModUtil::apiFunc('PageLock', 'user', 'pageLock', array('lockName' => "contentContent{$this->contentId}", 'returnUrl' => $returnUrl));

        return true;
    }

    public function handleCommand(Zikula_Form_View $view, &$args)
    {
        $url = null;

        if ($args['commandName'] == 'save' || $args['commandName'] == 'translate') {
            if (!$view->isValid()) {
                return false;
            }
            $contentData = $view->getValues();

            $message = null;
            if (!$this->contentType['plugin']->isValid($contentData['data'], $message)) {
                $errorPlugin = &$view->getPluginById('error');
                $errorPlugin->message = $message;
                return false;
            }

            $this->contentType['plugin']->loadData($contentData['data']);

            $ok = ModUtil::apiFunc('Content', 'Content', 'updateContent', array('content' => $contentData + $contentData['content'], 'searchableText' => $this->contentType['plugin']->getSearchableText(), 'id' => $this->contentId));
            if ($ok === false) {
                return $view->registerError(null);
            }
            if ($args['commandName'] == 'translate') {
                $url = ModUtil::url('Content', 'admin', 'translatecontent', array('cid' => $this->contentId, 'back' => 1));
            }
        } else if ($args['commandName'] == 'delete') {
            $ok = ModUtil::apiFunc('Content', 'Content', 'deleteContent', array('contentId' => $this->contentId));
            if ($ok === false) {
                return $view->registerError(null);
            }
        } else if ($args['commandName'] == 'cancel') {
        }

        if ($url == null) {
            $url = $this->backref;
        }
        if (empty($url)) {
            $url = ModUtil::url('Content', 'admin', 'editpage', array('pid' => $this->pageId));
        }
        ModUtil::apiFunc('PageLock', 'user', 'releaseLock', array('lockName' => "contentContent{$this->contentId}"));

        return $view->redirect($url);
    }

    public function handleSomethingChanged(&$view, &$args)
    {
        $contentData = $view->getValues();
        $this->contentType['plugin']->handleSomethingChanged($view, $contentData['data']);
    }
}
