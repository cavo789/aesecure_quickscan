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

jimport('joomla.application.component.controlleradmin');

class TZ_Portfolio_PlusControllerTemplates extends JControllerAdmin
{
    protected $text_prefix  = 'COM_TZ_PORTFOLIO_PLUS_TEMPLATES';

    public function getModel($name = 'Template', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function upload()
    {
        $app = Factory::getApplication();
        $context = "$this->option.edit.$this->context";

        // Redirect to the edit screen.
        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=templates'
                . '&layout=upload', false
            )
        );

        return true;
    }
}