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

class TZ_Portfolio_PlusPluginModelItem extends JModelItem{

    protected $addon            = null;
    protected $article          = null;
    protected $trigger_params   = null;

    protected function populateState()
    {
        $input  = Factory::getApplication() -> input;
        $return = $input -> get('return', null, 'default', 'base64');
        $this->setState('return_page', ($return ? base64_decode($return) : ''));

        $this -> setState($this -> getName().'.addon', $this -> addon);
        $this -> setState($this -> getName().'.article', $this -> article);
        $params    = null;
        if($this -> addon){
            if(is_string($this -> addon -> params)) {
                $params = new Registry($this->addon->params);
            }else{
                $params = $this -> addon -> params;
            }
            $this -> addon -> params    = clone($params);
        }
        $this -> setState($this -> getName().'.addon', $this -> addon);

        if($trigger_params = $this -> trigger_params){
            if(is_string($trigger_params)){
                $trigger_params = new Registry($trigger_params);
            }
            if($params){
                $params -> merge($trigger_params);
            }else{
                $params    = $trigger_params;
            }
        }
        $this -> setState('params', clone($params));

        parent::populateState();
    }

    public function getItem($pk = null){
        if($article = $this -> article){
            return $this -> article;
        }
        return false;
    }

    public function getReturnPage()
    {
        return base64_encode($this->getState('return_page'));
    }
}