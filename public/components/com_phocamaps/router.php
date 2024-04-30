<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_phocamaps
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Factory;


/**
 * Routing class of com_phocamaps
 *
 * @since  3.3
 */


class PhocamapsRouter extends RouterView
{
	protected $noIDs = false;

	/**
	 * Content Component router constructor
	 *
	 * @param   JApplicationCms  $app   The application object
	 * @param   JMenu            $menu  The menu object to work with
	 */
	public function __construct($app = null, $menu = null)
	{


		$params = ComponentHelper::getParams('com_phocamaps');
		$this->noIDs = (bool) $params->get('sef_ids');

		$categories = new RouterViewConfiguration('map');
		$categories->setKey('id');
		$this->registerView($categories);

/*
		$category = new RouterViewConfiguration('map');
		$category->setKey('id')->setParent($categories, 'parent_id')->setNestable();
		$this->registerView($category);
        $file = new RouterViewConfiguration('file');
		$file->setKey('id')->setParent($category, 'catid');//->setNestable();
		$this->registerView($file);

		//$play = new RouterViewConfiguration('play');
		//$play->setKey('id')->setParent($category, 'catid');//->setNestable();
		//$this->registerView($play);

		$views = array('play', 'download', 'feed', 'user');
        foreach ($views as $k => $v) {
            $item = new RouterViewConfiguration($v);
		    $item->setName($v)->setParent($file, 'id')->setParent($category, 'catid');
		    $this->registerView($item);
        }

*/

		parent::__construct($app, $menu);

		//phocadownloadimport('phocadownload.path.routerrules');
		//phocadownloadimport('phocadownload.category.category');
		//$this->attachRule(new MenuRules($this));
		//$this->attachRule(new PhocaDownloadRouterrules($this));

		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));



	}
}


function PhocaMapsBuildRoute(&$query)
{

	$app = Factory::getApplication();
	$router = new PhocamapsRouter($app, $app->getMenu());

	return $router->build($query);
}


function PhocaMapsParseRoute($segments)
{


	$app = Factory::getApplication();
	$router = new PhocamapsRouter($app, $app->getMenu());

	return $router->parse($segments);
}

