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
FormHelper::loadFieldClass('predefinedlist');

class JFormFieldTZStatus extends JFormFieldPredefinedList {

    public $type        = 'TZStatus';
    protected $predefinedOptions = array(
        '-3' =>	'COM_TZ_PORTFOLIO_PLUS_DRAFTS',
        '3' =>	'COM_TZ_PORTFOLIO_PLUS_PENDING',
        '4' =>	'COM_TZ_PORTFOLIO_PLUS_UNDER_REVIEW',
        '-2' =>	'JTRASHED',
        '0'  => 'JUNPUBLISHED',
        '1'  => 'JPUBLISHED',
        '2'  => 'JARCHIVED',
        '*'  => 'JALL',
    );


}