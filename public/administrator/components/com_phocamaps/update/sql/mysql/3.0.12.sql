ALTER TABLE `#__phocamaps_map` ADD COLUMN `trackfiles_osm` varchar(255) NOT NULL default '';
ALTER TABLE `#__phocamaps_map` ADD COLUMN `trackcolors_osm` varchar(255) NOT NULL default '';
ALTER TABLE `#__phocamaps_map` ADD COLUMN `fitbounds_osm` tinyint(1) NOT NULL default '0';
ALTER TABLE `#__phocamaps_map` ADD COLUMN `gesturehandling` varchar(100) NOT NULL default '';




