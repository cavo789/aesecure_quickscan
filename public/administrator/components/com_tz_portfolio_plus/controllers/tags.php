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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.controlleradmin');

class TZ_Portfolio_PlusControllerTags extends JControllerAdmin
{
    protected $text_prefix  = 'COM_TZ_PORTFOLIO_PLUS_TAGS';

    public function getModel($name = 'Tag', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function delete()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = $this -> input -> get('cid', array(), 'array');

        if (!is_array($cid) || count($cid) < 1)
        {
            Factory::getApplication() -> enqueueMessage(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'error');
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            $cid    = ArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid))
            {
                $this->setMessage(JText::plural('COM_TZ_PORTFOLIO_PLUS_TAGS_COUNT_DELETED', count($cid)));
            }
            else
            {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
    }

    public function searchAjax(){
        $app    = Factory::getApplication();
        $input  = $app -> input;
        JLoader::import('com_tz_portfolio_plus.helpers.tags', JPATH_ADMINISTRATOR.'/components');

        // Receive request data
        $filters = array(
            'like'      => trim($input->get('like', null, 'string')),
            'title'     => trim($input->get('title', null, 'string')),
            'published' => $input->get('published', 1, 'int')
        );

        if ($results = TZ_Portfolio_PlusHelperTags::searchTags($filters))
        {
            // Output a JSON object
            echo json_encode($results);
        }

        $app->close();
    }

}