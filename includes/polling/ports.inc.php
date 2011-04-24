<?php

unset($ports);
$ports = snmp_cache_ifIndex($device); // Cache Port List

#mysql_query("INSERT INTO `ports` (`device_id`,`ifIndex`) VALUES ('".$device['device_id']."','$ifIndex')");

// Build SNMP Cache Array
$data_oids = array('ifName','ifDescr','ifAlias', 'ifAdminStatus', 'ifOperStatus', 'ifMtu', 'ifSpeed', 'ifHighSpeed', 'ifType', 'ifPhysAddress',
                   'ifPromiscuousMode','ifConnectorPresent','ifDuplex');

$stat_oids = array('ifInErrors', 'ifOutErrors', 'ifInUcastPkts', 'ifOutUcastPkts', 'ifInNUcastPkts', 'ifOutNUcastPkts',
                   'ifHCInMulticastPkts', 'ifHCInBroadcastPkts', 'ifHCOutMulticastPkts', 'ifHCOutBroadcastPkts',
                   'ifInOctets', 'ifOutOctets', 'ifHCInOctets', 'ifHCOutOctets', 'ifInDiscards', 'ifOutDiscards', 'ifInUnknownProtos',
                   'ifInBroadcastPkts', 'ifOutBroadcastPkts', 'ifInMulticastPkts', 'ifOutMulticastPkts');

$stat_oids_db = array('ifInOctets', 'ifOutOctets', 'ifInErrors', 'ifOutErrors', 'ifInUcastPkts', 'ifOutUcastPkts'); // From above for DB

$etherlike_oids = array('dot3StatsAlignmentErrors', 'dot3StatsFCSErrors', 'dot3StatsSingleCollisionFrames', 'dot3StatsMultipleCollisionFrames',
                        'dot3StatsSQETestErrors', 'dot3StatsDeferredTransmissions', 'dot3StatsLateCollisions', 'dot3StatsExcessiveCollisions',
                        'dot3StatsInternalMacTransmitErrors', 'dot3StatsCarrierSenseErrors', 'dot3StatsFrameTooLongs', 'dot3StatsInternalMacReceiveErrors',
                        'dot3StatsSymbolErrors');

$cisco_oids = array('locIfHardType', 'locIfInRunts', 'locIfInGiants', 'locIfInCRC', 'locIfInFrame', 'locIfInOverrun', 'locIfInIgnored', 'locIfInAbort',
                    'locIfCollisions', 'locIfInputQueueDrops', 'locIfOutputQueueDrops');

$pagp_oids = array('pagpOperationMode', 'pagpPortState', 'pagpPartnerDeviceId', 'pagpPartnerLearnMethod', 'pagpPartnerIfIndex', 'pagpPartnerGroupIfIndex',
                   'pagpPartnerDeviceName', 'pagpEthcOperationMode', 'pagpDeviceId', 'pagpGroupIfIndex');

$ifmib_oids = array_merge($data_oids, $stat_oids);

$ifmib_oids = array('ifEntry', 'ifXEntry');

echo("Caching Oids: ");
foreach ($ifmib_oids as $oid) { echo("$oid "); $port_stats = snmpwalk_cache_oid($device, $oid, $port_stats, "IF-MIB"); }

if ($config['enable_ports_etherlike'])
{
  echo("dot3Stats "); $port_stats = snmpwalk_cache_oid($device, "dot3StatsEntry", $port_stats, "EtherLike-MIB");
} else {
  echo("dot3StatsDuplexStatus"); $port_stats = snmpwalk_cache_oid($device, "dot3StatsDuplexStatus", $port_stats, "EtherLike-MIB");
}

