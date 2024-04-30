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

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

class TZ_Portfolio_PlusController extends JControllerLegacy
{
	/**
	 * @var		string	The extension for which the categories apply.
	 * @since	1.6
	 */
	protected $extension;

    protected $input;
    protected $default_view = 'dashboard';

//    protected $plugin_views = false;

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->extension)) {
			$this->extension = $this -> input -> getCmd('extension', 'com_tz_portfolio_plus');
		}
	}

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{

        // Get the document object.
        $app        = Factory::getApplication();
        $document   = $app -> getDocument();

		$document -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/css/style.min.css', array('version' => 'auto'));

        JHtml::_('jquery.framework');

        // Set the default view name and format from the Request.
        $view		= $this -> input -> get('view', 'dashboard');

        $vFormat	= $document->getType();
        $layout		= $this -> input -> get('layout', 'default');
        $id			= $this -> input -> getInt('id');

        // Check each manage permission
        if(!$this -> _checkAccess($view)){
            $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus', false));
            return false;
        }

        // Check for addon_datas
        if($view == 'addon_datas' && !$this -> input -> getInt('addon_id')){
            $response = 500;

            if ($app->get('sef_rewrite'))
            {
                $response = 404;
            }
            throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_VIEW_NOT_FOUND', $view,
                $vFormat, $this->getName() . 'View'), $response);
        }

        // Check for edit form.
        if($layout == 'edit'){
            if (in_array($view, array('category', 'article', 'group', 'field', 'style'))
                && !$this->checkEditId('com_tz_portfolio_plus.edit.'.$view, $id)) {

                // Somehow the person just went to the form - we don't allow that.
                $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');

                // Check edit category
                if($view == 'category') {
                    $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=categories&extension='
                        . $this->extension, false));
                }

                // Check edit article
                if($view == 'article') {
                    $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=articles', false));
                }

                // Check edit group
                if($view == 'group') {
                    $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=groups', false));
                }

                // Check edit field
                if($view == 'field') {
                    $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=fields', false));
                }

                // Check edit style
                if($view == 'style') {
                    $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=template_styles', false));
                }

                return false;
            }


            // Check for edit form of addon
            if($view == 'addon'){
                $user   = Factory::getUser();
                if(!$user -> authorise('core.admin', 'com_tz_portfolio_plus.addon.'.$id)
                    && !$user -> authorise('core.options', 'com_tz_portfolio_plus.addon.'.$id)) {

                    // Somehow the person just went to the form - we don't allow that.
                    $this->setMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
                    $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=addons', false));
                    return false;

                }
            }
        }

        require_once JPATH_COMPONENT.'/helpers/categories.php';

        $display    = parent::display($cachable, $urlparams);

        // Footer
        JLayoutHelper::render('footer');

        return $display;
	}

	protected function _checkAccess($view){
	    $user   = Factory::getUser();
	    $error  = false;

	    switch ($view){
            case 'articles':
            case 'article':
            case 'featured':
                if(!$user -> authorise('core.manage.article', $this -> extension)){
                    $error  = true;
                }
                break;
            case 'categories':
            case 'category':
                if(!$user -> authorise('core.manage.category', $this -> extension)){
                    $error  = true;
                }
                break;
            case 'fields':
            case 'field':
                if(!$user -> authorise('core.manage.field', $this -> extension)){
                    $error  = true;
                }
                break;
            case 'groups':
            case 'group':
                if(!$user -> authorise('core.manage.group', $this -> extension)){
                    $error  = true;
                }
                break;
            case 'tags':
            case 'tag':
                if(!$user -> authorise('core.manage.tag', $this -> extension)){
                    $error  = true;
                }
                break;
            case 'addons':
            case 'addon':
            case 'addon_datas':
            case 'addon_data':
                if(!$user -> authorise('core.manage.addon', $this -> extension)){
                    $error  = true;
                }
                break;
            case 'styles':
            case 'style':
                if(!$user -> authorise('core.manage.style', $this -> extension)){
                    $error  = true;
                }
                break;
            case 'templates':
            case 'template':
                if(!$user -> authorise('core.manage.template', $this -> extension)){
                    $error  = true;
                }
                break;
            case 'extensions':
            case 'extension':
                if(!$user -> authorise('core.manage.extension', $this -> extension)){
                    $error  = true;
                }
                break;
            case 'acls':
            case 'acl':
                if(!$user -> authorise('core.manage.acl', $this -> extension)){
                    $error  = true;
                }
                break;
        }
        if($error){
	        $this -> setMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	        return false;
        }
        return true;
    }

    public function introGuide(){

        $this->checkToken();

        $app    = Factory::getApplication();
	    $input  = $this -> input;
        $view   = $input -> get('v');

	    $folderPath = COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/cache';

        if(!$view){
            $app -> close();
        }

        $filePath   = Path::clean($folderPath.'/introguide.json');

        $config     = new stdClass();

        if(File::exists($filePath)) {
            $config = file_get_contents($filePath);
            $config = json_decode($config);
        }
        $config -> $view    = 1;

        $config = json_encode($config);

        try {
            echo File::write($filePath, $config);
        }catch (Exception $e){
        }
        Factory::getApplication() -> close();
    }

    public function installdemo(){

        $this->checkToken();

        $result     = false;
        $message    = null;
        $app        = Factory::getApplication();
        $db         = Factory::getDbo();
        $query      = $db -> getQuery(true);

        $query -> select('COUNT(*)');
        $query -> from('#__tz_portfolio_plus_content');

        $db -> setQuery($query);

        if($db -> loadResult()){
            $message    = JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_SAMPLE_DATA_ERROR');
        }else{
            $file       = Path::clean(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/install/demo.sql');
            $buffer     = file_get_contents($file);
            $queries    = TZ_Portfolio_PlusDatabase::splitQueries($buffer);
            $db     = Factory::getDbo();

            foreach ($queries as $sql) {
                // Trim any whitespace.
                $sql    = trim($sql);
                $db -> setQuery($sql);
                $result = $db -> execute();
            }

            /* Update created_by with current user */
            $user   = Factory::getUser();

            $query  -> clear();
            $query -> update('#__tz_portfolio_plus_content');
            $query -> set('created_by = '.$user -> get('id'));
            $db -> setQuery($query);
            $db -> execute();

            /* Update asset_id */
            $query -> clear();
            $query -> select('*');
            $query -> from('#__tz_portfolio_plus_content');

            $db -> setQuery($query);

            if($items = $db -> loadObjectList()){
                /* Get Category */
                $query  -> clear();
                $query -> select('*');
                $query -> from('#__tz_portfolio_plus_categories');
                $query -> where('extension = '.$db -> quote('com_tz_portfolio_plus'));
                $query -> where('published = 1');
                $query -> order('id ASC');

                $db -> setQuery($query);
                if($category = $db -> loadObject()){
                    /* Update category with it is available */
                    $query -> clear();
                    $query -> update('#__tz_portfolio_plus_content_category_map');
                    $query -> set('catid='.$category -> id);
                    $db -> setQuery($query);
                    $db -> execute();
                }

                \JTable::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'tables');
                $table  = \JTable::getInstance('Content', 'TZ_Portfolio_PlusTable');

                foreach($items as $item){
                    $table -> load($item -> id);
                    $table -> store();
                }
            }

            /* Create menu if not exists */
            $menu       = $app -> getMenu('site');
            $menuItems  = $menu -> getItems('link', 'index.php?option=com_tz_portfolio_plus&view=portfolio');
            if(!count($menuItems)){
                JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_menus/tables');
                JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_menus/models');
                $modelMenu  = JModelLegacy::getInstance('Item', 'MenusModel');

                $component  = JComponentHelper::getComponent('com_tz_portfolio_plus');

                $modelMenu -> save(array('title' => 'TZ Portfolio Plus',
                    'component_id' => $component -> id,
                    'menutype' => 'mainmenu',
                    'link' => 'index.php?option=com_tz_portfolio_plus&view=portfolio',
                    'type' => 'component',
                    'level' => 1,
                    'published' => 1,
                    'parent_id' => 1,
                    'client_id' => 0,
                    'language' => '*'));
            }

            $message    = JText::_('COM_TZ_PORTFOLIO_PLUS_INSTALL_SAMPLE_DATA_SUCCESSFULL');
        }

        echo new JResponseJson('', $message, !$result);

        $app -> setHeader('Content-Type', 'application/json', true);
        $app -> close();
    }
}
