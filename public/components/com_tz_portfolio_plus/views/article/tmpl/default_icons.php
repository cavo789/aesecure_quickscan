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
defined('_JEXEC') or die;

$params = $this -> item -> params;
$canEdit	= $this->item->params->get('access-edit');

$bootstrap4 = ($params -> get('enable_bootstrap',1) && $params -> get('bootstrapversion', 4) == 4);
?>

<?php if (!$this->print) : ?>
    <?php if ($canEdit ||  $params->get('show_print_icon', 1) || $params->get('show_email_icon', 1)) : ?>
        <div class="tpp-item-icon">
            <div class="btn-group pull-right float-right">
                <a class="btn btn-default btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" id="dropdownMenuButton-<?php
                echo $this -> item -> id; ?>" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="tps tp-cog"></i><?php if($params -> get('bootstrapversion', 4) != 4){ ?> <span class="caret"></span><?php }?>
                </a>
                <?php // Note the actions class is deprecated. Use dropdown-menu instead. ?>
                <ul class="dropdown-menu actions" aria-labelledby="dropdownMenuButton-<?php
                echo $this -> item -> id; ?>">
                    <?php if ($params->get('show_print_icon', 1)) : ?>
                        <li class="print-icon"> <?php echo JHtml::_('icon.print_popup',  $this->item, $params); ?> </li>
                    <?php endif; ?>
                    <?php if ($params->get('show_email_icon', 1)) : ?>
                        <li class="email-icon"> <?php echo JHtml::_('icon.email',  $this->item, $params); ?> </li>
                    <?php endif; ?>
                    <?php if ($canEdit) : ?>
                        <li class="edit-icon"> <?php echo JHtml::_('icon.edit', $this->item, $params); ?> </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
<?php else : ?>
    <div class="pull-right float-right">
        <?php echo JHtml::_('icon.print_screen',  $this->item, $params); ?>
    </div>
<?php endif; ?>