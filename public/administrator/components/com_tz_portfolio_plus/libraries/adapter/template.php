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

namespace TZ_Portfolio_Plus\Installer\Adapter;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Adapter\TemplateAdapter;

\JLoader::import('com_tz_portfolio_plus.includes.framework',JPATH_ADMINISTRATOR.'/components');

class TZ_Portfolio_PlusInstallerTemplateAdapter extends TemplateAdapter{

    public function __construct(\JInstaller $parent, \JDatabaseDriver $db, array $options = array())
    {

        parent::__construct($parent, $db, $options);

        // Get a generic TZ_Portfolio_PlusTableExtension instance for use if not already loaded
        if (!($this->extension instanceof TZ_Portfolio_PlusTableExtensions)) {
            \JTable::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH . DIRECTORY_SEPARATOR . 'tables');
            $this->extension = \JTable::getInstance('Extensions', 'TZ_Portfolio_PlusTable');
        }

        if(is_object($this -> extension) && isset($this -> extension -> id)) {
            $this->extension->extension_id = $this->extension->id;
        }

        $type   = strtolower($this -> type);
        $type   = preg_replace('/^TZ_Portfolio_PlusInstaller/i', '', $type);
        $type   = preg_replace('/^TZ_Portfolio_PlusInstallerAdapter/i', '', $type);

