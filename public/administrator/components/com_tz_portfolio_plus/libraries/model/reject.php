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

class TZ_Portfolio_PlusModelRejectBase extends JModelAdmin
{
    protected function populateState()
    {
        parent::populateState();

        $app = Factory::getApplication();

        $cid = $app->input->get('cid', array(), 'array');
        $this -> setState('article.id', $cid);

        $return = $app->input->get('return', null, 'base64');
        $this->setState('return_page', base64_decode($return));
    }

    public function getTable($type = 'Content_Rejected', $prefix = 'TZ_Portfolio_PlusTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this -> option.'.'.$this -> getName(), $this -> getName(),
            array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form))
        {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app = Factory::getApplication();
        $data = $app->getUserState('com_tz_portfolio_plus.edit.'.$this -> getName().'.data', array());

        if (empty($data))
        {
            $data = $this-> getItem();
        }

        $this->preprocessData('com_tz_portfolio_plus.'.$this -> getName(), $data);

        return $data;
    }

    public function getItem($pk = null)
    {
        $item = parent::getItem();

        return $item;
    }

    public function save($data, $articleIds = array())
    {
        $table      = $this -> getTable();
        $tblContent = $this -> getTable('Content');

        if(count($articleIds)){
            foreach($articleIds as $i => $articleId){
                $_data  = $data;
                $table -> reset();
                $_data['id']    = 0;
                $_data['content_id'] = $articleId;
                if($table -> load(array('content_id' => $articleId))) {
                    $_data['id'] = $table->id;
                }
                $result = parent::save($_data);
                if($result){
                    $tblContent -> publish($articleId, -3);
                }
            }
        }
        return true;
    }


}