if ($config['enable_ports_adsl'])
{
  $device['adsl_count'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `ports` WHERE `device_id` = '".$device['device_id']."' AND `ifType` = 'adsl'"),0);
}

if ($device['adsl_count'] > "0")
{
  echo("ADSL ");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.1.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.2.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.3.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.4.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.5.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.2", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.3", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.4", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.5", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.6", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.7", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.6.1.8", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.1", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.2", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.3", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.4", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.5", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.6", $port_stats, "ADSL-LINE-MIB");
  $port_stats = snmpwalk_cache_oid($device, ".1.3.6.1.2.1.10.94.1.1.7.1.7", $port_stats, "ADSL-LINE-MIB");
}

echo("\n");

/// FIXME This probably needs re-enabled. We need to clear these things when they get unset, too.
#foreach ($etherlike_oids as $oid) { $port_stats = snmpwalk_cache_oid($device, $oid, $port_stats, "EtherLike-MIB"); }
#foreach ($cisco_oids as $oid)     { $port_stats = snmpwalk_cache_oid($device, $oid, $port_stats, "OLD-CISCO-INTERFACES-MIB"); }
#foreach ($pagp_oids as $oid)      { $port_stats = snmpwalk_cache_oid($device, $oid, $port_stats, "CISCO-PAGP-MIB"); }

if ($device['os_group'] == "ios")
{
  $port_stats = snmp_cache_portIfIndex ($device, $port_stats);
  $port_stats = snmp_cache_portName ($device, $port_stats);
  $data_oids[] = "portName";

  /// FIXME Why is this commented out?
  #$port_stats = snmpwalk_cache_oid($device, "vmVlan", $port_stats, "CISCO-VLAN-MEMBERSHIP-MIB");
  #$port_stats = snmpwalk_cache_oid($device, "vlanTrunkPortEncapsulationOperType", $port_stats, "CISCO-VTP-MIB");
  #$port_stats = snmpwalk_cache_oid($device, "vlanTrunkPortNativeVlan", $port_stats, "CISCO-VTP-MIB");
}

$polled = time();

/// End Building SNMP Cache Array

if ($debug) { print_r($port_stats); }

/// New interface detection
///// FIXME
/// End New interface detection

/// Loop ports in the DB and update where necessary
$port_query = mysql_query("SELECT * FROM `ports` WHERE `device_id` = '".$device['device_id']."' AND `deleted` = 0");
while ($port = mysql_fetch_assoc($port_query))
{
  echo("Port " . $port['ifDescr'] . " ");
  if ($port_stats[$port['ifIndex']] && $port['disabled'] != "1")
  { // Check to make sure Port data is cached.
    $this_port = &$port_stats[$port['ifIndex']];
    if ($device['os'] == "vmware" && preg_match("/Device ([a-z0-9]+) at .*/", $this_port['ifDescr'], $matches)) { $this_port['ifDescr'] = $matches[1]; }
    $polled_period = $polled - $port['poll_time'];

    $update .= "`poll_time` = '".$polled."'";
    $update .= ", `poll_prev` = '".$port['poll_time']."'";
    $update .= ", `poll_period` = '".$polled_period."'";

    ### Copy ifHC[In|Out]Octets values to non-HC if they exist
    if ($this_port['ifHCInOctets'] > 0 && is_numeric($this_port['ifHCInOctets']) && $this_port['ifHCOutOctets'] > 0 && is_numeric($this_port['ifHCOutOctets']))
    {
      echo("HC ");
      $this_port['ifInOctets'] = $this_port['ifHCInOctets'];
	$this_port['ifOutOctets'] = $this_port['ifHCOutOctets'];
    }

    ### rewrite the ifPhysAddress

    if(strpos($this_port['ifPhysAddress'], ":"))
    {
      list($a_a, $a_b, $a_c, $a_d, $a_e, $a_f) = explode(":", $this_port['ifPhysAddress']);
      $ah_a = zeropad($a_a);
      $ah_b = zeropad($a_b);
      $ah_c = zeropad($a_c);
      $ah_d = zeropad($a_d);
      $ah_e = zeropad($a_e);
      $ah_f = zeropad($a_f);
      #$this_port['ifPhysAddress'] = $ah_a.":".$ah_b.":".$ah_c.":".$ah_d.":".$ah_e.":".$ah_f;
      $this_port['ifPhysAddress'] = $ah_a.$ah_b.$ah_c.$ah_d.$ah_e.$ah_f;
    }

    if (is_numeric($this_port['ifHCInBroadcastPkts']) && is_numeric($this_port['ifHCOutBroadcastPkts']) && is_numeric($this_port['ifHCInMulticastPkts']) && is_numeric($this_port['ifHCOutMulticastPkts']))
    {
      echo("HC ");
      $this_port['ifInBroadcastPkts'] = $this_port['ifHCInBroadcastPkts'];
      $this_port['ifOutBroadcastPkts'] = $this_port['ifHCOutBroadcastPkts'];
      $this_port['ifInMulticastPkts'] = $this_port['ifHCInMulticastPkts'];
      $this_port['ifOutMulticastPkts'] = $this_port['ifHCOutMulticastPkts'];
    }

    ### Overwrite ifSpeed with ifHighSpeed if it's over 10G
    if (is_numeric($this_port['ifHighSpeed']) && $this_port['ifSpeed'] > "1000000000")
    {
      echo("HighSpeed ");
      $this_port['ifSpeed'] = $this_port['ifHighSpeed'] * 1000000;
    }

    ### Overwrite ifDuplex with dot3StatsDuplexStatus if it exists
    if (isset($this_port['dot3StatsDuplexStatus']))
    {
      echo("dot3Duplex ");
      $this_port['ifDuplex'] = $this_port['dot3StatsDuplexStatus'];
    }

    ### Update IF-MIB data
    foreach ($data_oids as $oid)
    {
      if ($port[$oid] != $this_port[$oid] && !isset($this_port[$oid]))
      {
        $update .= ", `$oid` = NULL";
        log_event($oid . ": ".$port[$oid]." -> NULL", $device, 'interface', $port['interface_id']);
        if ($debug) { echo($oid . ": ".$port[$oid]." -> NULL "); } else { echo($oid . " "); }
      } elseif ($port[$oid] != $this_port[$oid]) {
        $update .= ", `$oid` = '".mres($this_port[$oid])."'";
	  log_event($oid . ": ".$port[$oid]." -> " . $this_port[$oid], $device, 'interface', $port['interface_id']);
        if ($debug) { echo($oid . ": ".$port[$oid]." -> " . $this_port[$oid]." "); } else { echo($oid . " "); }
      }
    }

    /// Parse description (usually ifAlias) if config option set

    if (isset($config['port_descr_parser']) && is_file($config['install_dir'] . "/" . $config['port_descr_parser']))
    {
      $port_attribs = array('type','descr','circuit','speed','notes');
      include($config['install_dir'] . "/" . $config['port_descr_parser']);

      foreach ($port_attribs as $attrib)
      {
        $attrib_key = "port_descr_".$attrib;
        if ($port_ifAlias[$attrib] != $port[$attrib_key])
        {
          $update .= ", `".$attrib_key."` = '".$port_ifAlias[$attrib]."'";
          log_event($attrib . ": ".$port[$attrib_key]." -> " . $port_ifAlias[$attrib], $device, 'interface', $port['interface_id']);
        }
      }
    }

    /// Ende parse ifAlias

    /// Update IF-MIB metrics
    foreach ($stat_oids_db as $oid)
    {
	$update .= ", `$oid` = '".$this_port[$oid]."'";
      $update .= ", `".$oid."_prev` = '".$port[$oid]."'";
      $oid_prev = $oid . "_prev";
      if ($port[$oid])
      {
        $oid_diff = $this_port[$oid] - $port[$oid];
        $oid_rate  = $oid_diff / $polled_period;
        if ($oid_rate < 0) { $oid_rate = "0"; }
        $update .= ", `".$oid."_rate` = '".$oid_rate."'";
        $update .= ", `".$oid."_delta` = '".$oid_diff."'";
        if ($debug) {echo("\n $oid ($oid_diff B) $oid_rate Bps $polled_period secs\n"); }
      }
    }

    /// Update RRDs
    $rrdfile = $host_rrd . "/port-" . safename($port['ifIndex'] . ".rrd");
    if (!is_file($rrdfile))
    {
      rrdtool_create($rrdfile," --step 300 \
      DS:INOCTETS:DERIVE:600:0:12500000000 \
      DS:OUTOCTETS:DERIVE:600:0:12500000000 \
      DS:INERRORS:DERIVE:600:0:12500000000 \
      DS:OUTERRORS:DERIVE:600:0:12500000000 \
      DS:INUCASTPKTS:DERIVE:600:0:12500000000 \
      DS:OUTUCASTPKTS:DERIVE:600:0:12500000000 \
      DS:INNUCASTPKTS:DERIVE:600:0:12500000000 \
      DS:OUTNUCASTPKTS:DERIVE:600:0:12500000000 \
      DS:INDISCARDS:DERIVE:600:0:12500000000 \
      DS:OUTDISCARDS:DERIVE:600:0:12500000000 \
      DS:INUNKNOWNPROTOS:DERIVE:600:0:12500000000 \
      DS:INBROADCASTPKTS:DERIVE:600:0:12500000000 \
      DS:OUTBROADCASTPKTS:DERIVE:600:0:12500000000 \
      DS:INMULTICASTPKTS:DERIVE:600:0:12500000000 \
      DS:OUTMULTICASTPKTS:DERIVE:600:0:12500000000 \
      RRA:AVERAGE:0.5:1:2400 \
      RRA:AVERAGE:0.5:6:1200 \
      RRA:AVERAGE:0.5:24:1200 \
      RRA:AVERAGE:0.5:288:1200 \
      RRA:MAX:0.5:1:600 \
      RRA:MAX:0.5:6:700 \
      RRA:MAX:0.5:24:775 \
      RRA:MAX:0.5:288:797");
    }

    foreach ($stat_oids as $oid)
    {  /// Copy values from array to global variables and force numeric.
      $$oid = $this_port[$oid];
      if (!is_numeric($$oid)) { $$oid = "0"; }
    }

    $if_rrd_update  = "$polled:$ifInOctets:$ifOutOctets:$ifInErrors:$ifOutErrors:$ifInUcastPkts:$ifOutUcastPkts:$ifInNUcastPkts:$ifOutNUcastPkts:$ifInDiscards:$ifOutDiscards:$ifInUnknownProtos";
    $if_rrd_update .= ":$ifInBroadcastPkts:$ifOutBroadcastPkts:$ifInMulticastPkts:$ifOutMulticastPkts";
    $ret = rrdtool_update("$rrdfile", $if_rrd_update);

#      if ($config['enable_ports_Xbcmc'] && $config['os'][$device['os']]['ifXmcbc']) {
#        if (!is_file($ifx_rrd)) { shell_exec($ifx_rrd_cmd); }
#        $ifx_rrd_update = "$polled:$ifHCInBroadcastPkts:$ifHCOutBroadcastPkts:$ifHCInMulticastPkts:$ifHCOutMulticastPkts";
#        $ret = rrdtool_update($ifx_rrd, $ifx_rrd_update);
#      }

    /// End Update IF-MIB

    /// Update PAgP
    if ($this_port['pagpOperationMode'] || $port['pagpOperationMode'])
    {
      foreach ($pagp_oids as $oid)
      { // Loop the OIDs
        if ($this_port[$oid] != $port[$oid])
        { // If data has changed, build a query
          $update .= ", `$oid` = '".mres($this_port[$oid])."'";
          echo("PAgP ");
	  log_event("$oid -> ".$this_port[$oid], $device, 'interface', $port['interface_id']);
        }
      }
    }
    // End Update PAgP

    /// Do EtherLike-MIB
    if ($config['enable_ports_etherlike']) { include("port-etherlike.inc.php"); }

    /// Do ADSL MIB
    if ($config['enable_ports_adsl']) { include("port-adsl.inc.php"); }

    /// Do PoE MIBs
    if ($config['enable_ports_poe']) { include("port-poe.inc.php"); }

    // Update MySQL
    if ($update)
    {
      $update_query  = "UPDATE `ports` SET ".$update." WHERE `interface_id` = '" . $port['interface_id'] . "'";
      @mysql_query($update_query);
      if ($debug) {echo("\nMYSQL : [ $update_query ]"); }
    }
    // End Update MySQL

    unset($update_query); unset($update);

    // Send alerts for interface flaps.
    if ($config['warn']['ifdown'] && ($port['ifOperStatus'] != $this_port['ifOperStatus']) && $port['ignore'] == 0)
    {
      if ($this_port['ifAlias'])
      {
        $falias = preg_replace('/^"/', '', $this_port['ifAlias']); $falias = preg_replace('/"$/', '', $falias); $full = $this_port['ifDescr'] . " (" . $falias . ")";
      } else {
        $full = $this_port['ifDescr'];
      }
      switch ($this_port['ifOperStatus'])
      {
        case "up":
          notify($device, "Interface UP - " . $device['hostname'] . " - " . $full, "Device:    " . $device['hostname'] . "\nInterface: " . $full . "\nTimestamp: " . date($config['timestamp_format']));
          break;
        case "down":
          notify($device, "Interface DOWN - " . $device['hostname'] . " - " . $full, "Device:    " . $device['hostname'] . "\nInterface: " . $full . "\nTimestamp: " . date($config['timestamp_format']));
          break;
      }
    }
  }
  elseif ($port['disabled'] != "1")
  {
    echo("Port Deleted"); // Port missing from SNMP cache.
    mysql_query("UPDATE `ports` SET `deleted` = '1' WHERE `device_id` = '".$device['device_id']."' AND `ifIndex` = '".$this_port['ifIndex']."'");
  } else {
    echo("Port Disabled.");
  }

  echo("\n");

  #### Clear Per-Port Variables Here
  unset($this_port);

}

#### Clear Variables Here
unset($port_stats);

?>
