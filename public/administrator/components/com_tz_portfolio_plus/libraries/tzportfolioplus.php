<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2013 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

function tzportfolioplusimport($path)
{
    JLoader::import('com_tz_portfolio_plus.libraries.'.$path,JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components');
}

function jsPlusAddSlashes($s)
{
    $o="";
    $l=strlen($s);
    for($i=0;$i<$l;$i++)
    {
        $c=$s[$i];
        switch($c)
        {
            case '<': $o.='\\x3C'; break;
            case '>': $o.='\\x3E'; break;
            case '\'': $o.='\\\''; break;
            case '\\': $o.='\\\\'; break;
            case '"':  $o.='\\"'; break;
            case "\n": $o.='\\n'; break;
            case "\r": $o.='\\r'; break;
            default:
                $o.=$c;
        }
    }
    return $o;
}
