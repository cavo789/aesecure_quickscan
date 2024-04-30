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

use Joomla\CMS\Factory;

jimport('joomla.application.component.controllerform');

class TZ_Portfolio_PlusControllerConfig extends JControllerForm
{
    protected $input    = null;

    public function __construct($config = array())
    {
        $this -> input  = Factory::getApplication()->input;

        parent::__construct($config);

    }

    function ApplyConfig(){
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data   = $this->input->post->get('jform', array(), 'array');
        $model  = $this -> getModel();
        if($model -> saveConfig($data)){
            $this -> setRedirect('index.php?option=com_tz_portfolio_plus&view=config&layout=image&tmpl=component');
        }

    }
    function SaveConfig(){
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data   = $this->input->post->get('jform', array(), 'array');
        $model  = $this -> getModel();
        $model -> saveConfig($data);

        $doc    = Factory::getApplication() -> getDocument();
        $doc -> addScriptDeclaration('window.parent.SqueezeBox.close();');
        return true;
    }
}