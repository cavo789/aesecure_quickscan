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

// Initiasile related data.
require_once JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php';
$menuTypes = TZ_Portfolio_PlusHelper::getMenuLinks();
$user = Factory::getApplication() -> getIdentity();

?>
<div class="com_templates">
    <label id="jform_menuselect-lbl" for="jform_menuselect"><?php echo JText::_('JGLOBAL_MENU_SELECTION'); ?></label>
    <div class="btn-toolbar">
        <button type="button" class="btn btn-sm btn-secondary jform-rightbtn" onclick="jQuery('.chk-menulink').attr('checked', !jQuery('.chk-menulink').attr('checked'));">
            <i class="icon-checkbox-partial"></i> <?php echo JText::_('JGLOBAL_SELECTION_INVERT_ALL'); ?>
        </button>
    </div>
    <div id="menu-assignment" class="menu-assignment">
        <?php if($menuTypes){ ?>
        <ul class="menu-links">

            <?php foreach ($menuTypes as &$type) : ?>
                <li>
                    <div class="menu-links-block">
                        <button type="button" class="btn btn-sm btn-secondary mb-2 jform-rightbtn" onclick="jQuery('.<?php echo $type->menutype; ?>').attr('checked', !jQuery('.<?php echo $type->menutype; ?>').attr('checked'));">
                            <i class="icon-checkbox-partial"></i> <?php echo JText::_('JGLOBAL_SELECTION_INVERT'); ?>
                        </button>
                        <h5><?php echo $type->title ? $type->title : $type->menutype; ?></h5>

                        <?php foreach ($type->links as $link) :?>
                            <label class="checkbox small" for="link<?php echo (int) $link->value;?>" >
                                <input type="checkbox" name="jform[menus_assignment][]" value="<?php echo (int) $link->value;?>" id="link<?php echo (int) $link->value;?>"<?php if ($link-> params -> get('tz_template_style_id') == $this->item->id):?> checked="checked"<?php endif;?><?php if ($link->checked_out && $link->checked_out != $user->id):?> disabled="disabled"<?php else:?> class="chk-menulink <?php echo $type->menutype; ?>"<?php endif;?> />
                                <?php echo JLayoutHelper::render('joomla.html.treeprefix', array('level' => $link->level)) . $link->text; ?>
                                <?php if ($link-> params -> get('tz_template_style_id') == $this->item->id):?>
                                    <input type="hidden" name="jform[menus_assignment_old][]" value="<?php echo $link -> value;?>">
                                <?php endif;?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php } ?>
    </div>
</div>