<?php
/**
 * Content
 *
 * @copyright (C) 2007-2009, Jorn Wildt
 * @link http://www.elfisk.dk
 * @version $Id$
 * @license See license.txt
 */

function smarty_function_contentlabelhelp($params, &$render) 
{
  $text = $params['text'];
  $text = (strlen($text)>0 && $text[0]=='_' ? constant($text) : $text);
  if (!isset($params['html']) || !$params['html'])
    $text = DataUtil::formatForDisplay($text);
  $result = "<div class=\"content-label-help\">$text</div>";

  if (array_key_exists('assign', $params))
    $render->assign($params['assign'], $result);
  else
    return $result;
}

?>