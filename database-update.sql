CREATE TABLE IF NOT EXISTS `toner` ( `toner_id` int(11) NOT NULL auto_increment, `device_id` int(11) NOT NULL default '0', `toner_index` int(11) NOT NULL, `toner_type` varchar(64) NOT NULL, `toner_oid` varchar(64) NOT NULL, `toner_descr` varchar(32) NOT NULL default '', `toner_capacity` int(11) NOT NULL default '0', `toner_current` int(11) NOT NULL default '0', PRIMARY KEY (`toner_id`), KEY `device_id` (`device_id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE  `mempools` CHANGE  `mempool_descr`  `mempool_descr` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE  `processors` CHANGE  `processor_descr`  `processor_descr` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
DROP TABLE `cempMemPool`;
DROP TABLE `cpmCPU`;
DROP TABLE `cmpMemPool`;
ALTER TABLE `mempools` CHANGE `mempool_used` `mempool_used` BIGINT( 16 ) NOT NULL ,CHANGE `mempool_free` `mempool_free` BIGINT( 16 ) NOT NULL ,CHANGE `mempool_total` `mempool_total` BIGINT( 16 ) NOT NULL ,CHANGE `mempool_largestfree` `mempool_largestfree` BIGINT( 16 ) NULL DEFAULT NULL ,CHANGE `mempool_lowestfree` `mempool_lowestfree` BIGINT( 16 ) NULL DEFAULT NULL ;
ALTER TABLE  `ports` ADD  `port_descr_type` VARCHAR( 32 ) NULL DEFAULT NULL AFTER  `device_id` ,ADD  `port_descr_descr` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `port_descr_type` ,ADD  `port_descr_circuit` VARCHAR( 64 ) NULL DEFAULT NULL AFTER  `port_descr_descr` ,ADD  `port_descr_speed` VARCHAR( 32 ) NULL DEFAULT NULL AFTER  `port_descr_circuit` ,ADD  `port_descr_notes` VARCHAR( 128 ) NULL DEFAULT NULL AFTER  `port_descr_speed`;
CREATE TABLE IF NOT EXISTS `frequency` ( `freq_id` int(11) NOT NULL auto_increment, `device_id` int(11) NOT NULL default '0', `freq_oid` varchar(64) NOT NULL, `freq_index` varchar(8) NOT NULL, `freq_type` varchar(32) NOT NULL, `freq_descr` varchar(32) NOT NULL default '', `freq_precision` int(11) NOT NULL default '1', `freq_current` float default NULL, `freq_limit` float default NULL, `freq_limit_low` float default NULL, PRIMARY KEY  (`freq_id`), KEY `freq_host` (`device_id`)) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=latin1;
ALTER TABLE  `temperature` CHANGE  `temp_index`  `temp_index` int(11) NOT NULL;
CREATE TABLE IF NOT EXISTS `current` ( `current_id` int(11) NOT NULL auto_increment, `device_id` int(11) NOT NULL default '0', `current_oid` varchar(64) NOT NULL, `current_index` varchar(8) NOT NULL, `current_type` varchar(32) NOT NULL, `current_descr` varchar(32) NOT NULL default '', `current_precision` int(11) NOT NULL default '1', `current_current` float default NULL, `current_limit` float default NULL, `current_limit_warn` float default NULL, `current_limit_low` float default NULL, PRIMARY KEY  (`current_id`), KEY `current_host` (`device_id`)) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=latin1;
ALTER TABLE `devices` ADD `serial` text default NULL;
ALTER TABLE  `temperature` CHANGE  `temp_index`  `temp_index` VARCHAR(32) NOT NULL;
ALTER TABLE  `ports` CHANGE  `ifDescr`  `ifDescr` VARCHAR(255) NOT NULL;
CREATE TABLE IF NOT EXISTS `ucd_diskio` (  `diskio_id` int(11) NOT NULL AUTO_INCREMENT,  `device_id` int(11) NOT NULL,  `diskio_index` int(11) NOT NULL,  `diskio_descr` varchar(32) NOT NULL,  PRIMARY KEY (`diskio_id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;
ALTER TABLE `eventlog` CHANGE  `type`  `type` VARCHAR( 64 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE  `bills` ADD  `bill_custid` VARCHAR( 64 ) NOT NULL ,ADD  `bill_ref` VARCHAR( 64 ) NOT NULL ,ADD  `bill_notes` VARCHAR( 256 ) NOT NULL;
CREATE TABLE IF NOT EXISTS `applications` (  `app_id` int(11) NOT NULL AUTO_INCREMENT,  `device_id` int(11) NOT NULL,  `app_type` varchar(64) NOT NULL,  PRIMARY KEY (`app_id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;
## 0.10.6
CREATE TABLE IF NOT EXISTS `sensors` ( `sensor_id` int(11) NOT NULL auto_increment, `sensor_class` varchar(64) NOT NULL, `device_id` int(11) NOT NULL default '0', `sensor_oid` varchar(64) NOT NULL, `sensor_index` varchar(8) NOT NULL, `sensor_type` varchar(32) NOT NULL, `sensor_descr` varchar(32) NOT NULL default '', `sensor_precision` int(11) NOT NULL default '1', `sensor_current` float default NULL, `sensor_limit` float default NULL, `sensor_limit_warn` float default NULL, `sensor_limit_low` float default NULL, `sensor_limit_low_warn` float default NULL, PRIMARY KEY  (`sensor_id`), KEY `sensor_host` (`device_id`)) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=latin1;
ALTER TABLE  `devices` CHANGE  `type`  `type` VARCHAR(20) NOT NULL;
ALTER TABLE  `voltage` CHANGE  `volt_index`  `volt_index` VARCHAR(10) NOT NULL;
ALTER TABLE  `frequency` CHANGE  `freq_index`  `freq_index` VARCHAR(10) NOT NULL;
ALTER TABLE  `sensors` CHANGE  `sensor_index`  `sensor_index` VARCHAR(10) NOT NULL;
DROP TABLE `fanspeed`;
DROP TABLE `temperature`;
DROP TABLE `voltage`;
DROP TABLE `current`;
ALTER TABLE  `sensors` ADD  `entPhysicalIndex` VARCHAR( 16 ) NULL;
ALTER TABLE `sensors`  ADD `entPhysicalIndex_measured` VARCHAR(16) NULL;
ALTER TABLE  `processors` DROP INDEX  `cpuCPU_id`;
ALTER TABLE  `processors` ADD INDEX (  `device_id` );
ALTER TABLE  `ucd_diskio` ADD INDEX (  `device_id` );
ALTER TABLE  `storage` ADD INDEX (  `device_id` );
ALTER TABLE  `mac_accounting` ADD INDEX (  `interface_id` );
ALTER TABLE  `ipv4_addresses` ADD INDEX (  `interface_id` );
ALTER TABLE  `ipv6_addresses` ADD INDEX (  `interface_id` );
ALTER TABLE  `ipv4_mac` ADD INDEX (  `interface_id` );
CREATE TABLE IF NOT EXISTS `ports_adsl` (  `interface_id` int(11) NOT NULL,  `port_adsl_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  `adslLineCoding` varchar(8) COLLATE utf8_bin NOT NULL,  `adslLineType` varchar(16) COLLATE utf8_bin NOT NULL,  `adslAtucInvVendorID` varchar(8) COLLATE utf8_bin NOT NULL,  `adslAtucInvVersionNumber` varchar(8) COLLATE utf8_bin NOT NULL,  `adslAtucCurrSnrMgn` decimal(5,1) NOT NULL,  `adslAtucCurrAtn` decimal(5,1) NOT NULL,  `adslAtucCurrOutputPwr` decimal(5,1) NOT NULL,  `adslAtucCurrAttainableRate` int(11) NOT NULL,  `adslAtucChanCurrTxRate` int(11) NOT NULL,  `adslAturInvSerialNumber` varchar(8) COLLATE utf8_bin NOT NULL,  `adslAturInvVendorID` varchar(8) COLLATE utf8_bin NOT NULL,  `adslAturInvVersionNumber` varchar(8) COLLATE utf8_bin NOT NULL,  `adslAturChanCurrTxRate` int(11) NOT NULL,  `adslAturCurrSnrMgn` decimal(5,1) NOT NULL,  `adslAturCurrAtn` decimal(5,1) NOT NULL,  `adslAturCurrOutputPwr` decimal(5,1) NOT NULL,  `adslAturCurrAttainableRate` int(11) NOT NULL,  UNIQUE KEY `interface_id` (`interface_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
## 0.10.7
ALTER TABLE `devices` ADD  `last_polled_timetaken` DOUBLE( 5, 2 ) NOT NULL AFTER  `last_polled` , ADD  `last_discovered_timetaken` DOUBLE( 5, 2 ) NOT NULL AFTER  `last_polled_timetaken`;
CREATE TABLE IF NOT EXISTS `perf_times` (  `type` varchar(8) NOT NULL,  `doing` varchar(64) NOT NULL,  `start` int(11) NOT NULL,  `duration` double(5,2) NOT NULL,  `devices` int(11) NOT NULL,  KEY `type` (`type`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
## 0.10.7.1
ALTER TABLE `bills` ADD `bill_autoadded` BOOLEAN NOT NULL DEFAULT '0'
ALTER TABLE `bill_ports` ADD `bill_port_autoadded` BOOLEAN NOT NULL DEFAULT '0'
CREATE TABLE IF NOT EXISTS `device_graphs` (  `device_id` int(11) NOT NULL,  `graph` varchar(32) COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS `graph_types`;
CREATE TABLE IF NOT EXISTS `graph_types` (  `graph_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `graph_subtype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `graph_section` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `graph_descr` varchar(64) COLLATE utf8_unicode_ci NOT NULL,  `graph_order` int(11) NOT NULL,  KEY `graph_type` (`graph_type`),  KEY `graph_subtype` (`graph_subtype`),  KEY `graph_section` (`graph_section`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `graph_types` (`graph_type`, `graph_subtype`, `graph_section`, `graph_descr`, `graph_order`) VALUES('device', 'bits', 'netstats', 'Total Traffic', 0),('device', 'hr_users', 'system', 'Users Logged In', 0),('device', 'ucd_load', 'system', 'Load Averages', 0),('device', 'ucd_cpu', 'system', 'Detailed Processor Usage', 0),('device', 'ucd_memory', 'system', 'Detailed Memory Usage', 0),('device', 'netstat_tcp', 'netstats', 'TCP Statistics', 0),('device', 'netstat_icmp_info', 'netstats', 'ICMP Informational Statistics', 0),('device', 'netstat_icmp_stat', 'netstats', 'ICMP Statistics', 0),('device', 'netstat_ip', 'netstats', 'IP Statistics', 0),('device', 'netstat_ip_frag', 'netstats', 'IP Fragmentation Statistics', 0),('device', 'netstat_udp', 'netstats', 'UDP Statistics', 0),('device', 'netstat_snmp', 'netstats', 'SNMP Statistics', 0),('device', 'temperatures', 'system', 'Temperatures', 0),('device', 'mempools', 'system', 'Memory Pool Usage', 0),('device', 'processors', 'system', 'Processor Usage', 0),('device', 'storage', 'system', 'Filesystem Usage', 0),('device', 'hr_processes', 'system', 'Running Processes', 0),('device', 'uptime', 'system', 'System Uptime', ''),('device', 'ipsystemstats_ipv4', 'netstats', 'IPv4 Packet Statistics', 0),('device', 'ipsystemstats_ipv6_frag', 'netstats', 'IPv6 Fragmentation Statistics', 0),('device', 'ipsystemstats_ipv6', 'netstats', 'IPv6 Packet Statistics', 0),('device', 'ipsystemstats_ipv4_frag', 'netstats', 'IPv4 Fragmentation Statistics', 0),('device',  'fortigate_sessions',  'firewall',  'Active Sessions',  ''), ('device',  'screenos_settings',  'firewall',  'Active Sessions',  '');

