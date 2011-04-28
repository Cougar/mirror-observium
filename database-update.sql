## 0.10.7.1
ALTER TABLE `bills` ADD `bill_autoadded` BOOLEAN NOT NULL DEFAULT '0';
ALTER TABLE `bill_ports` ADD `bill_port_autoadded` BOOLEAN NOT NULL DEFAULT '0';
ALTER TABLE `sensors` CHANGE  `sensor_precision`  `sensor_divisor` INT( 11 ) NOT NULL DEFAULT  '1'
ALTER TABLE `sensors` ADD  `sensor_multiplier` INT( 11 ) NOT NULL AFTER  `sensor_divisor`;
CREATE TABLE IF NOT EXISTS `device_graphs` (  `device_id` int(11) NOT NULL,  `graph` varchar(32) COLLATE utf8_unicode_ci NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
DROP TABLE IF EXISTS `graph_types`;
CREATE TABLE IF NOT EXISTS `graph_types` (  `graph_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `graph_subtype` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `graph_section` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `graph_descr` varchar(64) COLLATE utf8_unicode_ci NOT NULL,  `graph_order` int(11) NOT NULL,  KEY `graph_type` (`graph_type`),  KEY `graph_subtype` (`graph_subtype`),  KEY `graph_section` (`graph_section`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `graph_types` (`graph_type`, `graph_subtype`, `graph_section`, `graph_descr`, `graph_order`) VALUES('device', 'bits', 'netstats', 'Total Traffic', 0),('device', 'hr_users', 'system', 'Users Logged In', 0),('device', 'ucd_load', 'system', 'Load Averages', 0),('device', 'ucd_cpu', 'system', 'Detailed Processor Usage', 0),('device', 'ucd_memory', 'system', 'Detailed Memory Usage', 0),('device', 'netstat_tcp', 'netstats', 'TCP Statistics', 0),('device', 'netstat_icmp_info', 'netstats', 'ICMP Informational Statistics', 0),('device', 'netstat_icmp_stat', 'netstats', 'ICMP Statistics', 0),('device', 'netstat_ip', 'netstats', 'IP Statistics', 0),('device', 'netstat_ip_frag', 'netstats', 'IP Fragmentation Statistics', 0),('device', 'netstat_udp', 'netstats', 'UDP Statistics', 0),('device', 'netstat_snmp', 'netstats', 'SNMP Statistics', 0),('device', 'temperatures', 'system', 'Temperatures', 0),('device', 'mempools', 'system', 'Memory Pool Usage', 0),('device', 'processors', 'system', 'Processor Usage', 0),('device', 'storage', 'system', 'Filesystem Usage', 0),('device', 'hr_processes', 'system', 'Running Processes', 0),('device', 'uptime', 'system', 'System Uptime', ''),('device', 'ipsystemstats_ipv4', 'netstats', 'IPv4 Packet Statistics', 0),('device', 'ipsystemstats_ipv6_frag', 'netstats', 'IPv6 Fragmentation Statistics', 0),('device', 'ipsystemstats_ipv6', 'netstats', 'IPv6 Packet Statistics', 0),('device', 'ipsystemstats_ipv4_frag', 'netstats', 'IPv4 Fragmentation Statistics', 0),('device',  'fortigate_sessions',  'firewall',  'Active Sessions',  ''), ('device',  'screenos_sessions',  'firewall',  'Active Sessions',  ''), ('device',  'fdb_count',  'system',  'MAC Addresses Learnt',  '0'),('device', 'cras_sessions', 'firewall', 'Remote Access Sessions', 0);
DROP TABLE `frequency`;
ALTER TABLE  `mempools` CHANGE  `mempool_index`  `mempool_index` VARCHAR( 16 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `vrfs` CHANGE `mplsVpnVrfRouteDistinguisher` `mplsVpnVrfRouteDistinguisher` varchar(26) default NOT NULL;
## Change port rrds
ALTER TABLE  `devices` ADD  `timeout` INT NULL DEFAULT NULL AFTER  `port`;
ALTER TABLE `devices` ADD `retries` INT NULL DEFAULT NULL AFTER `timeout`;
ALTER TABLE `ports` ADD `disabled` tinyint(1) NOT NULL DEFAULT '0' AFTER `ignore`;
ALTER TABLE  `perf_times` CHANGE  `duration`  `duration` DOUBLE( 8, 2 ) NOT NULL
ALTER TABLE `sensors` ADD `poller_type` VARCHAR(16) NOT NULL DEFAULT 'snmp' AFTER `device_id`;
## Add transport
ALTER TABLE `devices` ADD `transport` VARCHAR(16) NOT NULL DEFAULT 'udp' AFTER `port`;
## Extend port descriptions
ALTER TABLE ports MODIFY port_descr_circuit VARCHAR(255);
ALTER TABLE ports MODIFY port_descr_descr VARCHAR(255);
ALTER TABLE ports MODIFY port_descr_notes VARCHAR(255);
ALTER TABLE devices MODIFY community VARCHAR(255);
ALTER TABLE users MODIFY password VARCHAR(34);
ALTER TABLE sensors MODIFY sensor_descr VARCHAR(255);
ALTER TABLE `vrfs` MODIFY  `mplsVpnVrfRouteDistinguisher` VARCHAR(128);
ALTER TABLE `vrfs` MODIFY  `vrf_name` VARCHAR(128);
ALTER TABLE `ports` MODIFY  `ifDescr` VARCHAR(255);
CREATE TABLE IF NOT EXISTS `vminfo` (`id` int(11) NOT NULL AUTO_INCREMENT, `device_id` int(11) NOT NULL, `vmwVmVMID` int(11) NOT NULL, `vmwVmDisplayName` varchar(128) NOT NULL, `vmwVmGuestOS` varchar(128) NOT NULL, `vmwVmMemSize` int(11) NOT NULL, `vmwVmCpus` int(11) NOT NULL, `vmwVmState` varchar(128) NOT NULL, PRIMARY KEY  (`id`), KEY `device_id` (`device_id`), KEY `vmwVmVMID` (`vmwVmVMID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `ports` MODIFY  `port_descr_type` VARCHAR(255);
RENAME TABLE  `vmware_vminfo` TO  `vminfo` ;
ALTER TABLE `vminfo` ADD `vm_type` VARCHAR(16) NOT NULL DEFAULT 'vmware' AFTER `device_id`;
CREATE TABLE IF NOT EXISTS `cef_switching` (  `device_id` int(11) NOT NULL,  `entPhysicalIndex` int(11) NOT NULL,  `afi` varchar(4) COLLATE utf8_unicode_ci NOT NULL,  `cef_index` int(11) NOT NULL,  `cef_path` varchar(16) COLLATE utf8_unicode_ci NOT NULL,  `drop` int(11) NOT NULL,  `punt` int(11) NOT NULL,  `punt2host` int(11) NOT NULL,  `drop_prev` int(11) NOT NULL,  `punt_prev` int(11) NOT NULL,  `punt2host_prev` int(11) NOT NULL,`updated` INT NOT NULL ,  `updated_prev` INT NOT NULL )  ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE  `mac_accounting` CHANGE  `peer_mac`  `mac` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `mac_accounting`  DROP `peer_ip`,  DROP `peer_desc`,  DROP `peer_asn`;
UPDATE sensors SET sensor_class='frequency' WHERE sensor_class='freq';
ALTER TABLE  `cef_switching` ADD  `cef_switching_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
ALTER TABLE  `mempools` ADD  `mempool_perc` INT NOT NULL AFTER  `device_id`;
ALTER TABLE  `observer_dev`.`ports` ADD UNIQUE  `device_ifIndex` (  `device_id` ,  `ifIndex` );
ALTER TABLE  `ports` DROP INDEX  `host`;
ALTER TABLE  `ports` DROP INDEX  `snmpid`;
CREATE TABLE IF NOT EXISTS `ospf_areas` (  `device_id` int(11) NOT NULL,  `ospfAreaId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfAuthType` varchar(64) COLLATE utf8_unicode_ci NOT NULL,  `ospfImportAsExtern` varchar(128) COLLATE utf8_unicode_ci NOT NULL,  `ospfSpfRuns` int(11) NOT NULL,  `ospfAreaBdrRtrCount` int(11) NOT NULL,  `ospfAsBdrRtrCount` int(11) NOT NULL,  `ospfAreaLsaCount` int(11) NOT NULL,  `ospfAreaLsaCksumSum` int(11) NOT NULL,  `ospfAreaSummary` varchar(64) COLLATE utf8_unicode_ci NOT NULL,  `ospfAreaStatus` varchar(64) COLLATE utf8_unicode_ci NOT NULL,  UNIQUE KEY `device_area` (`device_id`,`ospfAreaId`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `ospf_instances` (  `device_id` int(11) NOT NULL,  `ospf_instance_id` int(11) NOT NULL,  `ospfRouterId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfAdminStat` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfVersionNumber` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfAreaBdrRtrStatus` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfASBdrRtrStatus` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfExternLsaCount` int(11) NOT NULL,  `ospfExternLsaCksumSum` int(11) NOT NULL,  `ospfTOSSupport` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfOriginateNewLsas` int(11) NOT NULL,  `ospfRxNewLsas` int(11) NOT NULL,  `ospfExtLsdbLimit` int(11) DEFAULT NULL,  `ospfMulticastExtensions` int(11) DEFAULT NULL,  `ospfExitOverflowInterval` int(11) DEFAULT NULL,  `ospfDemandExtensions` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  UNIQUE KEY `device_id` (`device_id`,`ospf_instance_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `ospf_ports` (  `device_id` int(11) NOT NULL,  `interface_id` int(11) NOT NULL,  `ospf_port_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfIfIpAddress` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfAddressLessIf` int(11) NOT NULL,  `ospfIfAreaId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,  `ospfIfType` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfAdminStat` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfRtrPriority` int(11) DEFAULT NULL,  `ospfIfTransitDelay` int(11) DEFAULT NULL,  `ospfIfRetransInterval` int(11) DEFAULT NULL,  `ospfIfHelloInterval` int(11) DEFAULT NULL,  `ospfIfRtrDeadInterval` int(11) DEFAULT NULL,  `ospfIfPollInterval` int(11) DEFAULT NULL,  `ospfIfState` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfDesignatedRouter` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfBackupDesignatedRouter` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfEvents` int(11) DEFAULT NULL,  `ospfIfAuthKey` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfStatus` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfMulticastForwarding` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfDemand` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  `ospfIfAuthType` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,  UNIQUE KEY `device_id` (`device_id`,`interface_id`,`ospf_port_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE IF NOT EXISTS `ports_stack` (`interface_id_high` INT NOT NULL ,`interface_id_low` INT NOT NULL) ENGINE = INNODB;
ALTER TABLE `ports_stack` ADD `device_id` INT NOT NULL;
ALTER TABLE `ports_stack` ADD `ifStackStatus` VARCHAR(32);
