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

// No direct access.
defined('_JEXEC') or die;

tzportfolioplusimport('controller.admin');

class TZ_Portfolio_PlusControllerAddon_Datas extends TZ_Portfolio_Plus_AddOnControllerAdmin
{
	protected $text_prefix	= 'COM_TZ_PORTFOLIO_PLUS_ADDON_DATAS';

	public function getModel($name = 'Addon_Data', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}
