<?php
/**
 * Content
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

function smarty_function_contentlabelhelp($params, &$view) 
{
    $text = $params['text'];
    $text = (strlen($text)>0 && $text[0]=='_' ? constant($text) : $text);
    if (!isset($params['html']) || !$params['html']) {
        $text = DataUtil::formatForDisplay($text);
    }
    $result = "<em class=\"z-sub z-formnote\">$text</em>";

    if (array_key_exists('assign', $params)) {
        $view->assign($params['assign'], $result);
    } else {
        return $result;
    }
}
