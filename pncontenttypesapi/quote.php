<?php
/**
 * Content quote plugin
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */


class content_contenttypesapi_quotePlugin extends contentTypeBase
{
  var $text;
  var $inputType;

  function getModule() { return 'content'; }
  function getName() { return 'quote'; }
  function getTitle() { return _CONTENT_CONTENTENTTYPE_QUOTETITLE; }
  function getDescription() { return _CONTENT_CONTENTENTTYPE_QUOTEDESCR; }
  function isTranslatable() { return true; }
  
  function loadData(&$data)
  {
    $this->text = $data['text'];
    $this->source = $data['source'];
    $this->desc = $data['desc'];
  }


  function display()
  {
    $text = DataUtil::formatForDisplayHTML($this->text);
    $source = DataUtil::formatForDisplayHTML($this->source);
    $desc = DataUtil::formatForDisplayHTML($this->desc);
	 
    $text = pnModCallHooks('item', 'transform', '', array($text));
    $text = $text[0];
    
    $render = pnRender::getInstance('content', false);
    $render->assign('source', $source);
    $render->assign('text', $text);
    $render->assign('desc', $desc);    
    
    return $render->fetch('contenttype/quote_view.html');
  }

  
  function displayEditing()
  {
    $text = DataUtil::formatForDisplayHTML($this->text);
    $source = DataUtil::formatForDisplayHTML($this->source);
    $desc = DataUtil::formatForDisplayHTML($this->desc);  
    
    $text = pnModCallHooks('item', 'transform', '', array($text));
    $text = trim($text[0]);
    
    $text = '<blockquote>' . $text . '</blockquote><p>-- ' . $desc . '</p>';

    return $text;
  }

  
  function getDefaultData()
  { 
    return array('text'       => _CONTENT_CONTENTENTTYPE_QUOTETEXTDEFAULT,
                 'source'  => _CONTENT_CONTENTENTTYPE_QUOTESOURCEDEFAULT,
                 'desc'  => _CONTENT_CONTENTENTTYPE_QUOTEDESCDEFAULT);
  }

  
  function startEditing(&$render)
  {
    $scripts = array('javascript/ajax/prototype.js', 'javascript/ajax/pnajax.js');
    PageUtil::addVar('javascript', $scripts);
  }


  function getSearchableText()
  {
    return html_entity_decode(strip_tags($this->text));
  }
}


function content_contenttypesapi_quote($args)
{
  return new content_contenttypesapi_quotePlugin();
}