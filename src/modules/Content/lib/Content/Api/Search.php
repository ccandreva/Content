<?php

/**
 * Search plugin info
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */
class Content_Api_Search extends Zikula_Api
{

    public function info()
    {
        return array('title' => 'Content', 'functions' => array('Content' => 'search'));
    }

    /**
     * Search form component
     * */
    public function options($args)
    {
        if (SecurityUtil::checkPermission('Content::', '::', ACCESS_READ)) {
            $this->view->assign('active', (isset($args['active']) && isset($args['active']['content'])) || (!isset($args['active'])));
            return $this->view->fetch('search/options.tpl');
        }

        return '';
    }

    public function search($args)
    {
        $dom = ZLanguage::getModuleDomain('Content');
        ModUtil::dbInfoLoad('Content');
        ModUtil::dbInfoLoad('Search');
        $dbconn = DBConnectionStack::getConnection(true);
        $dbtables = DBUtil::getTables();

        $searchTable = $dbtables['search_result'];
        $searchColumn = $dbtables['search_result_column'];
        $pageTable = $dbtables['content_page'];
        $pageColumn = $dbtables['content_page_column'];
        $contentTable = $dbtables['content_content'];
        $contentColumn = $dbtables['content_content_column'];
        $contentSearchTable = $dbtables['content_searchable'];
        $contentSearchColumn = $dbtables['content_searchable_column'];

        $sessionId = session_id();

        $where = search_construct_where($args, array($contentSearchColumn['text']), null);

        $sql = "INSERT INTO $searchTable
  ($searchColumn[title],
                $searchColumn[text],
                $searchColumn[module],
                $searchColumn[extra],
                $searchColumn[created],
                $searchColumn[session])
SELECT $pageColumn[title],
                $contentSearchColumn[text],
       'content',
                $pageColumn[id],
                $pageColumn[cr_date] AS createdDate,
       '" . DataUtil::formatForStore($sessionId) . "'
FROM $pageTable
JOIN $contentTable
     ON $contentColumn[pageId] = $pageColumn[id]
JOIN $contentSearchTable
     ON $contentSearchColumn[contentId] = $contentColumn[id]
WHERE $where and $contentColumn[active] = 1 and $contentColumn[visiblefor] " . (UserUtil::isLoggedIn() ? '<=1' : '>=1');

        $dbresult = DBUtil::executeSQL($sql);
        if (!$dbresult) {
            return LogUtil::registerError(__('Error! Could not load items.', $dom));
        }
        return true;
    }

    public function search_check(&$args)
    {
        $datarow = &$args['datarow'];
        $pageId = (int) $datarow['extra'];

        $datarow['url'] = ModUtil::url('Content', 'User', 'view', array('pid' => $pageId));

        return true;
    }

}