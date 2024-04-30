<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2017 tzportfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - http://tzportfolio.com/forum

# Family website: http://www.templaza.com

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Layout\FileLayout;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

FormHelper::loadFieldClass('list');

class JFormFieldTZTag extends JFormFieldList{

    public $type        = 'TZTag';

    protected function getInput()
    {
        // AJAX mode requires ajax-chosen
        if (!$this->isNested())
        {
            // Get the field id
            $id    = isset($this->element['id']) ? $this->element['id'] : null;
            $cssId = '#' . $this->getId($id, $this->element['name']);

            $minTermLength = 1;

            $is_j4  = COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE;

            if($is_j4) {
                $displayData = $this->getLayoutData();

                $displayData['selector']        = $cssId;
                $displayData['allowCustom']     = $this->allowCustom();
                $displayData['options']         = $this -> getOptions();
                $displayData['minTermLength']   = $minTermLength;
                $displayData['remoteSearch']    = $this -> isRemoteSearch();

                return $this->getRenderer('form.field.tag')->render($displayData);
            }else{
                $displayData = array(
                    'minTermLength' => $minTermLength,
                    'selector' => $cssId,
                    'allowCustom' => TZ_Portfolio_PlusUser::getUser()->authorise('core.create',
                        'com_tz_portfolio_plus.tag') ? $this->allowCustom() : false,
                );

                $this->getRenderer('form.field.tag')->render($displayData);
            }
        }

        if (!is_array($this->value) && !empty($this->value))
        {
            if ($this->value instanceof TagsHelper)
            {
                if (empty($this->value->tags))
                {
                    $this->value = array();
                }
                else
                {
                    $this->value = $this->value->tags;
                }
            }

            // String in format 2,5,4
            if (is_string($this->value))
            {
                $this->value = explode(',', $this->value);
            }
        }

        return parent::getInput();
    }

    protected function getOptions()
    {
        $published = $this->element['published']?: array(0, 1);
        $app       = Factory::getApplication();

        $tag       = $app->getLanguage()->getTag();

        $db     = TZ_Portfolio_PlusDatabase::getDbo();
        $query  = $db -> getQuery(true);
        $query -> select('DISTINCT id AS value, title AS text, published');
        $query -> from('#__tz_portfolio_plus_tags');

        // Filter on the published state
        if (is_numeric($published))
        {
            $query->where('published = ' . (int) $published);
        }
        elseif (is_array($published))
        {
            $published = ArrayHelper::toInteger($published);
            $query->where('published IN (' . implode(',', $published) . ')');
        }

        // Get the options.
        $db->setQuery($query);

        try
        {
            $options = $db->loadObjectList();
        }
        catch (\RuntimeException $e)
        {
            return array();
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        // Prepare nested data
        if ($this->isNested())
        {
            $this->prepareOptionsNested($options);
        }
//        else
//        {
//            $options = TagsHelper::convertPathsToNames($options);
//        }

        return $options;
    }

    protected function prepareOptionsNested(&$options)
    {
        if ($options)
        {
            foreach ($options as &$option)
            {
                $repeat = (isset($option->level) && $option->level - 1 >= 0) ? $option->level - 1 : 0;
                $option->text = str_repeat('- ', $repeat) . $option->text;
            }
        }

        return $options;
    }

    public function isNested()
    {
        if ($this->isNested === null)
        {
            // If mode="nested" || ( mode not set & config = nested )
            if (isset($this->element['mode']) && (string) $this->element['mode'] === 'nested')
            {
                $this->isNested = true;
            }
        }

        return $this->isNested;
    }

    public function allowCustom()
    {
        if (isset($this->element['custom']) && (string) $this->element['custom'] === 'deny')
        {
            return false;
        }

        return true;
    }

    protected function getRenderer($layoutId = 'default')
    {
        $renderer   = new FileLayout($layoutId,COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.'/layouts');

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

    public function isRemoteSearch()
    {
        if ($this->element['remote-search']) {
            return !\in_array((string) $this->element['remote-search'], array('0', 'false', ''));
        }

        return true;
    }
}