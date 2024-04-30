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
defined('_JEXEC') or die();

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.modellist');
jimport('joomla.html.pagination');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

JLoader::register('TZ_Portfolio_PlusModelArticles', JPATH_ADMINISTRATOR
    . '/components/com_tz_portfolio_plus/models/articles.php');

class TZ_Portfolio_PlusModelMyArticles extends TZ_Portfolio_PlusModelArticles
{
    protected $pagNav         = null;
    protected $rowsTag        = null;
    protected $categories     = null;
    protected $filterFormName = 'filter_myarticles';

    protected function populateState($ordering = null, $direction = null){
        parent::populateState($ordering,$direction);

        $app    = JFactory::getApplication('site');
        $params = $app -> getParams('com_tz_portfolio_plus');
        $this -> setState('params',$params);

        $filters = $app->input->get('filter', array(), 'array');

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        if($params -> get('filter', '') && $params -> get('filter', '') != ''){
            $published  = $params -> get('filter', '');
        }
        $this -> setState('filter.published', $published);

        $this -> setState('catid',$app -> input -> get('catid'));
    }


    public function getFilterForm($data = array(), $loadData = true)
    {
        // Get the form.
        \JForm::addFormPath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH. '/models/forms');
        \JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH . '/models/fields');

        $form   = parent::getFilterForm();
        $params = $this -> getState('params');
        $filter  = $params -> get('filter', '');

        $published = $this->getState('filter.published');
        if($params -> get('filter', '') && $params -> get('filter', '') != ''){
            $published  = $filter;
        }

        if($filter && $filter != '*'){
            $form -> removeField('published', 'filter');
        }else{
            $form -> setValue('published', 'filter', $published);
        }

        return $form;
    }


    protected function getListQuery()
    {
        $user   = JFactory::getUser();

        $query  = parent::getListQuery();

        $canApprove = $user -> authorise('core.approve', 'com_tz_portfolio_plus');

        if(!$canApprove) {
            $query->where('a.created_by =' . $user->get('id'));
        }

        return $query;
    }
}
?>