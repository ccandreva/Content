<?php
/**
 * Content RSS plugin
 *
 * @copyright (C) 2007, Content Development Team
 * @link http://noc.postnuke.com/projects/content/
 * @version $Id$
 * @license See license.txt
 */


Loader::requireOnce('modules/content/pnincludes/simplepie/simplepie.inc');


class content_contenttypesapi_RSSPlugin extends contentTypeBase
{
  var $url;
  var $includeContent;
  var $refreshTime;
  var $maxNoOfItems;

  function getModule() { return 'content'; }
  function getName() { return 'rss'; }
  function getTitle() { return _CONTENT_CONTENTENTTYPE_RSSTITLE; }
  function getDescription() { return _CONTENT_CONTENTENTTYPE_RSSDESCR; }

  
  function loadData($data)
  {
    $this->url = $data['url'];
    $this->includeContent = $data['includeContent'];
    $this->refreshTime = $data['refreshTime'];
    $this->maxNoOfItems = $data['maxNoOfItems'];
  }

  
  function display()
  {
    $this->feed = new SimplePie($this->url, pnConfigGetVar('temp'), $this->refreshTime*60);

    $items = $this->feed->get_items();

    $itemsData = array();
    foreach ($items as $item)
    {
      if (count($itemsData) < $this->maxNoOfItems)
      {
        $itemsData[] = array('title' => $this->decode($item->get_title()),
                             'description' => $this->decode($item->get_description()),
                             'permalink'   => $item->get_permalink());
      }
    }
    $this->feedData = array('title' =>  $this->decode($this->feed->get_title()),
                            'description' =>  $this->decode($this->feed->get_description()),
                            'permalink'   => $this->feed->get_permalink(),
                            'items'       => $itemsData);

    $render = pnRender::getInstance('content', false);
    $render->assign('feed', $this->feedData);
    $render->assign('includeContent', $this->includeContent);

    return $render->fetch('contenttype/rss_view.html');
  }

  
  function displayEditing()
  {
    return "<input value=\"" . DataUtil::formatForDisplay($this->url) . "\" style=\"width: 30em\" readonly=readonly/>";
  }

  
  function getDefaultData()
  { 
    return array('url' => '',
                 'includeContent' => false,
                 'refreshTime' => 60,
                 'maxNoOfItems' => 10);
  }


  function decode($s)
  {
    return mb_convert_encoding($s, _CHARSET, $this->feed->get_encoding());
  }
}


function content_contenttypesapi_RSS($args)
{
  return new content_contenttypesapi_RSSPlugin($args['data']);
}

