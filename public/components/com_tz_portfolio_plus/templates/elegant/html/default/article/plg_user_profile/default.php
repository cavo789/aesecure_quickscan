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
if($params -> get('show_about_author', 1) && ($author = $this -> authorAbout)){
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
    <div class="tpp-author-about card">
        <div class="card-body d-flex align-items-center">
            <div class="avatar mr-4<?php echo (!$author -> avatar)? ' tpp-avatar__bg-'.rand(1,5):'';?>">
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
                <ul class="list-inline muted text-muted ">
                    <?php if($params -> get('show_cat_gender_user', 1) && $author -> gender){?>
                        <li class="list-inline-item" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_GENDER');?>">
                            <?php if($author -> gender == 'm'){ ?>
                                <i class="tp tp-mars" aria-hidden="true"></i>
                                <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_MALE'); ?>
                            <?php }else{ ?>
                                <i class="tp tp-venus" aria-hidden="true"></i>
                                <?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_FEMALE'); ?>
                            <?php } ?>
                        </li>
                    <?php } ?>
                    <?php if($params -> get('show_cat_email_user', 1) && $author -> email){?>
                    <li class="list-inline-item email" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_EMAIL');?>">
                        <i class="tp tp-envelope-o" aria-hidden="true"></i>
                        <?php
                        echo $author -> email;?></li>
                    <?php } ?>
                    <?php if($params -> get('show_cat_url_user',1) AND !empty($author -> url)){?>
                    <li class="list-inline-item" title="<?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_WEBSITE');?>">
                        <i class="tp tp-globe" aria-hidden="true"></i>
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
                    <ul class="list-inline social ">
                        <?php if($author -> twitter){?>
                            <li class="list-inline-item">
                                <a href="<?php echo $author -> twitter; ?>" title="<?php
                                echo JText::_('PLG_USER_PROFILE_TWITTER');
                                ?>" target="_blank"><i class="tp tp-twitter" aria-hidden="true"></i></a>
                            </li>
                        <?php } ?>
                        <?php if($author -> facebook){?>
                            <li class="list-inline-item">
                                <a href="<?php echo $author -> facebook; ?>" title="<?php
                                echo JText::_('PLG_USER_PROFILE_FACEBOOK');
                                ?>" target="_blank"><i class="tp tp-facebook-official" aria-hidden="true"></i></a>
                            </li>
                        <?php } ?>
                        <?php if($author -> instagram){?>
                            <li class="list-inline-item">
                                <a href="<?php echo $author -> instagram; ?>" title="<?php
                                echo JText::_('PLG_USER_PROFILE_INSTAGRAM');
                                ?>" target="_blank"><i class="tp tp-instagram" aria-hidden="true"></i></a>
                            </li>
                        <?php } ?>
                        <?php if($author -> googleplus){?>
                            <li class="list-inline-item">
                                <a href="<?php echo $author -> googleplus; ?>" title="<?php
                                echo JText::_('PLG_USER_PROFILE_GOOGLEPLUS');
                                ?>" target="_blank"><i class="tp tp-google-plus-square" aria-hidden="true"></i></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        </div>
        <?php if($params -> get('show_cat_description_user', 1)  AND !empty($author -> description)){?>
            <div class="description card-footer text-muted">
                <?php echo $author -> description; ?>
            </div>
        <?php } ?>
    </div>
    <?php
}