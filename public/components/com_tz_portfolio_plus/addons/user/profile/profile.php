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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;

class PlgTZ_Portfolio_PlusUserProfile extends TZ_Portfolio_PlusPlugin
{
    protected $autoloadLanguage = true;

    public function onContentPrepareForm($form, $data){
        $app    = Factory::getApplication();
        $name   = $form->getName();

        if($app -> isClient('administrator')){
            if($name == 'com_users.user' || $name == 'com_admin.profile') {
                JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR
                    .'models'.DIRECTORY_SEPARATOR.'fields');
                JForm::addFormPath(__DIR__.'/forms');

                $file   = Path::clean(__DIR__.'/forms/profile.xml');
                if(file_exists($file)){
                    $form -> loadFile($file, false);
                }
            }
        }else{
            if($name == 'com_users.profile') {
                JForm::addFieldPath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR
                    .'models'.DIRECTORY_SEPARATOR.'fields');
                JForm::addFormPath(__DIR__.'/forms');

                $file   = Path::clean(__DIR__.'/forms/profile.xml');
                if(file_exists($file)){
                    $form -> loadFile($file, false);
                }
            }
        }
        return parent::onContentPrepareForm($form, $data);
    }


    public function onAfterDisplayAdditionInfo($context, &$article, $params, $page = 0, $layout = 'default'){}
    public function onContentDisplayListView($context, &$article, $params, $page = 0, $layout = 'default'){}
    public function onContentDisplayArticleView($context, &$article, $params, $page = 0, $layout = 'default'){}
    public function onBeforeDisplayAdditionInfo($context, &$article, $params, $page = 0, $layout = 'default'){}
    public function onContentAfterSave($context, $data, $isnew){}

    /** Display author about for listing or article view.
     * @param string $context
     * @param int $authorId The id of user to get information of user
     * @param string $params the params of listing or article view.
     * @param string $page
     * @param string $layout the layout of add-on similar listing or article view.
     **/
    public function onContentDisplayAuthorAbout($context, $authorId, $params, &$article = null, $page = 0, $layout = 'default'){

        list($extension, $vName)   = explode('.', $context);

        if($extension == 'module' || $extension == 'modules'){
            if($path = $this -> getModuleLayout($this -> _type, $this -> _name, $extension, $vName, $layout, $params)){
                // Display html
                ob_start();
                include $path;
                $html = ob_get_contents();
                ob_end_clean();
                $html = trim($html);
                return $html;
            }
        }else {
            tzportfolioplusimport('plugin.modelitem');

            $addon      = TZ_Portfolio_PlusPluginHelper::getPlugin($this -> _type, $this -> _name);

            if($controller = TZ_Portfolio_PlusPluginHelper::getAddonController($addon -> id, array(
                'article' => $article,
                'authorId' => $authorId,
                'trigger_params' => $params
            ))){
                $input      = Factory::getApplication()->input;
                $task   = $input->get('addon_task');
                $input->set('addon_view', $vName);
                $input->set('addon_layout', 'default');
                if($layout) {
                    $input->set('addon_layout', $layout);
                }

                $html   = null;
                try {
                    ob_start();
                    $controller->execute($task);
                    $controller->redirect();
                    $html = ob_get_contents();
                    ob_end_clean();
                }catch (Exception $e){
                    return false;
                }

                if($html){
                    $html   = trim($html);
                }
                $input -> set('addon_task', null);
                return $html;

            }
        }
    }
}