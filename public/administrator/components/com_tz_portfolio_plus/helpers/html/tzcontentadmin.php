<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;
use TZ_Portfolio_Plus\Database\TZ_Portfolio_PlusDatabase;

JLoader::register('TZ_Portfolio_PlusHelper', JPATH_ADMINISTRATOR . '/components/com_tz_portfolio_plus/helpers/tz_portfolio_plus.php');
JLoader::import('com_tz_portfolio_plus.helpers.association',JPATH_ADMINISTRATOR . '/components');

/**
 * Content HTML helper
 *
 * @since  3.0
 */
abstract class JHtmlTZContentAdmin
{
    /**
     * Render the list of associated items
     *
     * @param   int  $articleid  The article item id
     *
     * @return  string  The language HTML
     */
    public static function association($articleid)
    {
        // Defaults
        $html = '';

        // Get the associations
        if ($associations = TZ_Portfolio_PlusBackEndHelperAssociation::getArticleAssociations($articleid))
        {
            foreach ($associations as $tag => $associated)
            {
                $associations[$tag] = (int) $associated->id;
            }

            // Get the associated menu items
            $db = TZ_Portfolio_PlusDatabase::getDbo();
            $query = $db->getQuery(true)
                ->select('c.*')
                ->select('l.sef as lang_sef')
                ->from('#__tz_portfolio_plus_content as c')
                ->select('cat.title as category_title')
                ->join('LEFT', '#__tz_portfolio_plus_content_category_map as m ON m.contentid=c.id')
                ->join('LEFT', '#__tz_portfolio_plus_categories as cat ON cat.id=m.catid')
                ->where('c.id IN (' . implode(',', array_values($associations)) . ')')
                ->join('LEFT', '#__languages as l ON c.language=l.lang_code')
                ->select('l.image')
                ->select('l.title as language_title');
            $db->setQuery($query);

            try
            {
                $items = $db->loadObjectList('id');
            }
            catch (RuntimeException $e)
            {
                throw new Exception($e->getMessage(), 500);
            }

            if ($items)
            {
                foreach ($items as &$item)
                {
                    $text = strtoupper($item->lang_sef);
                    $url = JRoute::_('index.php?option=com_tz_portfolio_plus&task=article.edit&id=' . (int) $item->id);
                    $tooltipParts = array(
                        JHtml::_('image', 'mod_languages/' . $item->image . '.gif',
                            $item->language_title,
                            array('title' => $item->language_title),
                            true
                        ),
                        $item->title,
                        '(' . $item->category_title . ')'
                    );
                    $item->link = JHtml::_('tooltip', implode(' ', $tooltipParts), null, null, $text, $url, null, 'hasTooltip label label-association label-' . $item->lang_sef);
                }
            }

            $html = JLayoutHelper::render('joomla.content.associations', $items);
        }

        return $html;
    }

    /**
     * Show the feature/unfeature links
     *
     * @param   int      $value      The state value
     * @param   int      $i          Row number
     * @param   boolean  $canChange  Is user allowed to change?
     *
     * @return  string       HTML code
     */
    public static function featured($value, $i, $canChange = true)
    {
        JHtml::_('bootstrap.tooltip');

        // Array of image, task, title, action
        $states	= array(
            0	=> array('unfeatured',	'articles.featured',	'COM_TZ_PORTFOLIO_PLUS_UNFEATURED_ARTICLE',	'JGLOBAL_TOGGLE_FEATURED'),
            1	=> array('featured',	'articles.unfeatured',	'COM_TZ_PORTFOLIO_PLUS_FEATURED_ARTICLE',		'JGLOBAL_TOGGLE_FEATURED'),
        );
        $state	= ArrayHelper::getValue($states, (int) $value, $states[1]);
        $icon	= $state[0];

        $class  = 'btn btn-micro';
        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE){
            $class = 'tbody-icon';
        }

        if ($canChange)
        {
            $html	= '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1]
                . '\')" class="'.$class.' hasTooltip' . ($value == 1 ? ' active' : '') . '" title="'
                . JHtml::tooltipText($state[3]) . '"><span class="icon-'
                . $icon . '"></span></a>';
        }
        else
        {
            $html	= '<a class="'.$class.' hasTooltip disabled' . ($value == 1 ? ' active' : '')
                . '" title="' . JHtml::tooltipText($state[2]) . '"><span class="icon-'
                . $icon . '"></span></a>';
        }

        return $html;
    }
}
