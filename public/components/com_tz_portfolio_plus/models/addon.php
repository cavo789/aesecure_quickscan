<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2011-2017 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

class TZ_Portfolio_PlusModelAddOn extends JModelLegacy{

    protected function populateState(){
        $app    = JFactory::getApplication();
        $params = $app -> getParams();
        $this -> setState('params',$params);

        $addon_id = $app -> input -> getInt('addon_id');
        $this -> setState('addon.addon_id', $addon_id);
    }

    public function getRenderAddonView(){
        if($addon_id = $this -> getState('addon.addon_id')){
            if($addon      = TZ_Portfolio_PlusPluginHelper::getPluginById($addon_id)) {

                if($addonObj = TZ_Portfolio_PlusPluginHelper::getInstance($addon->type, $addon->name)) {
                    if(method_exists($addonObj, 'onRenderAddonView')) {
                        ob_start();
                        $addonObj->onRenderAddonView();
                        $html = ob_get_contents();
                        ob_end_clean();
                        return $html;
                    }
                }
            }
        }
        return false;
    }
}