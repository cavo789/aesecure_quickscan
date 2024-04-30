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

/**
 * Content Component HTML Helper.
 */
class JHtmlIcon
{
    public static function create($category, $params, $options = array())
    {
        $uri = JURI::getInstance();

        $url = 'index.php?option=com_tz_portfolio_plus&task=article.add&return='.base64_encode($uri).'&a_id=0&catid=' . $category->id;

        if ($params->get('show_cat_icons', 1)) {

            $icon	= 'tps tp-plus-circle';
            if(count($options) && isset($options['icon'])){
                $icon	= $options['icon'];
                unset($options['icon']);
            }
            $text = '<i class="'.$icon.'"></i> ' . JText::_('JNEW') . '&#160;';
        } else {
            $text = JText::_('JNEW').'&#160;';
        }

        $button = JHtml::_('link', JRoute::_($url), $text, 'class="btn btn-primary"');

        $output = '<span class="hasTip dropdown-item" title="'.JText::_('COM_TZ_PORTFOLIO_PLUS_CREATE_ARTICLE').'">'.$button.'</span>';
        return $output;
    }

    public static function email($article, $params, $attribs = array())
    {
        $mailto_file    = JPATH_SITE . '/components/com_mailto/helpers/mailto.php';
        if(!file_exists($mailto_file)) {
            return '';
        }
        require_once $mailto_file;

        if(!function_exists('MailToHelper')){
            return '';
        }

        $uri	= JURI::getInstance();
        $base	= $uri->toString(array('scheme', 'host', 'port'));
        $template = JFactory::getApplication()->getTemplate();

        $link   = TZ_Portfolio_PlusHelperRoute::getArticleRoute($article -> slug,$article -> catid);

        $link	= $base . JRoute::_($link,false);

        $url	= 'index.php?option=com_mailto&amp;tmpl=component&amp;template='.$template.'&amp;link='.MailToHelper::addLink($link);

        $status = 'width=400,height=350,menubar=yes,resizable=yes';

        if ($params->get('show_cat_icons', 1)) {

            $icon	= 'tpr tp-envelope';
            if(count($attribs) && isset($attribs['icon'])){
                $icon	= $attribs['icon'];
                unset($attribs['icon']);
            }
            $text = '<i class="'. $icon .'"></i> ' . JText::_('JGLOBAL_EMAIL');
        } else {
            $text = JText::_('JGLOBAL_EMAIL');
        }

        $attribs['title']	= JText::_('JGLOBAL_EMAIL');

        $class  = 'dropdown-item';
        if(isset($attribs['class'])){
            $class  .= ' '.$attribs['class'];
        }
        $attribs['class']   = $class;
        $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

        $output = JHtml::_('link', JRoute::_($url), $text, $attribs);
        return $output;
    }

