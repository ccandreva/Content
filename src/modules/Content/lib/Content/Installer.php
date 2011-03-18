<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_Installer extends Zikula_Installer
{

    public function install()
    {
        $dom = ZLanguage::getModuleDomain('Content');

        if (!DBUtil::createTable('content_page')) {
            return false;
        }
        if (!DBUtil::createTable('content_content')) {
            return false;
        }
        if (!DBUtil::createTable('content_pagecategory')) {
            return false;
        }
        if (!DBUtil::createTable('content_searchable')) {
            return false;
        }
        if (!DBUtil::createTable('content_translatedpage')) {
            return false;
        }
        if (!DBUtil::createTable('content_translatedcontent')) {
            return false;
        }
        if (!DBUtil::createTable('content_history')) {
            return false;
        }

        if (!$this->_content_setCategoryRoot()) {
            LogUtil::registerStatus($this->__('Warning! Could not create the default Content category tree. If you want to use categorisation with Content, register at least one property for the module in the Category Registry.')); 
        }

        $this->setVar('shorturlsuffix', '.html');
        $this->setVar('styleClasses', "greybox|Grey box\nredbox|Red box\nyellowbox|Yellow box\ngreenbox|Green box");
        $this->setVar('enableVersioning', false);
        $this->setVar('flickrApiKey', '');
        $this->setVar('googlemapApiKey', '');
        $this->setVar('categoryUsage', '3');
        $this->setVar('categoryPropPrimary', 'primary');
        $this->setVar('categoryPropSecondary', 'primary');
        $this->setVar('newPageState', '1');
        $this->setVar('countViews', '0');

        // Register for hooks subscribing
        HookUtil::registerHookSubscriberBundles($this->version);
        // register handlers
        EventUtil::registerPersistentModuleHandler('Content', 'module.content.getTypes', array('Content_Util', 'getTypes'));

        // create the default data for the Content module
        $this->defaultdata();        

        return true;
    }

    private function _content_setCategoryRoot()
    {
        // load necessary classes

        $rootcat = CategoryUtil::getCategoryByPath('/__SYSTEM__/Modules/Global');
        if ($rootcat) {
            // create an entry in the categories registry
            $registry = new Categories_DBObject_Registry();
            $registry->setDataField('modname', 'Content');
            $registry->setDataField('table', 'content_page');
            $registry->setDataField('property', 'primary');
            $registry->setDataField('category_id', $rootcat['id']);
            $registry->insert();
        }

        return true;
    }

// -----------------------------------------------------------------------
// Module upgrade
// -----------------------------------------------------------------------

    public function upgrade($oldVersion)
    {
        $ok = true;

        // Upgrade dependent on old version number
        switch ($oldVersion) {
            case '0.0.0':
            case '1.0.0':
            case '1.1.0':
                $ok = $ok && $this->contentUpgrade_1_2_0($oldVersion);
            case '1.2.0':
                $ok = $ok && $this->contentUpgrade_1_2_0_1($oldVersion);
            case '1.2.0.1':
            case '2.0.0':
            case '2.0.1':
                $ok = $ok && $this->contentUpgrade_2_0_2($oldVersion);
            case '2.0.2':
            case '2.1.0':
                $ok = $ok && $this->contentUpgrade_2_1_1($oldVersion);
            case '2.1.1':
                $ok = $ok && $this->contentUpgrade_2_1_2($oldVersion);
            case '2.1.2':
            case '3.0.0':
            case '3.0.1':
            case '3.0.2':
            case '3.0.3':
                $ok = $ok && $this->contentUpgrade_3_1_0($oldVersion);
            case '3.1.0':
                $ok = $ok && $this->contentUpgrade_3_2_0($oldVersion);
            case '3.2.0':
                $ok = $ok && $this->contentUpgrade_3_2_1($oldVersion);
            case '3.2.1':
                $ok = $ok && $this->contentUpgrade_4_0_0($oldVersion);
            // future
        }

        // Update successful
        return $ok;
    }

    protected function contentUpgrade_1_2_0($oldVersion)
    {
        if (!DBUtil::createTable('content_translatedcontent')) {
            return contentInitError(__FILE__, __LINE__, "Table creation failed for 'content_translatedcontent': " . $dbconn->ErrorMsg());
        }
        if (!DBUtil::createTable('content_translatedpage')) {
            return contentInitError(__FILE__, __LINE__, "Table creation failed for 'content_translatedpage': " . $dbconn->ErrorMsg());
        }
        if (!$this->setVar('shorturlsuffix', '.html')) {
            return false;
        }
        return true;
    }

    protected function contentUpgrade_1_2_0_1($oldVersion)
    {
        // Drop unused version 1.x column. Some people might have done this manually, so ignore errors.
        $dbconn = DBConnectionStack::getConnection();
        $tables = DBUtil::getTables();
        $dict = NewDataDictionary($dbconn);
        $table = $tables['content_content'];
        $sqlarray = $dict->DropColumnSQL($table, array('con_language'));
        $dict->ExecuteSQLArray($sqlarray);
        return true;
    }

    protected function contentUpgrade_2_0_2($oldVersion)
    {
        DBUtil::changeTable('content_content');
        $this->setVar('styleClasses', "greybox|Grey box\nredbox|Red box");
        return true;
    }

    protected function contentUpgrade_2_1_1($oldVersion)
    {
        if (!DBUtil::createTable('content_history')) {
            return false;
        }
        return true;
    }

    protected function contentUpgrade_2_1_2($oldVersion)
    {
        // Add language column (again since version 1.2.0.1)
        DBUtil::changeTable('content_page');


        $dbconn = DBConnectionStack::getConnection();
        $tables = DBUtil::getTables();
        $language = ZLanguage::getLanguageCode();

        // Assume language of created pages is same as current lang
        $table = $tables['content_page'];
        $column = $tables['content_page_column'];
        $sql = "UPDATE $table SET $column[language] = '" . DataUtil::formatForStore($language) . "'";
        DBUtil::executeSQL($sql);
        return true;
    }

    protected function contentUpgrade_3_1_0($oldVersion)
    {
        $tables = DBUtil::getTables();


        // fix serialisations
        foreach (array('content' => 'id', 'history' => 'id', 'translatedcontent' => 'contentId') as $table => $idField) {
            $obj = DBUtil::selectObjectArray('content_' . $table);
            foreach ($obj as $contentItem) {
                $data = DataUtil::mb_unserialize($contentItem['data']);
                $contentItem['data'] = serialize($data);
                DBUtil::updateObject($contentItem, 'content_' . $table, '', $idField, true);
            }
        }

        // fix language codes
        foreach (array('page' => 'id', 'translatedcontent' => 'contentId', 'translatedpage' => 'pageId') as $table => $idField) {
            $obj = DBUtil::selectObjectArray('content_' . $table);
            if (!count($obj)) {
                continue;
            }
            foreach ($obj as $contentItem) {
                // translate l3 -> l2
                $l2 = ZLanguage::translateLegacyCode($contentItem['language']);
                if (!$l2) {
                    continue;
                }
                $sql = 'UPDATE ' . $tables['content_' . $table] . ' a SET a.' . $tables['content_' . $table . '_column']['language'] . ' = \'' . $l2 . '\' WHERE a.' . $tables['content_' . $table . '_column'][$idField] . ' = \'' . $contentItem[$idField] . '\'';
                DBUtil::executeSQL($sql);
            }
        }
        return true;
    }

    protected function contentUpgrade_3_2_0($oldVersion)
    {
        // update the database
        DBUtil::changeTable('content_page');
        DBUtil::changeTable('content_content');
        DBUtil::changeTable('content_translatedpage');
        DBUtil::changeTable('content_translatedcontent');
        DBUtil::changeTable('content_history');
        
        // add new variable(s)
        $this->setVar('categoryUsage', '1');
        $this->setVar('categoryPropPrimary', 'primary');
        $this->setVar('categoryPropSecondary', 'primary');
        $this->setVar('newPageState', '1');
        
        return true;
    }

    protected function contentUpgrade_3_2_1($oldVersion)
    {
        // update the database
        DBUtil::changeTable('content_page');
        DBUtil::changeTable('content_content');
        DBUtil::changeTable('content_translatedcontent');
        
        // add new variable(s)
        $this->setVar('countViews', '0');

        // clear compiled templates and Content cache
        ModUtil::apiFunc('view', 'user', 'clear_compiled');
        ModUtil::apiFunc('view', 'user', 'clear_cache', array('module' => 'Content'));

        return true;
    }
    
    protected function contentUpgrade_4_0_0($oldVersion)
    {
        // update tables with new indexes
        if (!DBUtil::changeTable('content_page')) {
            return false;
        }
        if (!DBUtil::changeTable('content_content')) {
            return false;
        }
        if (!DBUtil::changeTable('content_translatedpage')) {
            return false;
        }
        if (!DBUtil::changeTable('content_translatedcontent')) {
            return false;
        }
        if (!DBUtil::changeTable('content_history')) {
            return false;
        }
                
        // Register for hook subscribing
        HookUtil::registerHookSubscriberBundles($this->version);
        
        // convert module vars
        $modvars = $this->getVars();
        if ($modvars) {
            foreach ($modvars as $key => $value) {
                $this->setVar($key, $value);
            }
            ModUtil::delVar('Content');
        }
        self::updateLayout();
        // upgrade the Content module's ContentTypes
        self::updateContentType();
        // upgrade other module's ContentTypes if available
        ModUtil::apiFunc('Content', 'admin', 'upgradecontenttypes');

        // register handlers
        EventUtil::registerPersistentModuleHandler('Content', 'module.content.getTypes', array('Content_Util', 'getTypes'));

        // clear compiled templates and Content cache
        ModUtil::apiFunc('view', 'user', 'clear_compiled');
        ModUtil::apiFunc('view', 'user', 'clear_cache', array('module' => 'Content'));
        return true;
    }



// -----------------------------------------------------------------------
// Module uninstall
// -----------------------------------------------------------------------

    public function uninstall()
    {
        DBUtil::dropTable('content_page');
        DBUtil::dropTable('content_content');
        DBUtil::dropTable('content_pagecategory');
        DBUtil::dropTable('content_searchable');
        DBUtil::dropTable('content_translatedcontent');
        DBUtil::dropTable('content_translatedpage');
        DBUtil::dropTable('content_history');

        $this->delVars();

        // unregister handlers
        EventUtil::unregisterPersistentModuleHandlers('Content');
        HookUtil::unregisterHookSubscriberBundles($this->version);

        // Deletion successful
        return true;
    }



// -----------------------------------------------------------------------
// Create default data for a new install
// -----------------------------------------------------------------------
    protected function defaultdata()
    {
        // create one page with 2 columns and some content
        $page = array('title'   => $this->__('Content introduction page'),
                'urlname'       => $this->__('content-introduction-page'),
                'layout'        => 'Column2d6238',
                'setLeft'       => '0',
                'setRight'      => '1',
                'language'      => ZLanguage::getLanguageCode());
        
        // Insert the default page
        if (!($obj = DBUtil::insertObject($page, 'content_page'))) {
            LogUtil::registerStatus($this->__('Warning! Could not create the default Content introductory page.'));
        } else {
            // create the contentitems for this page
            $content = array();
            $content[] = array('pageId' => $obj['id'],
                    'areaIndex'         => '0',
                    'position'          => '0',
                    'module'            => 'Content',
                    'type'              => 'Heading',
                    'data'              => serialize(array('text' => $this->__('A Content page consists of various content items in a chosen layout'),
                                                'headerSize' => 'h3')));
            $content[] = array('pageId' => $obj['id'],
                    'areaIndex'         => '1',
                    'position'          => '0',
                    'module'            => 'Content',
                    'type'              => 'Html',
                    'data'              => serialize(array('text' => $this->__('<p>Each created page has a specific layout, like 1 column with and without a header, 2 columns, 3 columns. The chosen layout contains various content areas. In each area you can place 1 or more content items of various kinds like:</p> <ul> <li>HTML text;</li> <li>YouTube videos;</li> <li>Google maps;</li> <li>Flickr photos;</li> <li>RSS feeds;</li> <li>Computer Code;</li> <li>the output of another Zikula module.</li> </ul> <p>Within these content areas you can sort the content items by means of drag & drop.<br /> You can make an unlimited number of pages and structure them hierarchical. Your page structure can be displayed in a multi level menu in your website.</p>'),
                                                'inputType' => 'text')));
            $content[] = array('pageId' => $obj['id'],
                    'areaIndex'         => '1',
                    'position'          => '1',
                    'module'            => 'Content',
                    'type'              => 'Html',
                    'data'              => serialize(array('text' => $this->__('<p><strong>This is a second HTML text content item in the left column</strong><br /> Content is an extendible module. You can create your own content plugins and layouts and other Zikula modules can also offer content items. The News published module for instance has a Content plugin for a list of the latest articles.</p>'),
                                                'inputType' => 'text')));
            $content[] = array('pageId' => $obj['id'],
                    'areaIndex'         => '2',
                    'position'          => '0',
                    'module'            => 'Content',
                    'type'              => 'Quote',
                    'data'              => serialize(array('text' => $this->__('No matter what your needs, Zikula can provide the solution.'),
                                                'source' => 'http://zikula.org', 'desc' => 'Zikula homepage')));
            $content[] = array('pageId' => $obj['id'],
                    'areaIndex'         => '2',
                    'position'          => '1',
                    'module'            => 'Content',
                    'type'              => 'ComputerCode',
                    'data'              => serialize(array('text' => $this->__('$this->doAction($var); // just some code'))));
            $content[] = array('pageId' => $obj['id'],
                    'areaIndex'         => '2',
                    'position'          => '2',
                    'module'            => 'Content',
                    'type'              => 'Html',
                    'data'              => serialize(array('text' => $this->__('<p>So you see that you can place all kinds of content on the page in your own style and liking. This makes Content a really powerful module.</p> <p>This page uses the <strong>2 column (62|38) layout</strong> which has a header, 2 colums with 62% width on the left and 38% width on the right and a footer</p>'),
                                                'inputType' => 'text')));
            $content[] = array('pageId' => $obj['id'],
                    'areaIndex'         => '3',
                    'position'          => '0',
                    'module'            => 'Content',
                    'type'              => 'Html',
                    'data'              => serialize(array('text' => $this->__('This <strong>footer</strong> finishes of this introduction page. Good luck with using Content. The <a href="index.php?module=content&type=admin">Edit Contents</a> interface lets you edit or delete this introduction page. In the <a href="index.php?module=content&type=admin">administration</a> interface you can further control the Content module.'),
                                                'inputType' => 'text')));

            // write the items to the dbase
            foreach ($content as $contentitem) {
                DBUtil::insertObject($contentitem, 'content_content');
            }
        }
    }
    /**
     * update the DB to reflect new names of Layouts
     * can be called as static method from any module with correct args
     * will expect LegacyLayoutTypeMap() method in module installer
     *
     * @param string $modname
     * @return boolean
     */
    public static function updateLayout($modname="Content")
    {
        $installerclass = $modname . "_Installer";
        $installer = new $installerclass;
        $legacyMap = $installer->LegacyLayoutTypeMap();

        $tables = DBUtil::getTables();
        $table = $tables['content_page'];
        $columns = $tables['content_page_column'];
        $columnArray = array('id', 'layout');
        $items = DBUtil::selectObjectArray($table, '', '', -1, -1, '', null, null, $columnArray);

        foreach ($items as $item) {
            $newitem = $item;
            $newitem['layout'] = in_array($item['layout'], $legacyMap) ? $legacyMap($item['layout']) : false;
            if ($newitem['layout']) {
                DBUtil::updateObject($newitem, $table);
            }
        }
        return true;
    }
    /**
     * update the DB to reflect new names of ContentTypes
     * can be called as static method from any modules with correct args
     * will expect LegacyContentTypeMap() method in module installer
     * 
     * @param string $modname
     * @return boolean
     */
    public static function updateContentType($modname="Content")
    {
        $installerclass = $modname . "_Installer";
        if (class_exists($installerclass)) {
            $installer = new $installerclass(ServiceUtil::getManager());
        } else {
            return;
        }
        if (method_exists($installer, 'LegacyContentTypeMap')) {
            $legacyMap = $installer->LegacyContentTypeMap();
        } else {
            return;
        }
        ModUtil::dbInfoLoad('Content');
        $tables = DBUtil::getTables();
        $table = $tables['content_content'];
        $columns = $tables['content_content_column'];
        $where = "WHERE " . $columns['module'] . "='" . $modname . "'";
        $columnArray = array('id', 'module', 'type');
        $items = DBUtil::selectObjectArray('content_content', $where, '', -1, -1, '', null, null, $columnArray);

        $count = 0;
        foreach ($items as $item) {
            $newitem = $item;
            $newitem['type'] = array_key_exists($item['type'], $legacyMap) ? $legacyMap[$item['type']] : false;
            if ($newitem['type']) {
                DBUtil::updateObject($newitem, 'content_content');
                $count++;
            }
        }
        if ($count > 0) {
            $dom = ZLanguage::getModuleDomain('Content');
            LogUtil::registerStatus(__f('%1$s ContentTypes upgraded for %2$s', array($count, $modname), $dom));
        }
        return $count;
    }
    /**
     * map old LayoutType names to new
     * @return array
     */
    protected function LegacyLayoutTypeMap()
    {
        $oldToNew = array(
            'column1' => 'Column1',
            'column1topheader' => 'Column1top',
            'column1woheader' => 'Column1woheader',
            'column2_1_2header' => 'Column2d12',
            'column2_2575_header' => 'Column2d2575',
            'column2_3070_header' => 'Column2d3070',
            'column2_3366_header' => 'Column2d3366',
            'column2_3862_header' => 'Column2d3862',
            'column2_6238_header' => 'Column2d6238',
            'column2_6633_header' => 'Column2d6633',
            'column2_7030_header' => 'Column2d7030',
            'column2_7525_header' => 'Column2d7525',
            'column2header' => 'Column2header',
            'column3_252550_header' => 'Column3d252550',
            'column3_255025_header' => 'Column3d255025',
            'column3_502525_header' => 'Column3d502525',
            'column3header' => 'Column3header',
        );
        return $oldToNew;
    }
    /**
     * map old ContentType names to new
     * @return array
     */
    protected function LegacyContentTypeMap()
    {
        $oldToNew = array(
            'author' => 'Author',
            'block' => 'Block',
            'breadcrumb' => 'Breadcrumb',
            'camtasia' => 'Camtasia',
            'computercode' => 'Computercode',
            'directory' => 'Directory',
            'flickr' => 'Flickr',
            'googlemap' => 'GoogleMap',
            'heading' => 'Heading',
            'html' => 'Html',
            'joinposition' => 'JoinPosition',
            'modulefunc' => 'ModuleFunc',
            'openstreetmap' => 'OpenStreetMap',
            'pagenavigation' => 'PageNavigation',
            'pagesetter_pub' => 'PagesetterPub',
            'pagesetter_publist' => 'PagesetterPublist',
            'quote' => 'Quote',
            'rss' => 'Rss',
            'slideshare' => 'Slideshare',
            'vimeo' => 'Vimeo',
            'youtube' => 'YouTube',
        );
        return $oldToNew;
    }
}