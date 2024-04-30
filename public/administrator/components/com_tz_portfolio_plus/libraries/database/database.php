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

namespace TZ_Portfolio_Plus\Database;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class TZ_Portfolio_PlusDatabase{

    protected static $cache    = array();

    /* Get database class from application
    *  @var $fullGroup: Disable full group by of Joomla 4.
    */
    public static function getDbo($fullGroup = false){

        $storeId    = md5(__METHOD__.':'.$fullGroup);
        $query      = false;

        if(isset(self::$cache[$storeId])){
            $query = self::$cache[$storeId];
        }

        $db    = Factory::getDbo();
        if(COM_TZ_PORTFOLIO_PLUS_JVERSION_4_COMPARE && !$fullGroup && !$query){
            $db->setQuery("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            self::$cache[$storeId]  = $db->execute();
        }

        return $db;
    }

    public static function splitQueries($query)
    {
        $buffer    = array();
        $queries   = array();
        $in_string = false;

        // Trim any whitespace.
        $query = trim($query);

        // Remove comment lines.
        $query = preg_replace("/\n\#[^\n]*/", '', "\n" . $query);

        // Remove PostgreSQL comment lines.
        $query = preg_replace("/\n\--[^\n]*/", '', "\n" . $query);

        // Find function.
        $funct = explode('CREATE OR REPLACE FUNCTION', $query);

        // Save sql before function and parse it.
        $query = $funct[0];

        // Parse the schema file to break up queries.
        for ($i = 0; $i < strlen($query) - 1; $i++)
        {
            if (!$in_string && $query[$i] === ';')
            {
                $queries[] = substr($query, 0, $i);
                $query     = substr($query, $i + 1);
                $i         = 0;
            }

            if ($in_string && $query[$i] == $in_string && $buffer[1] !== '\\')
            {
                $in_string = false;
            }
            elseif (!$in_string && ($query[$i] === '"' || $query[$i] === "'") && (!isset($buffer[0]) || $buffer[0] !== '\\'))
            {
                $in_string = $query[$i];
            }

            if (isset ($buffer[1]))
            {
                $buffer[0] = $buffer[1];
            }

            $buffer[1] = $query[$i];
        }

        // If the is anything left over, add it to the queries.
        if (!empty($query))
        {
            $queries[] = $query;
        }

        // Add function part as is.
        for ($f = 1, $fMax = count($funct); $f < $fMax; $f++)
        {
            $queries[] = 'CREATE OR REPLACE FUNCTION ' . $funct[$f];
        }

        return $queries;
    }
}