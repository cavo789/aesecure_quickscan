<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');
jimport('joomla.filesystem.file');

class TZ_Portfolio_PlusControllerSearch extends JControllerLegacy
{
	public function search(){
		// Slashes cause errors, <> get stripped anyway later on. # causes problems.
		$badchars = array('#', '>', '<', '\\');
		if($searchword = trim(str_replace($badchars, '', $this->input->getString('searchword', null, 'post')))){
			// If searchword enclosed in double quotes, strip quotes and do exact match
			if (substr($searchword, 0, 1) == '"' && substr($searchword, -1) == '"')
			{
				$post['searchword'] = substr($searchword, 1, -1);
				$this->input->set('searchphrase', 'exact');
			}
			else
			{
				$post['searchword'] = $searchword;
			}
		}

		$data	= $this -> input -> getArray();
		if(isset($data['id'])){
			$id			= $this -> input -> getInt('id');
			$post['id']	= $id;
		}

		// The Itemid from the request, we will use this if it's a search page or if there is no search page available
        $itemId         = $this -> input -> getInt('Itemid');
		$post['Itemid'] = $itemId;

		// Set Itemid id for links from menu

        $uri    = JUri::getInstance();
		$app    = JFactory::getApplication();
		$menu   = $app->getMenu();
		$item   = $menu->getItem($post['Itemid']);

        $uri->setQuery($post);
        $uri->setVar('option', 'com_tz_portfolio_plus');

		if($item -> query['view'] == 'portfolio'){
		    $uri -> setVar('view', 'portfolio');
        }else{
		    $uri -> setVar('view', 'search');
        }

		// The requested Item is not a search page so we need to find one
        if ($item->component != 'com_tz_portfolio_plus' || ($item -> component == 'com_tz_portfolio_plus'
                && $item->query['view'] != 'search' && $item->query['view'] != 'portfolio'))
		{
			// Get item based on component, not link. link is not reliable.
			$item = $menu->getItems('component', 'com_tz_portfolio_plus', true);

			// If we found a search page, use that.
			if (!empty($item))
			{
				$post['Itemid'] = $item->id;
			}
		}

		if($fields = $this->input -> get('fields', array(), 'array')){
			if(count($fields)){
				$fields			= array_filter($fields);
				$post['fields']	= $fields;
			}
		}

		$post['limit']	= $this->input->getUInt('limit', null, 'post');

		unset($post['task']);
		unset($post['submit']);

		$uri = JUri::getInstance();
		$uri->setQuery($post);
		$uri->setVar('option', 'com_tz_portfolio_plus');
        if($item->query['view'] == 'portfolio') {
            $uri->setVar('view', 'portfolio');
        }else{
            $uri->setVar('view', 'search');
        }

		$this->setRedirect(JRoute::_('index.php' . $uri->toString(array('query', 'fragment')), false));
	}
}
