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

jimport('joomla.application.component.controllerform');

class TZ_Portfolio_PlusControllerDashboard extends JControllerForm
{
    public function feedBlog(){

        $app        = Factory::getApplication();
        $document   = Factory::getApplication() -> getDocument();
        $vType      = $document->getType();
        $vType      = 'ajax';
        $vName      = $this -> view_item;
        $vLayout    = $this->input->get('layout', 'default', 'string');
        $json       = new JResponseJson();

        $app -> setHeader('Content-Type', 'application/json; charset=' . $app->charSet, true);
        $app -> sendHeaders();

        if($view = $this->getView($vName, $vType, '', array('layout' => $vLayout))) {

            // Get/Create the model
            $model = $this->getModel();
            if (!$model) {
                $json -> success    = false;
                $json -> message    = $model -> getError();
                echo json_encode($json);
                $app -> close();
            }

            // Push the model into the view (as default)
            $view->setModel($model, true);

            // Display the view
            ob_start();
            $view->display('feed');
            $content    = ob_get_contents();
            ob_end_clean();
        }
    }

    public function checkUpdate(){

        $json   = new JResponseJson();

        try{
            $xml    = simplexml_load_file(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/tz_portfolio_plus.xml');

            if(isset($xml -> updateservers)){
                $updateServers = $xml -> updateservers;
                if(isset($updateServers -> server )){
                    $server     = $updateServers -> server;
                    $updateLink = trim((string) $server);
                    $pirority   = $server['pirority'];


//                    $update = new \Joomla\CMS\Updater\Update();
//                    if($update ->loadFromXml($updateLink)){
//                        $version    = $update -> get('version');
//                        $json -> data   = $version -> _data;
//                    }

//                    var_dump($update);
//                    var_dump($updateLink);
//                    die();

//                    try {
//                    $updateLink = 'http://feeds.feedburner.com/tzportfolio/blog';
                        $updateXML = simplexml_load_file($updateLink);
//                        var_dump($updateXML); die();
//                    }catch (Exception $exception){
//                        var_dump($exception); die();
//                    }
                    if(isset($updateXML -> update)){
                        $updateXML      = $updateXML -> update[$pirority - 1];
                        $json -> data   = (string) $updateXML -> version;
                    }
                }
            }
        }catch (Exception $exception){
            $json -> success    = false;
            $json -> message    = $exception -> getMessage();
        }
        echo json_encode($json);
        Factory::getApplication() -> close();
    }

    public function statistics(){

        $json   = new JResponseJson();

        $data   = array('addons' => array(), 'styles' => array());

        if($adoModels = JModelLegacy::getInstance('AddOns', 'TZ_Portfolio_PlusModel')) {
            $adoInstTotal   = $adoModels -> getTotal();
            $data['addons']['installed']  = $adoInstTotal;
            try{
//                if($adosUpdate = $adoModels -> getItemsUpdate()) {
                $adosUpdate = $adoModels -> getItemsUpdate();
                $adosUpdateTotal = $adosUpdate?count($adosUpdate):0;
                $data['addons']['update']  = $adosUpdateTotal;
//                }
            }catch (Exception $exception){}
        }
        if($adoModel = JModelLegacy::getInstance('AddOn', 'TZ_Portfolio_PlusModel')) {
            try {
                $addon = $adoModel->getItemsFromServer();
            }catch (Exception $exception){}

            $adoTotal   = $adoModel->getState('list.total', 0);
            $data['addons']['total']  = $adoTotal - 1
                + TZ_Portfolio_PlusHelperAddons::getTotal(array('protected' => 1));
        }
        if($stlModel = JModelLegacy::getInstance('Template', 'TZ_Portfolio_PlusModel')) {
            try {
                $style = $stlModel->getItemsFromServer();
            }catch (Exception $exception){}
            $stlTotal   = $stlModel->getState('list.total', 0);
            $data['styles']['total'] = $stlTotal + TZ_Portfolio_PlusHelperTemplates::getTotal(array('protected' => 1));
        }
        if($stlModels = JModelLegacy::getInstance('Templates', 'TZ_Portfolio_PlusModel')) {
            $stlInstTotal   = $stlModels -> getTotal();
            $data['styles']['installed'] = $stlInstTotal;
            try {
//                if($stlsUpdate = $stlModels -> getItemsUpdate()) {
                $stlsUpdate = $stlModels->getItemsUpdate();
                $stlsUpdateTotal = $stlsUpdate?count($stlsUpdate):0;
                $data['styles']['update'] = $stlsUpdateTotal;
//            }

            }catch (Exception $exception){}
        }
        if(count($data)){
            $json -> data   = $data;
        }
        echo json_encode($json);
        Factory::getApplication() -> close();
//        die('43243');
    }
}