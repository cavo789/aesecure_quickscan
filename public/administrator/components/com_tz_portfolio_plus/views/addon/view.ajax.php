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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

JLoader::register('TZ_Portfolio_PlusHelperAddon_Datas', COM_TZ_PORTFOLIO_PLUS_ADMIN_HELPERS_PATH
    .DIRECTORY_SEPARATOR.'addon_datas.php');

class TZ_Portfolio_PlusViewAddon extends JViewLegacy
{
    protected $state;
    protected $itemsServer;

    public function display($tpl = null)
    {
        $app    = Factory::getApplication();
        $json   = new JResponseJson();
        $data   = new stdClass();

        $this->state                = $this->get('State');
        if($this -> getLayout() == 'upload_list_item') {
            $this -> itemsServer       = $this -> get('ItemsFromServer');
            $paginationServer   = $this -> get('PaginationFromServer');
            $data -> html       = $this -> loadTemplate($tpl);

            $pagHtml    = $paginationServer -> getListFooter();
//            if(preg_match('/(onclick="document\.adminForm\.limitstart\.value=([0-9]+).*?"(>))/i', $pagHtml, $match)){
//                $pagHtml    = preg_replace('/(onclick="document\.adminForm\.limitstart\.value=([0-9]+).*?"(>))/i', 'data-tpp-limitstart="$2"$3', $pagHtml);
//            }
            $data -> pagination = $pagHtml;

            $json -> data   = $data;
        }

        $app -> setHeader('Content-Type', 'application/json; charset=' . $app->charSet, true);
        $app -> sendHeaders();

        echo json_encode($json);
        $app -> close();

    }
}