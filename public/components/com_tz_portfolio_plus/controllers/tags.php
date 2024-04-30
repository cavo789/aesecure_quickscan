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

class TZ_Portfolio_PlusControllerTags extends JControllerLegacy
{

    public function searchAjax(){
        $app    = JFactory::getApplication();
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
