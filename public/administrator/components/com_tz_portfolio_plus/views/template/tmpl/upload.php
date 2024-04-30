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

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

JHtml::_('bootstrap.tooltip');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

$this->document->addStyleSheet(TZ_Portfolio_PlusUri::base() . '/vendor/intro/introjs.min.css', array('version' => 'v=2.9.3'));
$this->document->addScript(TZ_Portfolio_PlusUri::base() . '/vendor/intro/intro.min.js', array('version' => 'v=2.9.3'));
$this->document->addScript(TZ_Portfolio_PlusUri::base() . '/js/introguide.min.js', array('version' => 'v=2.9.3'));

if(Factory::getApplication() -> getLanguage() -> isRtl()) {
    $this->document->addStyleSheet(TZ_Portfolio_PlusUri::base() . '/vendor/intro/introjs-rtl.min.css', array('version' => 'v=2.9.3'));
}

$this -> document -> addScriptDeclaration('
(function($,window){
    "use strict";
    
    $(document).ready(function(){
        var styleSteps  = [
                {
                    /* Step 1: Upload */
                    element: $("[data-target=\\"#tpp-template__upload\\"]")[0],
                    intro: "<div class=\\"head\\">'.$this -> escape(JText::_('JTOOLBAR_UPLOAD')).'</div>'
                        .$this -> escape(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE_UPLOAD_MANUAL_DESC', JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE'))).'",
                    position: "right"
                },
                {
                    /* Step 2: Install online */
                    element: $(".action-links .install-now")[0],
                    intro: "<div class=\\"head\\">'.$this -> escape(JText::_('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE_INSTALL_UPDATE_ONLINE')).'</div>'
                        .$this -> escape(JText::sprintf('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE_INSTALL_UPDATE_ONLINE_DESC', JText::_('COM_TZ_PORTFOLIO_PLUS_TEMPLATE'))).'",
                    position: "top"
                }];
                
        if($(".action-links .js-tpp-live-demo").length){
            styleSteps[2]   = {
                /* Step 3: Demo link */
                element: $(".action-links .js-tpp-live-demo")[0],
                intro: "<div class=\\"head\\">'.$this -> escape(JText::_('COM_TZ_PORTFOLIO_PLUS_LIVE_DEMO')).'</div>'
                    .$this -> escape(JText::_('COM_TZ_PORTFOLIO_PLUS_INTRO_GUIDE_LIVE_DEMO_DESC')).'",
                position: "top"
            }
        }
                
        
        tppIntroGuide("'.$this -> getName().'",styleSteps , '.(TZ_Portfolio_PlusHelper::introGuideSkipped($this -> getName())?1:0).', "'.JSession::getFormToken().'");
        
    });
     
     
})(jQuery,window);
');
?>

<?php echo JHtml::_('tzbootstrap.addrow');?>
<?php if(!empty($this -> sidebar)){?>
    <div id="j-sidebar-container" class="span2 col-md-2">
        <?php echo $this -> sidebar; ?>
    </div>
<?php } ?>

    <?php echo JHtml::_('tzbootstrap.startcontainer', '10', !empty($this -> sidebar));?>
    <form name="adminForm" method="post" id="adminForm" class="tpp-extension__upload"
          enctype="multipart/form-data"
          action="index.php?option=com_tz_portfolio_plus&view=template&layout=upload">

        <?php echo $this -> loadTemplate('list'); ?>

        <input type="hidden" value="" name="task">
        <?php echo JHTML::_('form.token');?>
    </form>

    <?php echo JHtml::_('tzbootstrap.endcontainer');?>
<?php echo JHtml::_('tzbootstrap.endrow');?>