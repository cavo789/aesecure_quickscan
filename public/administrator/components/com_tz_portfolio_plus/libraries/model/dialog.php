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
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class TZ_Portfolio_PlusModelDialogBase extends JModelAdmin
{
    protected $formName;
    protected $rejectModel;

    public function __construct()
    {
        $this -> rejectModel    = JModelLegacy::getInstance('Reject', 'TZ_Portfolio_PlusModel',
            array('ignore_request' => true));

        parent::__construct();
    }

    protected function populateState()
    {

        $app = Factory::getApplication();

        // Load state from the request.
        $cid = $app->input->get('cid', array(), 'array');
        $this->setState('article.id', $cid);

        $return = $app->input->get('return', null, 'base64');
        $this->setState('return_page', base64_decode($return));

        $this->setState('layout', $app->input->getString('layout'));

        $this -> rejectModel -> setState('article.id', $cid);

        $this -> setState('state.reject', $this -> rejectModel -> get('state'));

    }

    public function getFormReject($data = array(), $loadData = true)
    {
        $form   = false;

        if($model = JModelLegacy::getInstance('Reject', 'TZ_Portfolio_PlusModel')){
            $form   = $model -> getForm($data, $loadData);
        }

        return $form;
    }
    public function getForm($data = array(), $loadData = true)
    {
        return false;
    }

}