        $this->type = 'tz_portfolio_plus-'.strtolower($type);
    }

    protected function checkExistingExtension()
    {
        try
        {
            $this->currentExtensionId = $this->extension->find(
                array(
                    'element'   => $this->element,
                    'type'      => $this->type
                )
            );
        }
        catch (RuntimeException $e)
        {
            // Install failed, roll back changes
            throw new RuntimeException(
                \JText::sprintf(
                    'JLIB_INSTALLER_ABORT_ROLLBACK',
                    \JText::_('JLIB_INSTALLER_' . $this->route),
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }

    protected function setupInstallPaths()
    {
        // Set the template root path
        if (empty($this->element))
        {
            throw new RuntimeException(
                \JText::sprintf(
                    'JLIB_INSTALLER_ABORT_MOD_INSTALL_NOFILE',
                    \JText::_('JLIB_INSTALLER_' . strtoupper($this->route))
                )
            );
        }

        $this->parent->setPath('extension_root', COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . '/' . $this->element);
    }

    protected function copyBaseFiles()
    {
        $uniqid = md5($this->element);
        $uniqid = substr($uniqid, 0, 10);
        $path   = $this -> parent -> getPath('extension_root');
        $nPath  = $path.'__'.$uniqid;
        @rename($path, $nPath);

        parent::copyBaseFiles();

        // Remove old folder path
        if(is_dir($path)){
            // Copy config files
            if(Folder::exists($nPath.'/config')){
                $cfFiles    = Folder::files($nPath.'/config', '.json');
                if(count($cfFiles)){
                    foreach($cfFiles as $cfFile){
                        if(!File::exists($path.'/config/'.$cfFile)){
                            File::copy($nPath.'/config/'.$cfFile, $path.'/config/'.$cfFile);
                        }
                    }
                }
            }

            // Copy language files
            if(Folder::exists($nPath.'/language')){
                $cFolders   = Folder::folders($nPath.'/language');
                if(count($cFolders)){
                    foreach($cFolders as $cFolder){
                        if(!Folder::exists($path.'/language/'.$cFolder)){
                            Folder::copy($nPath.'/language/'.$cFolder, $path.'/language/'.$cFolder);
                        }
                    }
                }
            }
            Folder::delete($nPath);
        }else{
            @rename($nPath, $path);
        }
    }

    protected function storeExtension()
    {
        // Discover installs are stored a little differently
        if ($this->route == 'discover_install')
        {
            $manifest_details = \JInstaller::parseXMLInstallFile($this->parent->getPath('manifest'));

            $this->extension->manifest_cache    = json_encode($manifest_details);
            $this->extension->state             = 0;
            $this->extension->name              = $manifest_details['name'];
            $this->extension->published         = 1;
            $this->extension->params            = $this->parent->getParams();
            $this->extension->access            = 1;

            if(!isset($this ->extension ->protected) || (isset($this -> extension ->protected)
                    && !$this ->extension ->protected)) {
                $this->extension->protected = 0;
            }

            if (!$this->extension->store())
            {
                // Install failed, roll back changes
                throw new RuntimeException(\JText::_('JLIB_INSTALLER_ERROR_TPL_DISCOVER_STORE_DETAILS'));
            }

            return;
        }

        // Was there a template already installed with the same name?
        if ($this->currentExtensionId)
        {
            if (!$this->parent->isOverwrite())
            {
                // Install failed, roll back changes
                throw new RuntimeException(
                    \JText::_('JLIB_INSTALLER_ABORT_TPL_INSTALL_ALREADY_INSTALLED')
                );
            }

            // Load the entry and update the manifest_cache
            $this->extension->load($this->currentExtensionId);
        }
        else
        {
            $this->extension->type = 'tz_portfolio_plus-template';
            $this->extension->element = $this->element;

            // There is no folder for templates
            $this->extension->folder = '';
            $this->extension->published = 1;
            $this->extension->access = 1;
            $this->extension->params = $this->parent->getParams();

            if(!isset($this ->extension ->protected) || (isset($this -> extension ->protected)
                    && !$this ->extension ->protected)) {
                $this->extension->protected = 0;
            }
        }

        // Name might change in an update
        $this->extension->name = $this->name;
        $this->extension->manifest_cache = $this->parent->generateManifestCache();

        unset($this -> extension -> extension_id);

        if (!$this->extension->store())
        {
            // Install failed, roll back changes
            throw new RuntimeException(
                \JText::sprintf(
                    'JLIB_INSTALLER_ABORT_ROLLBACK',
                    \JText::_('JLIB_INSTALLER_' . strtoupper($this->route)),
                    $this->extension->getError()
                )
            );
        }

        // Set extension_id = id because table extension of joomla with key is "extension_id" so plus is "id"
        $this -> extension -> extension_id  = $this -> extension -> id;
    }


    protected function parseQueries()
    {
        if (in_array($this->route, array('install', 'discover_install')))
        {
            $db    = $this->db;
            $lang  = Factory::getApplication() -> getLanguage();
            $debug = $lang->setDebug(false);

            $columns = array($db->quoteName('template'),
                $db->quoteName('home'),
                $db->quoteName('title'),
                $db->quoteName('params'),
                $db->quoteName('protected'),
                $db->quoteName('layout'),
                $db->quoteName('preset')
            );

            $values = array(
                $db->quote($this->extension->element), $db->quote(0),
                $db->quote(\JText::sprintf('JLIB_INSTALLER_DEFAULT_STYLE', \JText::_($this->extension->name))),
                $db->quote($this->extension->params),
                $this->extension->protected,
                $db->quote(''),
                $db->quote(''));

            $lang->setDebug($debug);

            // Insert record in #__template_styles
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('#__tz_portfolio_plus_templates'))
                ->columns($columns)
                ->values(implode(',', $values));

            // There is a chance this could fail but we don't care...
            $db->setQuery($query)->execute();
        }
    }

    public function loadLanguage($path = null)
    {
        $source   = $this->parent->getPath('source');
        $basePath = JPATH_SITE.'/components/com_tz_portfolio_plus';

        if (!$source)
        {
            $this->parent->setPath('source', $basePath . '/templates/' . $this->parent->extension->element);
        }

        $base = JPATH_SITE.'/components/com_tz_portfolio_plus';
        $extension = 'tpl_' . $this->getName();
        $source    = $path ?: $base . '/templates/' . $this->getName();

        $this->doLoadLanguage($extension, $source, $base);
    }
}