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

use Joomla\CMS\Access\Access;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

JFormHelper::loadFieldClass('Rules');
tzportfolioplusimport('access');

class JFormFieldTZRules extends JFormFieldRules
{
    protected $type = 'TZRules';

    protected $addon;
    protected $parent;
    protected $addonGroup;

    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return)
        {
            $this -> parent         = $this->element['parent'] ? (string) $this->element['parent'] : '';
            $this -> addonGroup     = $this->element['group'] ? (string) $this->element['group'] : '';
            $this -> addon          = $this->element['addon'] ? (string) $this->element['addon'] : '';
        }

        return $return;
    }

    protected function getInput()
    {
        JHtml::_('bootstrap.tooltip');
        // Add Javascript for permission change
        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
            \JHtml::_('script', 'system/fields/permissions.min.js', array('version' => 'auto', 'relative' => true));
        }else {
            JHtml::_('script', 'system/permissions.js', array('version' => 'auto', 'relative' => true));
        }

        // Load JavaScript message titles
        JText::script('ERROR');
        JText::script('WARNING');
        JText::script('NOTICE');
        JText::script('MESSAGE');

        // Add strings for JavaScript error translations.
        JText::script('JLIB_JS_AJAX_ERROR_CONNECTION_ABORT');
        JText::script('JLIB_JS_AJAX_ERROR_NO_CONTENT');
        JText::script('JLIB_JS_AJAX_ERROR_OTHER');
        JText::script('JLIB_JS_AJAX_ERROR_PARSE');
        JText::script('JLIB_JS_AJAX_ERROR_TIMEOUT');

        // Initialise some field attributes.
        $section    = $this->section;
        $assetField = $this->assetField;
        $component  = empty($this->component) ? 'com_tz_portfolio_plus' : $this->component;
        $addon      = $this -> addon;
        $addonGroup = $this -> addonGroup;

        // Current view is global config?
        $isGlobalConfig = ($component === 'com_tz_portfolio_plus' && $section === 'component');

        // Get the actions for the asset.
        $actions = Access::getActionsFromFile(
            JPATH_ADMINISTRATOR . '/components/' . $component . '/access.xml',
            "/access/section[@name='" . $section . "']/");
        if($addon && $addonGroup) {
            $actions = TZ_Portfolio_PlusAccess::getAddOnActions($addon, $addonGroup, $section);
        }

        // Iterate over the children and add to the actions.
        foreach ($this->element->children() as $el)
        {
            if ($el->getName() == 'action')
            {
                $actions[] = (object) array(
                    'name' => (string) $el['name'],
                    'title' => (string) $el['title'],
                    'description' => (string) $el['description'],
                );
            }
        }

        // Get the asset id.
        // Note that for global configuration, com_config injects asset_id = 1 into the form.
        $assetId       = $this->form->getValue($assetField);
        $newItem       = empty($assetId) && $isGlobalConfig === false && $section !== 'component';
        $parentAssetId = null;

        // If the asset id is empty (component or new item).
        if (empty($assetId))
        {
            $parent = $this -> parent;
            if($addon && $addonGroup){
                $parent = 'addon';
            }

            // Get the component asset id as fallback.
            $db = TZ_Portfolio_PlusDatabase::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__assets'))
                ->where($db->quoteName('name') . ' = ' . $db->quote($component.($parent ?'.'.$parent:'')));

            $db->setQuery($query);

            $assetId = (int) $db->loadResult();

            /**
             * @to do: incorrect info
             * When creating a new item (not saving) it uses the calculated permissions from the component (item <-> component <-> global config).
             * But if we have a section too (item <-> section(s) <-> component <-> global config) this is not correct.
             * Also, currently it uses the component permission, but should use the calculated permissions for achild of the component/section.
             */
        }

        // If not in global config we need the parent_id asset to calculate permissions.
        if (!$isGlobalConfig)
        {
            // In this case we need to get the component rules too.
            $db = TZ_Portfolio_PlusDatabase::getDbo();

            $query = $db->getQuery(true)
                ->select($db->quoteName('parent_id'))
                ->from($db->quoteName('#__assets'))
                ->where($db->quoteName('id') . ' = ' . $assetId);

            $db->setQuery($query);

            $parentAssetId = (int) $db->loadResult();
        }

        // Full width format.

        // Get the rules for just this asset (non-recursive).
        $assetRules = JAccess::getAssetRules($assetId, false, false);

        // Get the available user groups.
        $groups = $this->getUserGroups();

        // Ajax request data.
        $ajaxUri = JRoute::_('index.php?option=com_config&task=config.store&format=json&' . JSession::getFormToken() . '=1');

        // Prepare output
        $html = array();

        // Description
        $html[] = '<p class="rule-desc">' . \JText::_('JLIB_RULES_SETTINGS_DESC') . '</p>';

        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            // Begin tabs
            $html[] = '<div class="row mb-2" data-ajaxuri="' . $ajaxUri . '" id="permissions-sliders">';

            // Building tab nav
            $html[] = '<div class="col-md-3">';
            $html[] = '<ul class="nav nav-pills flex-column">';
        }else{
            // Begin tabs
            $html[] = '<div class="tabbable tabs-left" data-ajaxuri="' . $ajaxUri . '" id="permissions-sliders">';

            // Building tab nav
            $html[] = '<ul class="nav nav-tabs">';
        }

        foreach ($groups as $group)
        {
            if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
                // Initial Active Tab
                $active = (int) $group->value === 1 ? ' active' : '';

                $html[] = '<li class="nav-item">';
                $html[] = '<a class="nav-link' . $active . '" href="#permission-' . $group->value . '" data-toggle="tab">';
            }else{
                // Initial Active Tab
                $active = (int) $group->value === 1 ? ' class="active"' : '';

                $html[] = '<li' . $active . '>';
                $html[] = '<a href="#permission-' . $group->value . '" data-toggle="tab">';
            }
            $html[] = \JLayoutHelper::render('joomla.html.treeprefix', array('level' => $group->level + 1)) . $group->text;
            $html[] = '</a>';
            $html[] = '</li>';
        }

        $html[] = '</ul>';

        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            $html[] = '</div>';
        }

        $html[] = '<div class="tab-content col-md-9">';

        // Start a row for each user group.
        foreach ($groups as $group)
        {
            // Initial Active Pane
            $active = (int) $group->value === 1 ? ' active' : '';

            $html[] = '<div class="tab-pane' . $active . '" id="permission-' . $group->value . '">';
            $html[] = '<table class="table table-striped">';
            $html[] = '<thead>';
            $html[] = '<tr>';

            $html[] = '<th class="actions" id="actions-th' . $group->value . '">';
            $html[] = '<span class="acl-action">' . \JText::_('JLIB_RULES_ACTION') . '</span>';
            $html[] = '</th>';

            $html[] = '<th class="settings" id="settings-th' . $group->value . '">';
            $html[] = '<span class="acl-action">' . \JText::_('JLIB_RULES_SELECT_SETTING') . '</span>';
            $html[] = '</th>';

            $html[] = '<th id="aclactionth' . $group->value . '">';
            $html[] = '<span class="acl-action">' . \JText::_('JLIB_RULES_CALCULATED_SETTING') . '</span>';
            $html[] = '</th>';

            $html[] = '</tr>';
            $html[] = '</thead>';
            $html[] = '<tbody>';

            // Check if this group has super user permissions
            $isSuperUserGroup = \JAccess::checkGroup($group->value, 'core.admin');

            foreach ($actions as $action)
            {
                $html[] = '<tr>';
                $html[] = '<td headers="actions-th' . $group->value . '">';
                $html[] = '<label for="' . $this->id . '_' . $action->name . '_' . $group->value . '" class="hasTooltip" title="'
                    . \JHtml::_('tooltipText', $action->title, $action->description) . '">';
                $html[] = \JText::_($action->title);
                $html[] = '</label>';
                $html[] = '</td>';

                $html[] = '<td headers="settings-th' . $group->value . '">';

                if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                    $html[] = '<select onchange="sendPermissions.call(this, event)" data-chosen="true" class="custom-select novalidate"'
                        . ' name="' . $this->name . '[' . $action->name . '][' . $group->value . ']"'
                        . ' id="' . $this->id . '_' . $action->name . '_' . $group->value . '"'
                        . ' title="' . strip_tags(\JText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', \JText::_($action->title), trim($group->text))) . '">';
                }else{
                    $html[] = '<select onchange="sendPermissions.call(this, event)" data-chosen="true" class="input-small novalidate"'
                        . ' name="' . $this->name . '[' . $action->name . '][' . $group->value . ']"'
                        . ' id="' . $this->id . '_' . $action->name	. '_' . $group->value . '"'
                        . ' title="' . strip_tags(JText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', JText::_($action->title), trim($group->text))) . '">';
                }

                /**
                 * Possible values:
                 * null = not set means inherited
                 * false = denied
                 * true = allowed
                 */

                // Get the actual setting for the action for this group.
                $assetRule = $newItem === false ? $assetRules->allow($action->name, $group->value) : null;

                // Build the dropdowns for the permissions sliders

                // The parent group has "Not Set", all children can rightly "Inherit" from that.
                $html[] = '<option value=""' . ($assetRule === null ? ' selected="selected"' : '') . '>'
                    . \JText::_(empty($group->parent_id) && $isGlobalConfig ? 'JLIB_RULES_NOT_SET' : 'JLIB_RULES_INHERITED') . '</option>';
                $html[] = '<option value="1"' . ($assetRule === true ? ' selected="selected"' : '') . '>' . \JText::_('JLIB_RULES_ALLOWED')
                    . '</option>';
                $html[] = '<option value="0"' . ($assetRule === false ? ' selected="selected"' : '') . '>' . \JText::_('JLIB_RULES_DENIED')
                    . '</option>';

                $html[] = '</select>&#160; ';

                $html[] = '<span id="icon_' . $this->id . '_' . $action->name . '_' . $group->value . '"' . '></span>';
                $html[] = '</td>';

                // Build the Calculated Settings column.
                $html[] = '<td headers="aclactionth' . $group->value . '">';

                $result = array();

                // Get the group, group parent id, and group global config recursive calculated permission for the chosen action.
                $inheritedGroupRule            = \JAccess::checkGroup((int) $group->value, $action->name, $assetId);
                $inheritedGroupParentAssetRule = !empty($parentAssetId) ? \JAccess::checkGroup($group->value, $action->name, $parentAssetId) : null;
                $inheritedParentGroupRule      = !empty($group->parent_id) ? \JAccess::checkGroup($group->parent_id, $action->name, $assetId) : null;

                // Current group is a Super User group, so calculated setting is "Allowed (Super User)".
                if ($isSuperUserGroup)
                {
                    if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                        $result['class'] = 'badge badge-success';
                    }else{
                        $result['class'] = 'label label-success';
                    }
                    $result['text'] = '<span class="icon-lock icon-white"></span>' . \JText::_('JLIB_RULES_ALLOWED_ADMIN');
                }
                // Not super user.
                else
                {
                    // First get the real recursive calculated setting and add (Inherited) to it.

                    // If recursive calculated setting is "Denied" or null. Calculated permission is "Not Allowed (Inherited)".
                    if ($inheritedGroupRule === null || $inheritedGroupRule === false)
                    {
                        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                            $result['class'] = 'badge badge-danger';
                        }else{
                            $result['class'] = 'label label-important';
                        }

                        $result['text']  = \JText::_('JLIB_RULES_NOT_ALLOWED_INHERITED');
                    }
                    // If recursive calculated setting is "Allowed". Calculated permission is "Allowed (Inherited)".
                    else
                    {
                        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                            $result['class'] = 'badge badge-success';
                        }else{
                            $result['class'] = 'label label-success';
                        }
                        $result['text']  = \JText::_('JLIB_RULES_ALLOWED_INHERITED');
                    }

                    // Second part: Overwrite the calculated permissions labels if there is an explicit permission in the current group.

                    /**
                     * @to do: incorrect info
                     * If a component has a permission that doesn't exists in global config (ex: frontend editing in com_modules) by default
                     * we get "Not Allowed (Inherited)" when we should get "Not Allowed (Default)".
                     */

                    // If there is an explicit permission "Not Allowed". Calculated permission is "Not Allowed".
                    if ($assetRule === false)
                    {
                        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                            $result['class'] = 'badge badge-danger';
                        }else{
                            $result['class'] = 'label label-important';
                        }
                        $result['text']  = \JText::_('JLIB_RULES_NOT_ALLOWED');
                    }
                    // If there is an explicit permission is "Allowed". Calculated permission is "Allowed".
                    elseif ($assetRule === true)
                    {
                        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                            $result['class'] = 'badge badge-success';
                        }else{
                            $result['class'] = 'label label-success';
                        }
                        $result['text']  = \JText::_('JLIB_RULES_ALLOWED');
                    }

                    // Third part: Overwrite the calculated permissions labels for special cases.

                    // Global configuration with "Not Set" permission. Calculated permission is "Not Allowed (Default)".
                    if (empty($group->parent_id) && $isGlobalConfig === true && $assetRule === null)
                    {
                        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                            $result['class'] = 'badge badge-danger';
                        }else{
                            $result['class'] = 'label label-important';
                        }
                        $result['text']  = \JText::_('JLIB_RULES_NOT_ALLOWED_DEFAULT');
                    }

                    /**
                     * Component/Item with explicit "Denied" permission at parent Asset (Category, Component or Global config) configuration.
                     * Or some parent group has an explicit "Denied".
                     * Calculated permission is "Not Allowed (Locked)".
                     */
                    elseif ($inheritedGroupParentAssetRule === false || $inheritedParentGroupRule === false)
                    {
                        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
                            $result['class'] = 'badge badge-danger';
                        }else{
                            $result['class'] = 'label label-important';
                        }
                        $result['text']  = '<span class="icon-lock icon-white"></span>' . \JText::_('JLIB_RULES_NOT_ALLOWED_LOCKED');
                    }
                }

                $html[] = '<span class="' . $result['class'] . '">' . $result['text'] . '</span>';
                $html[] = '</td>';
                $html[] = '</tr>';
            }

            $html[] = '</tbody>';
            $html[] = '</table></div>';
        }

        $html[] = '</div></div>';
        $html[] = '<div class="alert alert-warning">';

        if ($section === 'component' || !$section)
        {
            $html[] = \JText::_('JLIB_RULES_SETTING_NOTES');
        }
        else
        {
            $html[] = \JText::_('JLIB_RULES_SETTING_NOTES_ITEM');
        }

        $html[] = '</div>';

        return implode("\n", $html);
    }
}