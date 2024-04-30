<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2018 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$modalId    = 'tpp-myarticle__approve';
$cids       = $this -> state -> get('article.id', array());

echo JHtml::_(
    'bootstrap.renderModal',
    $modalId,
    array(
        'title'      => JText::_('COM_TZ_PORTFOLIO_PLUS_DELETE_ARTICLE_PERMANENTLY'),
        'width'      => '100%',
        'height'     => '500px',
        'modalWidth' => '40',
        'closeButton' => true,
        'class'       => 'tpp-dialog-modal',
        'footer'      => '<a class="btn btn-default" data-dismiss="modal" href="javascript:void(0);">'
            . JText::_('JCANCEL') . '</a><a class="btn btn-primary" href="javascript:void(0);" data-submit-button>'
            . JText::_('JACTION_DELETE') . '</a>',
    ),
    JText::_('COM_TZ_PORTFOLIO_PLUS_DIALOG_CONFIRM_DELETE_ARTICLE_PERMANENTLY')
);
?>