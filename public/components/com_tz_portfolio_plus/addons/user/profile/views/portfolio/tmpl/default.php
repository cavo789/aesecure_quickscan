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

$params     = $this -> params;
if($params -> get('show_cat_about_author', 1) && ($author = $this -> authorAbout)){
    $this -> document -> addStyleSheet(TZ_Portfolio_PlusUri::base(true).'/addons/user/profile/css/style.css',
        array('version' => 'auto', 'relative' => true));

    $avaName    =   '';
    $arrName    =   explode(' ',$author -> name);

    for ($i=0; $i<count($arrName); $i++){
        if ($word = trim($arrName[$i])) {
            $avaName.=$word[0];
        }
    }
    ?>
    <div class="tpp-author-about">
        <div class="media">
            <div class="avatar pull-left<?php echo (!$author -> avatar)? ' tpp-avatar__bg-'.rand(1,5):'';?>">
                <?php if($author -> avatar){?>
                    <img src="<?php echo JUri::root().$author -> avatar;?>" alt="<?php echo $author -> name;?>"/>
                <?php }else{?>
                    <span class="symbol"><?php echo $avaName; ?></span>
                <?php }?>
            </div>
            <div class="media-body">
                <h2 class="h3 title"><?php echo $author -> name; ?></h2>

                <?php if(($params -> get('show_cat_email_user', 1) && $author -> email) ||
                    ($params -> get('show_cat_gender_user', 1) && $author -> gender) ||
                    ($params -> get('show_cat_url_user',1) AND !empty($author -> url))){?>
                <ul class="list-inline muted text-muted small">
                    <?php if($params -> get('show_cat_email_user', 1) && $author -> email){?>
                    <li class="list-inline-item email"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_EMAIL');?>
                        <?php
                        echo $author -> email;?></li>
                    <?php } ?>
                    <?php if($params -> get('show_cat_gender_user', 1) && $author -> gender){?>
                    <li class="list-inline-item"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_GENDER');?><?php
                        echo ($author -> gender == 'm')?JText::_('COM_TZ_PORTFOLIO_PLUS_MALE'):JText::_('COM_TZ_PORTFOLIO_PLUS_FEMALE');
                        ?></li>
                    <?php } ?>
                    <?php if($params -> get('show_cat_url_user',1) AND !empty($author -> url)){?>
                    <li class="list-inline-item">
                        <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_WEBSITE');?>
                        <a href="<?php echo $author -> url;?>" target="_blank">
                            <?php echo $author -> url;?>
                        </a>
                    </li>
                    <?php } ?>
                </ul>
                <?php } ?>

                <?php if($params -> get('show_cat_social_link_user', 1) && (!empty($author -> twitter)
                        || !empty($author -> facebook) || !empty($author -> instagram)
                        || !empty($author -> googleplus))){?>
                    <ul class="list-inline social small">
                        <?php if($author -> twitter){?>
                            <li class="list-inline-item">
                                <a href="<?php echo $author -> twitter; ?>" target="_blank"><?php
                                    echo JText::_('PLG_USER_PROFILE_TWITTER');?></a>
                            </li>
                        <?php } ?>
                        <?php if($author -> facebook){?>
                            <li class="list-inline-item">
                                <a href="<?php echo $author -> facebook; ?>" target="_blank"><?php
                                    echo JText::_('PLG_USER_PROFILE_FACEBOOK');?></a>
                            </li>
                        <?php } ?>
                        <?php if($author -> instagram){?>
                            <li class="list-inline-item">
                                <a href="<?php echo $author -> instagram; ?>" target="_blank"><?php
                                    echo JText::_('PLG_USER_PROFILE_INSTAGRAM');?></a>
                            </li>
                        <?php } ?>
                        <?php if($author -> googleplus){?>
                            <li class="list-inline-item">
                                <a href="<?php echo $author -> googleplus; ?>" target="_blank"><?php
                                    echo JText::_('PLG_USER_PROFILE_GOOGLEPLUS');?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
        <?php if($params -> get('show_cat_description_user', 1)  AND !empty($author -> description)){?>
            <div class="description">
                <?php echo $author -> description; ?>
            </div>
        <?php } ?>
    </div>
    <?php
}