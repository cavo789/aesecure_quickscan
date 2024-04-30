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

?>
<div aria-hidden="false" aria-labelledby="myLoadPresetTitle" role="dialog" tabindex="-1" id="loadPreset" class="modal<?php
echo !COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE?' tz-modal-sm':''; ?> fade">
    <div class="modal-dialog">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h3 id="myLoadPresetTitle" class="modal-title"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LOAD_PRESET');?></h3>
                <button aria-hidden="true" data-dismiss="modal" data-bs-dismiss="modal" class="btn-close close order-2" type="button"><?php
                 echo !COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE?'×':'';?></button>
            </div>
            <div class="modal-body p-3">
                <p><?php
                    echo JText::sprintf('COM_TZ_PORTFOLIO_PLUS_CLICK_TO_LOAD_PRESET','<span class="text-success">"'
                        .JText::_('COM_TZ_PORTFOLIO_PLUS_ACCEPT').'"</span>');
                    ?></p>
                <p><em class="text-warning"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_LOAD_PRESET_BOX_DESC');?></em></p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" data-bs-dismiss="modal" class="btn btn-default" type="button"><?php echo JText::_('JTOOLBAR_CLOSE');?></button>
                <button id="loadPresetAccept" class="btn btn-success" type="button"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ACCEPT');?></button>
            </div>
        </div>
    </div>
</div>
