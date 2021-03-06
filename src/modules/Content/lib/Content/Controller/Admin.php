<?php

/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */
class Content_Controller_Admin extends Zikula_Controller
{

    /**
     * Main Page list
     * @return <type>
     */
    public function main($args)
    {
        if (!SecurityUtil::checkPermission('Content::', '::', ACCESS_EDIT)) {
            return LogUtil::registerPermissionError();
        }

        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/main.tpl', new Content_Form_Handler_Admin_Main($args));
    }

    /**
     * Change admin settings
     * @return <type>
     */
    public function settings()
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/settings.tpl', new Content_Form_Handler_Admin_Settings(array()));
    }

    /**
     * Create new page
     * @param <type> $args
     * @return <type>
     */
    public function newpage($args)
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/newpage.tpl', new Content_Form_Handler_Admin_NewPage($args));
    }

    /**
     * Edit singel page
     * @param array $args
     * @return <type>
     */
    public function editpage($args)
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/editpage.tpl', new Content_Form_Handler_Admin_Page($args));
    }

    /**
     * Clone single page
     * @param <type> $args
     * @return <type>
     */
    public function clonepage($args)
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/clonepage.tpl', new Content_Form_Handler_Admin_ClonePage($args));
    }

    /**
     * New content element
     * @param <type> $args
     * @return <type>
     */
    public function newcontent($args)
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/newcontent.tpl', new Content_Form_Handler_Admin_NewContent($args));
    }

    /**
     * Edit single content item
     * @param <type> $args
     * @return <type>
     */
    public function editcontent($args)
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/editcontent.tpl', new Content_Form_Handler_Admin_EditContent($args));
    }

    /**
     * Translate page
     * @param <type> $args
     * @return <type>
     */
    public function translatepage($args)
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/translatepage.tpl', new Content_Form_Handler_Admin_TranslatePage($args));
    }

    /**
     * Translate content item
     * @param <type> $args
     * @return <type>
     */
    public function translatecontent($args)
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/translatecontent.tpl', new Content_Form_Handler_Admin_TranslateContent($args));
    }

    /**
     * History
     * @param <type> $args
     * @return <type>
     */
    public function history($args)
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/history.tpl', new Content_Form_Handler_Admin_HistoryContent($args));
    }

    /**
     * Restore deleted pages
     * @param <type> $args
     * @return <type>
     */
    public function deletedpages($args)
    {
        $view = FormUtil::newForm('Content', $this);
        return $view->execute('admin/deletedpages.tpl', new Content_Form_Handler_Admin_DeletedPages($args));
    }

}
