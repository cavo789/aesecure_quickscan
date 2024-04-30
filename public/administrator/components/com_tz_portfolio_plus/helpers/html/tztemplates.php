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

defined('_JEXEC') or die;

class JHtmlTZTemplates
{
    public static function thumb($template, $clientId = 0)
    {
        $client = JApplicationHelper::getClientInfo($clientId);
        $basePath = $client->path . '/components/com_tz_portfolio_plus/templates/' . $template;
        $baseUrl = ($clientId == 0) ? JUri::root(true) : JUri::root(true) . '/administrator';
        $preview = $basePath . '/template_preview.png';
        $html = '';

        if (file_exists($preview))
        {
            JHtml::_('bootstrap.tooltip');

            $preview = $baseUrl . '/components/com_tz_portfolio_plus/templates/' . $template . '/template_preview.png';

            $html = JHtml::_('image', 'components/com_tz_portfolio_plus/templates/' . $template . '/template_preview.png'
                , JText::_('COM_TEMPLATES_PREVIEW'));
            $html = '<a href="#' . $template . '-Modal" role="button" class="thumbnail float-left hasTooltip" data-bs-toggle="modal" data-toggle="modal" title="' .
                JHtml::_('tooltipText', 'COM_TEMPLATES_CLICK_TO_ENLARGE') . '">' . $html . '</a>';


            $footer = '<button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">'
                . JText::_('JTOOLBAR_CLOSE') . '</button>';

            $html .= JHtml::_(
                'bootstrap.renderModal',
                $template . '-Modal',
                array(
                    'title'  => JText::_('COM_TEMPLATES_BUTTON_PREVIEW'),
                    'height' => '500px',
                    'width'  => '800px',
                    'footer' => $footer,
                ),
                $body = '<div><img src="' . $preview . '" style="max-width:100%" alt="' . $template . '"></div>'
            );
        }

        return $html;
    }
}