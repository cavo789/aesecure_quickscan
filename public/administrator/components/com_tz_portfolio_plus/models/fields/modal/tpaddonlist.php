<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Copyright Copyright (C) 2015-2019 TZ Portfolio (http://tzportfolio.com). All Rights Reserved.

-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

use Joomla\CMS\Layout\FileLayout;
use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

JFormHelper::loadFieldClass('checkboxes');

class JFormFieldModal_TPAddOnList extends JFormFieldCheckboxes
{

    protected $type             = 'Modal_TPPAddOnList';
    protected $multiple         = false;
    protected $excludes         = array();
    protected $forceMultiple    = false;
    protected $layout           = 'form.field.modals.addon';

    // @var element["excludes"] is ["folder:element","folder:element2"] or folder:element
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);
        $this -> multiple   = false;
        if($element){

            if(isset($element['edit'])){
                $edit   = (string) $element['edit'];
                if($edit == 'true' || $edit == '1'){
                    $this -> element['edit']    = true;
                }else{
                    $this -> element['edit']    = false;
                }
            }
            if(isset($element['multiple'])){
                $multiple   = (string) $element['multiple'];
                if($multiple == 'true' || $multiple == '1' || $multiple == 'multiple'){
                    $this -> multiple    = true;
                    $this -> forceMultiple  = true;
                }
            }
        }
        if($this -> multiple){
            $this -> layout = 'form.field.modals.addons';
        }
        if(!$this -> multiple){
            $this -> value  = (int) $this -> value;
        }

        if(isset($element['excludes'])){
            $excludes   = json_decode($element['excludes'], true);
            if(!$excludes){
                $excludes   = (array) $element['excludes'];
            }
            if(count($excludes)){
                JLoader::import('com_tz_portfolio_plus.libraries.plugin.helper', JPATH_ADMINISTRATOR.'/components');
                foreach($excludes as $ex){
                    if(strpos( $ex,':') !== false){
                        list($type, $el)    = explode(':', $ex, 2);
                        if($ado = TZ_Portfolio_PlusPluginHelper::getPlugin($type, $el)){
                            $this -> excludes[] = $ado -> id;
                        }
                    }
                }
            }
        }

        return $return;
    }
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        $data['title']      = null;
        $data['items']      = false;
        $data['submitform'] = false;
        $data['link']       = 'index.php?option=com_tz_portfolio_plus&view=addons&layout=modal'
            .($this -> multiple?'&ismultiple=true':'');

        if($items  = $this -> _getItems($this -> value)){
            if($this -> multiple) {
                $data['items'] = $items;
//                $addOnIds   = ArrayHelper::getColumn($items, 'id');
//                if(count($addOnIds)){
//                    $data['link']   .= '&filter_exclude_ids[]='.join('&filter_exclude_ids[]=', $addOnIds);
//                }
            }else {
                $data['title']  = $items[0]->title;
//                $data['link']  .= '&filter_exclude_ids[]='.$items[0] -> id;
            }
        }
        if (isset($this->element['language']))
        {
            $data['language']   = $this -> element['language'];
            $data['link']      .= '&forcedLanguage='.$this->element['language'];
        }

        if(isset($this -> element['submitform']) && $this -> element['submitform']){
            $data['submitform'] = boolval($this -> element['submitform']);
        }

        $data['excludes']   = $this -> excludes;

        $data['link']   .= '&tmpl=component';

        return $data;
    }

    protected function getRenderer($layoutId = 'default')
    {
        $renderer = new FileLayout($layoutId,COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/layouts');

        $renderer->setDebug($this->isDebugEnabled());

        $layoutPaths = $this->getLayoutPaths();

        if ($layoutPaths)
        {
            $renderer->setIncludePaths($layoutPaths);
        }

        return $renderer;
    }

    protected function getLayoutPaths()
    {
        return array();
    }

    protected function _getItems($ids){
//        JLoader::import('com_tz_portfolio_plus.helpers.addons', JPATH_ADMINISTRATOR.'/components');
//        $addons = TZ_Portfolio_PlusHelperAddons::getAddons();

        if($ids){
            $db     = TZ_Portfolio_PlusDatabase::getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('e.id, e.name AS title');
            $query -> from('#__tz_portfolio_plus_extensions AS e');
//            $query -> join('LEFT','#__tz_portfolio_plus_content_category_map AS m ON m.contentid = a.id');
//            $query -> join('LEFT','#__tz_portfolio_plus_categories AS c ON c.id = m.catid');
            if(is_array($ids)){
                $query -> where('e.id IN('.implode(',',$ids).')');
            }else{
                if(is_string($ids) && strpos($ids, ',')){
                    $query -> where('e.id IN('.$ids.')');
                }else {
                    $query->where('e.id =' . ((int)$ids));
                }
            }
            $query -> group('id');
            $db -> setQuery($query);
            if($rows = $db -> loadObjectList()){
                return $rows;
            }
        }
        return false;
    }
}
