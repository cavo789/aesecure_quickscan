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
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Content component.
 */
class TZ_Portfolio_PlusViewDate extends JViewLegacy
{
    function display()
    {
        $app = JFactory::getApplication();

        $doc		= JFactory::getDocument();
        $params 	= $app->getParams();
        $feedEmail	= (@$app->getCfg('feed_email')) ? $app->getCfg('feed_email') : 'author';
        $siteEmail	= $app->getCfg('mailfrom');
        // Get some data from the model
        $app->input->set('limit', $app->get('feed_limit'));
        $rows		= $this->get('Items');

        $uri    = JUri::getInstance();
        $doc->setLink( $uri -> getPath());

        JPluginHelper::importPlugin('tz_portfolio_plus_mediatype');

        foreach ($rows as $row)
        {

            // Compute the article slug
            $row->slug 			= $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
            $row -> description	= ($params->get('feed_summary', 0) ? $row->introtext.$row->fulltext : $row->introtext);

            $results    = $app -> triggerEvent('onContentDisplayMediaType',array('com_tz_portfolio_plus.date',
                &$row, &$params, 0));

            $media	= implode("\n",$results);

            // strip html from feed item title
            $title = $this->escape($row->title);
            $title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');

            $link 	= $row -> link;

            // strip html from feed item description text
            // TODO: Only pull fulltext if necessary (actually, just get the necessary fields).
            $description	= $row -> description;
            $author			= $row->created_by_alias ? $row->created_by_alias : $row->author;
            @$date			= ($row->created ? date('r', strtotime($row->created)) : '');

            // load individual item creator class
            $feedItem = new JFeedItem();

            $feedItem->title		= $title;
            $feedItem->link			= $link;

            $feedItem->description	= $media.$description;
            $feedItem->date			= $date;
            $feedItem->category		= $row->category_title;

            $feedItem->author		= $author;
            if ($feedEmail == 'site') {
                $feedItem->authorEmail = $siteEmail;
            }
            else {
                $feedItem->authorEmail = $row->author_email;
            }

            // loads item info into rss array
            $doc->addItem($feedItem);
        }
    }
}
