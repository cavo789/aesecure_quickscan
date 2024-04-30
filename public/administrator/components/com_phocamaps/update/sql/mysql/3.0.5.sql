ALTER TABLE `#__phocamaps_marker` ADD COLUMN `osm_icon` varchar(100) NOT NULL default '';
ALTER TABLE `#__phocamaps_marker` ADD COLUMN `osm_marker_color` varchar(20) NOT NULL default '';
ALTER TABLE `#__phocamaps_marker` ADD COLUMN `osm_icon_color` varchar(20) NOT NULL default '';
ALTER TABLE `#__phocamaps_marker` ADD COLUMN `osm_icon_prefix` varchar(20) NOT NULL default '';
ALTER TABLE `#__phocamaps_marker` ADD COLUMN `osm_icon_spin` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__phocamaps_marker` ADD COLUMN `osm_icon_class` text NOT NULL;