    /**
     * Display an edit icon for the article.
     *
     * This icon will not display in a popup window, nor if the article is trashed.
     * Edit access checks must be performed in the calling code.
     *
     * @param	object	$article	The article in question.
     * @param	object	$params		The article parameters
     * @param	array	$attribs	Not used??
     *
     * @return	string	The HTML for the article edit icon.
     * @since	1.6
     */
    public static function edit($article, $params, $attribs = array())
    {
        $user	= JFactory::getUser();
        $userId	= $user->get('id');
        $uri	= JURI::getInstance();

        // Ignore if in a popup window.
        if ($params && $params->get('popup')) {
            return;
        }

        // Ignore if the state is negative (trashed).
        if ($article->state < 0) {
            return;
        }

        if(!COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE) {
            JHtml::_('behavior.tooltip');
        }

        // Show checked_out icon if the article is checked out by a different user
        if (property_exists($article, 'checked_out') && property_exists($article, 'checked_out_time') && $article->checked_out > 0 && $article->checked_out != $user->get('id')) {
            $checkoutUser = JFactory::getUser($article->checked_out);
            $button = JHtml::_('image', 'system/checked_out.png', null, null, true);
            $date = JHtml::_('date', $article->checked_out_time);
            $tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '.JText::sprintf('COM_TZ_PORTFOLIO_PLUS_CHECKED_OUT_BY', $checkoutUser->name).' <br /> '.$date;
            return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
        }

        $tmpl   = JFactory::getApplication() -> input -> getCmd('tmpl',null);
        if($tmpl){
            $tmpl   = '&tmpl=component';
        }

        $url	= 'index.php?option=com_tz_portfolio_plus&amp;task=article.edit&amp;a_id='.$article->id.'&amp;return='.base64_encode($uri)
            .$tmpl;

        if ($article->state == 0) {
            $overlib = JText::_('JUNPUBLISHED');
        }
        else {
            $overlib = JText::_('JPUBLISHED');
        }

        $date = JHtml::_('date', $article->created);
        $author = $article->created_by_alias ? $article->created_by_alias : $article->author;

        $overlib .= '&lt;br /&gt;';
        $overlib .= $date;
        $overlib .= '&lt;br /&gt;';
        $overlib .= JText::sprintf('COM_TZ_PORTFOLIO_PLUS_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

        $icon	= $article->state ? ' tps tp-edit' : 'tpr tp-eye-slash';
        if($article->state && count($attribs) && isset($attribs['icon'])){
            $icon	= $attribs['icon'];
            unset($attribs['icon']);
        }
        if(count($attribs) && isset($attribs['icon_close'])){
            $icon	= $attribs['icon_close'];
            unset($attribs['icon_close']);
        }

        $text = '<i class="hasTip '.$icon.'" title="'.JText::_('COM_TZ_PORTFOLIO_PLUS_EDIT_ITEM').' :: '.$overlib.'"></i> '.JText::_('JGLOBAL_EDIT');

        $class  = 'dropdown-item';
        if(isset($attribs['class'])){
            $class  .= ' '.$attribs['class'];
        }
        $attribs['class']   = $class;

        $output = JHtml::_('link', JRoute::_($url), $text, $attribs);

        return $output;
    }


    public static function print_popup($article, $params, $attribs = array())
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $request = $input->request;

        $url    = TZ_Portfolio_PlusHelperRoute::getArticleRoute($article -> slug,$article -> catid);

        $url .= '&amp;tmpl=component&amp;print=1&amp;layout=default&amp;page='.@ $request->limitstart;

        $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

        // checks template image directory for image, if non found default are loaded
        if ($params->get('show_cat_icons', 1)) {
            $icon	= 'tps tp-print';
            if(count($attribs) && isset($attribs['icon'])){
                $icon	= $attribs['icon'];
                unset($attribs['icon']);
            }
            $text = '<i class="'.$icon.'"></i> '.JText::_('JGLOBAL_PRINT');
        } else {
            $text = JText::_('JGLOBAL_PRINT');
        }

        $attribs['title']	= JText::_('JGLOBAL_PRINT');
        $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
        $attribs['rel']		= 'nofollow';

        $class  = 'dropdown-item';
        if(isset($attribs['class'])){
            $class  .= ' '.$attribs['class'];
        }
        $attribs['class']   = $class;

        return JHtml::_('link', JRoute::_($url), $text, $attribs);
    }

    public static function print_screen($article, $params, $attribs = array())
    {
        // checks template image directory for image, if non found default are loaded
        if ($params->get('show_cat_icons', 1)) {
            $icon	= 'tps tp-print';
            if(count($attribs) && isset($attribs['icon'])){
                $icon	= $attribs['icon'];
                unset($attribs['icon']);
            }
            $text = $text = '<i class="'.$icon.'"></i> '.JText::_('JGLOBAL_PRINT');
        } else {
            $text = JText::_('JGLOBAL_PRINT');
        }

        return '<a href="#" onclick="window.print();return false;">' . $text . '</a>';
    }

    public static function getIcon($code) {
        jimport('joomla.filesytem.file');
        $json   =   file_get_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'tz_portfolio_plus'.DIRECTORY_SEPARATOR.'icomoon'.DIRECTORY_SEPARATOR.'selection.json');
        $data   =   json_decode($json);
        $icons  =   $data->icons;
        foreach ($icons as $icon) {
            if ($icon->properties->code == $code) {
                return $icon->properties->name;
            }
        }
        return '';
    }
}