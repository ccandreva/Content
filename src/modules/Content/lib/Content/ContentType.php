<?php

/**
 * Base class for content plugins
 *
 * You must add a constructor taking an array of the plugin's data. This array
 * corresponds to what is return by Form_View::getValues() when user
 * has submitted data after editing plugin. The constructor should initialize
 * object data based on the input.
 */
class Content_ContentType extends Content_Type
{

    protected $pageId;
    protected $contentAreaIndex;
    protected $position;
    protected $contentId;
    /**
     * Style position (none, above, topLeft, topRight, aboveLeft, aboveRight)
     * @var string
     */
    protected $stylePosition;
    /**
     * Style width (percentage)
     * @var int
     */
    protected $styleWidth;
    /**
     * Style class (CSS class name)
     * @var int
     */
    protected $styleClass;
    /**
     * Flag indicating if a styling <div> has been added
     */
    protected $addedStyle = false;

    public function getPageId()
    {
        return $this->pageId;
    }

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    public function getContentAreaIndex()
    {
        return $this->contentAreaIndex;
    }

    public function setContentAreaIndex($contentAreaIndex)
    {
        $this->contentAreaIndex = $contentAreaIndex;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getContentId()
    {
        return $this->contentId;
    }

    public function setContentId($contentId)
    {
        $this->contentId = $contentId;
    }

    public function getStylePosition()
    {
        return $this->stylePosition;
    }

    public function setStylePosition($stylePosition)
    {
        $this->stylePosition = $stylePosition;
    }

    public function getStyleWidth()
    {
        return $this->styleWidth;
    }

    public function setStyleWidth($styleWidth)
    {
        $this->styleWidth = $styleWidth;
    }

    public function getStyleClass()
    {
        return $this->styleClass;
    }

    public function setStyleClass($styleClass)
    {
        $this->styleClass = $styleClass;
    }

    public function getAddedStyle()
    {
        return $this->addedStyle;
    }

    public function setAddedStyle($addedStyle)
    {
        $this->addedStyle = $addedStyle;
    }

    /**
     * Get extended plugin information to display on admin module dependency list
     * @return string
     */
    public function getAdminInfo()
    {
        return '';
    }

    /**
     * Check for translation ability
     * @return bool True if content can be translated
     */
    public function isTranslatable()
    {
        return false;
    }

    /**
     * Check for availability of plugin
     * @return bool True if active and ready to be used
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Load plugin object data from values stored in database
     *
     * @return bool True
     */
    public function loadData(&$data)
    {
        return true;
    }

    /**
     * Get text displayed before actual content.
     *
     * Use this method to display styling like float and width for the content.
     * The default implementation adds a generic <div> around the content, but
     * you can choose to override this method in inherited plugins in order to
     * generate more compact HTML where the styling is included in the actual
     * content.
     * @return string Displayed text
     */
    public function displayStart()
    {
        $style = '';
        $class = '';

        if (!empty($this->styleClass)) {
            $class .= $this->styleClass . ' ';
        }
        switch ($this->stylePosition) {
            case 'above':
                $style .= "margin-left: auto; margin-right: auto;";
                break;
            case 'topLeft':
                $style .= "float: left;";
                break;
            case 'topRight':
                $style .= "float: right;";
                break;
            case 'aboveLeft':
                $style .= "margin-right: auto;";
                break;
            case 'aboveRight':
                $style .= "margin-left: auto;";
                break;
        }

        if ($this->stylePosition != 'none') {
            $class .= "figure-$this->stylePosition ";
            if (!empty($this->styleWidth)) {
                $class .= "$this->styleWidth ";
            }
        }

        if (empty($style) && empty($class)) {
            return '';
        }
        $styleHtml = empty($style) ? '' : " style=\"$style\"";
        $classHtml = empty($class) ? '' : " class=\"$class\"";

        $this->addedStyle = true;
        return "<div$styleHtml$classHtml>\n";
    }

    /**
     * Get output for normal display
     * @return string
     */
    public function display()
    {
        return '- no display function defined -';
    }

    /**
     * Get text displayed after actual content.
     * @return string Displayed text
     */
    public function displayEnd()
    {
        if (!$this->addedStyle) {
            return '';
        } else {
            return '</div>';
        }
    }

    /**
     * Get output for display in editing mode
     *
     * Default implementation simply returns plugin title
     * @return string
     */
    public function displayEditing()
    {
        return $this->getTitle();
    }

    /**
     * Get array containing default data similar to what is returned by Form_View::getValues()
     * @return array
     */
    public function getDefaultData()
    {
        return null;
    }

    /**
     * Event handler called when plugin is loaded for use in editing window
     *
     * Can be used to include JavaScript using PageUtil::addVar() or assign
     * values to the render using $this->view->assign().
     * 
     * @return void
     */
    public function startEditing()
    {

    }

    /* UNUSED ??? */

    public function handleSomethingChanged(Zikula_View $view, $data)
    {

    }

    /**
     * Event handler called when instance of plugin is deleted
     *
     * Can be used to clean up extra data stored in database
     * @return nothing
     */
    public function delete()
    {

    }

    /**
     * Event handler called after user has submitted input
     *
     * Can be used to check user data as well as post-process submitted data.
     * @return bool True is data is valid - false otherwise.
     */
    public function isValid(&$data)
    {
        return true;
    }

    /**
     * Return searchable text
     *
     * This function should return all the text that is searchable through PostNuke's standard
     * search interface. You must strip the text of any HTML tags and other structural information
     * before returning the text. If you have multiple searchable text fields then concatenate all
     * the text from these and return the full string.
     * @return string Searchable text
     */
    public function getSearchableText()
    {
        return null;
    }

    /**
     * return the default view template name as a string
     * @return string
     */
    public function getTemplate()
    {
        $template = 'contenttype/' . strtolower($this->getName()) . '_view.tpl';

        if ($this->view->template_exists($template)) {
            return $template;
        } else {
            return 'contenttype/blank.tpl';
        }
    }

    /**
     * return the default edit template name as a string
     * @return string
     */
    public function getEditTemplate()
    {
        $template = 'contenttype/' . strtolower($this->getName()) . '_edit.tpl';

        if ($this->view->template_exists($template)) {
            return $template;
        } else {
            return 'contenttype/blank.tpl';
        }
    }

    /**
     * return the default translation templates names in an array
     * @return array
     */
    public function getTranslationTemplates()
    {
        $templates = array();
        $template = 'contenttype/' . strtolower($this->getName()) . '_translate_original.tpl';
        if ($this->view->template_exists($template)) {
            $templates['original'] = $template;
        } else {
            $templates['original'] = 'contenttype/blank.tpl';
        }
        $template = 'contenttype/' . strtolower($this->getName()) . '_translate_new.tpl';
        if ($this->view->template_exists($template)) {
            $templates['new'] = $template;
        } else {
            $templates['new'] = 'contenttype/blank.tpl';
        }
        return $templates;
    }

}
