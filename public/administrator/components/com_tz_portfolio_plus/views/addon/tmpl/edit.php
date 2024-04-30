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
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
    JHtml::_('behavior.tabstate');
    JHtml::_('formbehavior.chosen', 'select');
}
else{
    JHtml::_('formbehavior.chosen', 'select[multiple]');
}
$this->fieldsets = $this->form->getFieldsets('params');

?>

<form action="<?php echo JRoute::_('index.php?option=com_tz_portfolio_plus&view=addon&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate tpArticle">
    <div class="form-horizontal">

        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON', true)); ?>

            <?php echo JHtml::_('tzbootstrap.addrow');?>
            <div class="span9 col-md-9">
                <?php if ($this->item->xml){ ?>
                    <h3>
                    <?php
                    if ($this->item->xml)
                    {
                        echo ($text = (string) $this->item->xml->name) ? JText::_($text) : $this->item->name;
                    }
                    else
                    {
                        echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON_XML_ERR');
                    }
                    ?>
                    </h3>
                    <div class="info-labels">
							<span class="badge badge-secondary hasTooltip" title="<?php echo JHtml::tooltipText('COM_TZ_PORTFOLIO_PLUS_ADDON_FIELD_FOLDER_LABEL', 'COM_TZ_PORTFOLIO_PLUS_ADDON_FIELD_FOLDER_DESC'); ?>">
								<?php echo $this -> item -> folder; ?>
							</span> /
                        <span class="badge badge-secondary hasTooltip" title="<?php echo JHtml::tooltipText('COM_TZ_PORTFOLIO_PLUS_ADDON_FIELD_ELEMENT_LABEL', 'COM_TZ_PORTFOLIO_PLUS_ADDON_FIELD_ELEMENT_DESC'); ?>">
								<?php echo $this -> item -> element; ?>
							</span>
                    </div>
                    <?php if ($this->item->xml->description){ ?>

                        <div>
                            <?php
                            $short_description = JText::_($this->item->xml->description);
                            $this->fieldset = 'description';
                            $long_description = JLayoutHelper::render('joomla.edit.fieldset', $this);
                            if(!$long_description) {
                                $truncated = JHtmlString::truncate($short_description, 550, true, false);
                                if(strlen($truncated) > 500) {
                                    $long_description = $short_description;
                                    $short_description = JHtmlString::truncate($truncated, 250);
                                    if($short_description == $long_description) {
                                        $long_description = '';
                                    }
                                }
                            }
                            ?>
                            <p><?php echo $short_description; ?></p>
                            <?php if ($long_description){ ?>
                                <p class="readmore">
                                    <a href="#" onclick="jQuery('.nav-tabs a[href=#description]').tab('show');">
                                        <?php echo JText::_('JGLOBAL_SHOW_FULL_DESCRIPTION'); ?>
                                    </a>
                                </p>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php }else{ ?>
                    <div class="alert alert-error"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_ADDON_XML_ERR'); ?></div>
                <?php } ?>

                <?php
                $this->fieldset = 'basic';
                $html = JLayoutHelper::render('joomla.edit.fieldset', $this);
                echo $html ? '<hr />' . $html : '';
                ?>
            </div>
            <div class="span3 col-md-3">
                <div class="card card-light">
                    <div class="card-body">
                        <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
                        <div class="form-vertical form-no-margin">
                            <?php echo $this -> form -> renderField('folder');?>
                            <?php echo $this -> form -> renderField('element');?>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo JHtml::_('tzbootstrap.endrow');?>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php if (isset($long_description) && $long_description != '') : ?>
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'description', JText::_('JGLOBAL_FIELDSET_DESCRIPTION', true)); ?>
            <?php echo $long_description; ?>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php endif; ?>

        <?php
        $this->fieldsets = array();
        $this->ignore_fieldsets = array('basic', 'description', 'permissions');
        echo JLayoutHelper::render('joomla.edit.params', $this);
        ?>

        <?php if ($this->canDo->get('core.admin')){
            $rules  = $this -> form -> getInput('rules');
            $rules  = trim($rules);
            if($rules && !empty($rules)){
        ?>
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('JCONFIG_PERMISSIONS_LABEL')); ?>
            <?php echo $this->form->getInput('rules'); ?>
            <?php echo $this->form->getInput('title'); ?>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php }
        } ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <?php if($this -> return_link){?>
    <input type="hidden" name="return" value="<?php echo $this -> return_link;?>" />
    <?php }?>
    <?php echo JHtml::_('form.token'); ?>
</form>
