-- # /*------------------------------------------------------------------------

-- # TZ Portfolio Plus Extension

-- # ------------------------------------------------------------------------

-- # author    DuongTVTemPlaza

-- # copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

-- # @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

-- # Websites: http://www.templaza.com

-- # Technical Support:  Forum - http://templaza.com/Forum

-- # -------------------------------------------------------------------------*/


--
-- Table structure for table `#__tz_portfolio_plus_addon_data`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_addon_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension_id` int(11) NOT NULL,
  `element` varchar(255) NOT NULL,
  `value` longtext NULL,
  `content_id` int(11) NULL,
  `published` tinyint(4) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `asset_id` INT UNSIGNED NOT NULL DEFAULT '0',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` INT(10) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_addon_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `addon_id` INT UNSIGNED NOT NULL DEFAULT '0',
  `data_id` int(11) NOT NULL DEFAULT '0',
  `meta_id` int(11) NOT NULL DEFAULT '0',
  `meta_key` varchar(255) NOT NULL DEFAULT '',
  `meta_value` longtext NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__tz_portfolio_plus_categories`
--
CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL DEFAULT '0',
  `images` text NULL,
  `template_id` int(10) unsigned NOT NULL DEFAULT '0',
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `params` text NULL,
  `metadesc` varchar(1024) NOT NULL COMMENT 'The meta description for the page.',
  `metakey` varchar(1024) NOT NULL COMMENT 'The meta keywords for the page.',
  `metadata` varchar(2048) NOT NULL COMMENT 'JSON encoded metadata properties.',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '',
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cat_idx` (`extension`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_path` (`path`),
  KEY `idx_left_right` (`lft`,`rgt`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `#__tz_portfolio_plus_categories`
--
INSERT IGNORE INTO `#__tz_portfolio_plus_categories` (`id`, `groupid`, `images`, `template_id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`, `version`) VALUES
(1, 0, '', 0, 0, 0, 0, 3, 0, '', 'system', 'ROOT', 'root', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 288, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1),
(2, 0, '', 0, 0, 1, 1, 2, 1, 'uncategorised', 'com_tz_portfolio_plus', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{"inheritFrom":"0","category_layout":"","image":"","show_cat_title":"1","cat_link_titles":"1","show_cat_intro":"1","show_cat_category":"0","cat_link_category":"1","show_cat_parent_category":"0","cat_link_parent_category":"1","show_cat_author":"0","cat_link_author":"1","show_cat_create_date":"0","show_cat_modify_date":"0","show_cat_publish_date":"0","show_cat_readmore":"1","show_cat_hits":"0","show_cat_tags":"0","show_cat_icons":"1","show_cat_print_icon":"0","show_cat_email_icon":"0","show_icons":"1","show_print_icon":"1","show_email_icon":"1","show_noauth":"0","link_category":"1","link_parent_category":"1","show_gender_user":"1","show_email_user":"1","show_url_user":"1","show_description_user":"1","show_related_article":"1","related_limit":"5","show_related_heading":"1","related_heading":"","show_related_title":"1","show_related_featured":"1","related_orderby":"rdate","mt_show_cat_image_hover":"","mt_cat_image_size":"","mt_image_size":"","mt_show_image_hover":"","mt_image_use_cloud":"","mt_image_related_show_image":"","mt_image_related_size":"","show_cat_vote":""}', '', '', '{"author":"","robots":""}', 233, '2015-12-12 14:42:28', 0, '2015-12-12 14:42:28', 0, '*', 1);

--
-- Table structure for table `#__tz_portfolio_plus_content`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL DEFAULT '0',
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `introtext` mediumtext NULL,
  `fulltext` mediumtext NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Store old state to restore state',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` text NULL,
  `urls` text NULL,
  `attribs` text NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `metakey` text NULL,
  `metadesc` text NULL,
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `metadata` text NULL,
  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.',
  `language` char(7) NOT NULL DEFAULT '' COMMENT 'The language code for the article.',
  `xreference` varchar(50) NOT NULL DEFAULT '' COMMENT 'A reference to enable linkages to external data sets.',
  `type` varchar(25) NOT NULL DEFAULT '',
  `media` text NULL,
  `template_id` int(11) NOT NULL DEFAULT '0',
  `priority` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`),
  KEY `idx_language` (`language`),
  KEY `idx_xreference` (`xreference`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_content_category_map`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_content_category_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contentid` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `main` tinyint(4) NOT NULL COMMENT 'Main Category',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_content_featured_map`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_content_featured_map` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `#__tz_portfolio_plus_content_rating`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_content_rating` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `lastip` varchar(50) NOT NULL DEFAULT '',
  `rating_sum` int(11) NOT NULL DEFAULT '0',
  `rating_count` int(11) NOT NULL DEFAULT '0',
  KEY `extravote_idx` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_content_rejected`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_content_rejected` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__tz_portfolio_plus_content table.',
  `created` datetime NOT NULL,
  `created_by` int(11) UNSIGNED NOT NULL COMMENT 'FK to the #__users table.',
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `#__tz_portfolio_plus_extensions`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(100) NOT NULL DEFAULT '',
  `element` varchar(100) NOT NULL DEFAULT '',
  `folder` varchar(100) NOT NULL DEFAULT '',
  `protected` tinyint(3) NOT NULL DEFAULT '0',
  `manifest_cache` text NULL,
  `params` text NULL,
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `access` int(10) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20;

-- --------------------------------------------------------

--
-- Dumping data for table `#__tz_portfolio_plus_extensions`
--
INSERT IGNORE INTO `#__tz_portfolio_plus_extensions` (`id`, `name`, `type`, `element`, `folder`, `protected`, `manifest_cache`, `params`, `checked_out`, `checked_out_time`, `published`, `access`, `ordering`) VALUES
(1, 'system', 'tz_portfolio_plus-template', 'system', '', 1, '{"name":"system","type":"tz_portfolio_plus-template","creationDate":"July 17th 2015","author":"DuongTVTemplaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"info@templaza.com","authorUrl":"","version":"1.0","description":"TZ_PORTFOLIO_PLUS_TPL_XML_DESCRIPTION","group":"","filename":"template"}', '', 0, '0000-00-00 00:00:00', 1, 1, 0),
(2, 'plg_content_vote', 'tz_portfolio_plus-plugin', 'vote', 'content', 1, '{"name":"plg_content_vote","type":"tz_portfolio_plus-plugin","creationDate":"Aug, 09th 2012","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 Open Source Matters. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com","version":"1.0.3","description":"PLG_CONTENT_VOTE_XML_DESCRIPTION","group":"","filename":"vote","special":0}', '{"show_cat_vote":"0","show_cat_counter":"1","cat_unrated":"1","show_counter":"1","unrated":"1"}', 0, '2016-01-07 10:03:01', 1, 1, 0),
(3, 'plg_mediatype_image', 'tz_portfolio_plus-plugin', 'image', 'mediatype', 1, '{"name":"plg_mediatype_image","type":"tz_portfolio_plus-plugin","creationDate":"September 17th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_MEDIATYPE_IMAGE_XML_DESCRIPTION","group":"","filename":"image","special":0}', '{"image_file_size":"10","image_file_type":"bmp,gif,jpg,jpeg,png,BMP,GIF,JPG,JPEG,PNG","image_mime_type":"image\\/jpeg,image\\/gif,image\\/png,image\\/bmp","image_size":["{\\"title\\":\\"XSmall\\",\\"width\\":\\"100\\",\\"image_name_prefix\\":\\"xs\\"}","{\\"title\\":\\"Small\\",\\"width\\":\\"200\\",\\"image_name_prefix\\":\\"s\\"}","{\\"title\\":\\"Medium\\",\\"width\\":\\"400\\",\\"image_name_prefix\\":\\"m\\"}","{\\"title\\":\\"Large\\",\\"width\\":\\"600\\",\\"image_name_prefix\\":\\"l\\"}","{\\"title\\":\\"XLarge\\",\\"width\\":\\"900\\",\\"image_name_prefix\\":\\"xl\\"}"],"mt_image_show_feed_image":"1","mt_image_feed_size":"o","mt_show_cat_image_hover":"1","mt_cat_image_size":"o","mt_image_size":"o","mt_show_image_hover":"1","mt_image_use_cloud":"0","mt_image_related_show_image":"1","mt_image_related_size":"o","mt_image_cloud_size":"o","mt_image_cloud_position":"inside","mt_image_cloud_softfocus":"0","mt_image_cloud_show_title":"1","mt_image_cloud_width":"","mt_image_cloud_height":"","mt_image_cloud_adjustX":"0","mt_image_cloud_adjustY":"0","mt_image_cloud_tint":"","mt_image_cloud_tint_opacity":"0.5","mt_image_cloud_len_opacity":"0.5","mt_image_cloud_smoothmove":"3","mt_image_cloud_title_opacity":"0.5"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(4, 'plg_extrafields_text', 'tz_portfolio_plus-plugin', 'text', 'extrafields', 1, '{"name":"plg_extrafields_text","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_TEXT_XML_DESCRIPTION","group":"","filename":"text","special":0}', '{"suggestion":"0"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(5, 'plg_extrafields_textarea', 'tz_portfolio_plus-plugin', 'textarea', 'extrafields', 1, '{"name":"plg_extrafields_textarea","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_TEXTAREA_XML_DESCRIPTION","group":"","filename":"textarea","special":0}', '{"cols":"50","rows":"5","use_editor_back_end":"0","use_editor_front_end":"0","groups_can_use_frontend_editor":"1"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(6, 'plg_extrafields_checkboxes', 'tz_portfolio_plus-plugin', 'checkboxes', 'extrafields', 1, '{"name":"plg_extrafields_checkboxes","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_CHECKBOXES_XML_DESCRIPTION","group":"","filename":"checkboxes","special":0}', '{"number_columns":"0"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(7, 'plg_extrafields_dropdownlist', 'tz_portfolio_plus-plugin', 'dropdownlist', 'extrafields', 1, '{"name":"plg_extrafields_dropdownlist","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_DROPDOWNLIST_XML_DESCRIPTION","group":"","filename":"dropdownlist","special":0}', '{"size":"5"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(8, 'plg_extrafields_multipleselect', 'tz_portfolio_plus-plugin', 'multipleselect', 'extrafields', 1, '{"name":"plg_extrafields_multipleselect","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_MULTIPLESELECT_XML_DESCRIPTION","group":"","filename":"multipleselect","special":0}', '{"size":"5"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(9, 'plg_extrafields_radio', 'tz_portfolio_plus-plugin', 'radio', 'extrafields', 1, '{"name":"plg_extrafields_radio","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_RADIO_XML_DESCRIPTION","group":"","filename":"radio","special":0}', '{"bootstrap_style":"1","number_columns":"0"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(10, 'plg_user_profile', 'tz_portfolio_plus-plugin', 'profile', 'user', 1, '{"name":"plg_user_profile","type":"tz_portfolio_plus-plugin","creationDate":"September 16th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_USER_PROFILE_XML_DESCRIPTION","group":"","filename":"profile","special":0}', '{}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(11, 'elegant', 'tz_portfolio_plus-template', 'elegant', '', 0, '{"name":"elegant","type":"tz_portfolio_plus-template","creationDate":"August 17th 2017","author":"Sonny","copyright":"Copyright (C) 2017 TemPlaza. All rights reserved.","authorEmail":"sonlv@templaza.com","authorUrl":"www.templaza.com","version":"1.0","description":"TZ_PORTFOLIO_PLUS_TPL_XML_DESCRIPTION","group":"","filename":"template"}', '{"use_single_layout_builder":"0","load_style":"1"}', 0, '0000-00-00 00:00:00', 1, 1, 0);


--
-- Table structure for table `#__tz_portfolio_plus_fieldgroups`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_fieldgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `name` varchar(255) NOT NULL DEFAULT '',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `field_ordering_type` tinyint(4) NOT NULL DEFAULT '0',
  `description` text NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `created_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `access` INT(10) UNSIGNED NOT NULL DEFAULT '1',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_fields`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `value` text NULL,
  `default_value` text NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `advanced_search` tinyint(4) NOT NULL DEFAULT '0',
  `list_view` tinyint(4) NOT NULL DEFAULT '0',
  `detail_view` tinyint(4) NOT NULL DEFAULT '1',
  `images` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `params` text NULL,
  `description` text NULL,
  `access` INT(10) UNSIGNED NOT NULL DEFAULT '1',
  `asset_id` INT UNSIGNED NOT NULL DEFAULT '1',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_field_content_map`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_field_content_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contentid` int(11) NOT NULL DEFAULT '0',
  `fieldsid` int(11) NOT NULL DEFAULT '0',
  `value` text NULL,
  `images` text NULL,
  `imagetitle` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_field_fieldgroup_map`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_field_fieldgroup_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldsid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_tags`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `description` text NULL,
  `params` text NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_tag_content_map`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_tag_content_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tagsid` int(11) NOT NULL DEFAULT '0',
  `contentid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_templates`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `home` char(7) NOT NULL DEFAULT '0',
  `protected` tinyint(3) NOT NULL DEFAULT '0',
  `layout` text NULL,
  `params` text NULL,
  `preset` VARCHAR( 255 ) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `#__tz_portfolio_plus_templates`
--
INSERT IGNORE INTO `#__tz_portfolio_plus_templates` (`id`, `template`, `title`, `home`, `protected`, `layout`, `params`) VALUES
(1, 'system', 'system - Default', '0', 1, '[{"name":"Media","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"20px 0","containertype":"container-fluid","children":[{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"media","customclass":"","responsiveclass":""}]},{"name":"Title","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"container-fluid","children":[{"col-xs":"","col-sm":"","col-md":"","col-lg":"10","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"title","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"2","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"icons","customclass":"","responsiveclass":""}]},{"name":"Information","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"container-fluid","children":[{"col-xs":"","col-sm":"","col-md":"","col-lg":"6","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"none","customclass":"muted","responsiveclass":"","children":[{"name":"Information Core","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"created_date","position":"","style":"","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"vote","position":"","style":"","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"author","position":"","style":"","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"category","position":"","style":"","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"parent_category","position":"","style":"","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"hits","position":"","style":"","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"published_date","position":"","style":"","customclass":"","responsiveclass":""},{"col-xs":"","col-sm":"","col-md":"","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"modified_date","position":"","style":"","customclass":"","responsiveclass":""}]}]},{"col-xs":"","col-sm":"","col-md":"","col-lg":"6","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"extrafields","customclass":"","responsiveclass":""}]},{"name":"Introtext","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"container-fluid","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"introtext","customclass":"","responsiveclass":""}]},{"name":"Fulltext","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"container-fluid","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"fulltext","customclass":"","responsiveclass":""}]},{"name":"Tags","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"container-fluid","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"tags","customclass":"","responsiveclass":""}]},{"name":"Author Info","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"container-fluid","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"author_about","customclass":"","responsiveclass":""}]},{"name":"Related Articles","class":"","responsive":"","backgroundcolor":"rgba(255, 255, 255, 0)","textcolor":"rgba(255, 255, 255, 0)","linkcolor":"rgba(255, 255, 255, 0)","linkhovercolor":"rgba(255, 255, 255, 0)","margin":"","padding":"","containertype":"container-fluid","children":[{"col-xs":"12","col-sm":"12","col-md":"12","col-lg":"12","col-xs-offset":"","col-sm-offset":"","col-md-offset":"","col-lg-offset":"","type":"related","customclass":"","responsiveclass":""}]}]', '{"layout":"default","use_single_layout_builder":"1"}'),
(2, 'elegant', 'elegant - Default', '1', 1, '', '{"layout":"default","use_single_layout_builder":"0","load_style":"1"}');