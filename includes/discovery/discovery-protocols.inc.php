<?php

echo("Discovery protocols:");

global $link_exists;

$community = $device['community'];

if ($device['os'] == "ironware")
{
  echo(" Brocade FDP: ");
  $fdp_array = snmpwalk_cache_twopart_oid($device, "snFdpCacheEntry", array(), "FOUNDRY-SN-SWITCH-GROUP-MIB");
  if ($fdp_array)
  {
    unset($fdp_links);
    foreach (array_keys($fdp_array) as $key)
    {
      /// FIXME dbFacile
      $port = mysql_fetch_assoc(mysql_query("SELECT * FROM `ports` WHERE device_id = '".$device['device_id']."' AND `ifIndex` = '".$key."'"));
      $fdp_if_array = $fdp_array[$key];
      foreach (array_keys($fdp_if_array) as $entry_key)
      {
        $fdp = $fdp_if_array[$entry_key];
        /// FIXME dbFacile
        $remote_device_id = @mysql_result(mysql_query("SELECT `device_id` FROM `devices` WHERE `sysName` = '".$fdp['snFdpCacheDeviceId']."' OR `hostname`='".$fdp['snFdpCacheDeviceId']."'"), 0);

        // FIXME do LLDP-code-style hostname overwrite here as well? (see below)

        if (!$remote_device_id)
        {
          $remote_device_id = discover_new_device($fdp['snFdpCacheDeviceId']);
          if ($remote_device_id)
          {
            humanize_port($port);
            log_event("Device autodiscovered through FDP on " . $device['hostname'] . " (port " . $port['label'] . ")", $remote_device_id, 'interface', $port['port_id']);
          }
        }

        if ($remote_device_id)
        {
          $if = $fdp['snFdpCacheDevicePort'];
          /// FIXME dbFacile
          $remote_port_id = @mysql_result(mysql_query("SELECT port_id FROM `ports` WHERE (`ifDescr` = '$if' OR `ifName`='$if') AND `device_id` = '".$remote_device_id."'"),0);
        } else {
          $remote_port_id = "0";
        }

        discover_link($port['port_id'], $fdp['snFdpCacheVendorId'], $remote_port_id, $fdp['snFdpCacheDeviceId'], $fdp['snFdpCacheDevicePort'], $fdp['snFdpCachePlatform'], $fdp['snFdpCacheVersion']);
      }
    }
  }
}

echo(" CISCO-CDP-MIB: ");
unset($cdp_array);
$cdp_array = snmpwalk_cache_twopart_oid($device, "cdpCache", array(), "CISCO-CDP-MIB");
if ($cdp_array)
{
  unset($cdp_links);
  foreach (array_keys($cdp_array) as $key)
  {
    $port = dbFetchRow("SELECT * FROM `ports` WHERE device_id = ? AND `ifIndex` = ?", array($device['device_id'], $key));
    $cdp_if_array = $cdp_array[$key];
    foreach (array_keys($cdp_if_array) as $entry_key)
    {
      $cdp = $cdp_if_array[$entry_key];
      if (is_valid_hostname($cdp['cdpCacheDeviceId']))
      {
        $remote_device_id = dbFetchCell("SELECT `device_id` FROM `devices` WHERE `sysName` = ? OR `hostname` = ?", array($cdp['cdpCacheDeviceId'], $cdp['cdpCacheDeviceId']));
        
        // FIXME do LLDP-code-style hostname overwrite here as well? (see below)

        if (!$remote_device_id)
        {
          $remote_device_id = discover_new_device($cdp['cdpCacheDeviceId']);
          if ($remote_device_id)
          {
            humanize_port($port);
            log_event("Device autodiscovered through CDP on " . $device['hostname'] . " (port " . $port['label'] . ")", $remote_device_id, 'interface', $port['port_id']);
          }
        }

        if ($remote_device_id)
        {
          $if = $cdp['cdpCacheDevicePort'];
          $remote_port_id = dbFetchCell("SELECT port_id FROM `ports` WHERE (`ifDescr` = ? OR `ifName` = ?) AND `device_id` = ?", array($if, $if, $remote_device_id));
        } else { 
          $remote_port_id = "0";
        }

        if ($port['port_id'] && $cdp['cdpCacheDeviceId'] && $cdp['cdpCacheDevicePort'])
        {
          discover_link($port['port_id'], 'cdp', $remote_port_id, $cdp['cdpCacheDeviceId'], $cdp['cdpCacheDevicePort'], $cdp['cdpCachePlatform'], $cdp['cdpCacheVersion']);
        }
      }
      else
      {
        echo("X");
      }
    }
  }
}

echo(" LLDP-MIB: ");

unset($lldp_array);
$lldp_array = snmpwalk_cache_threepart_oid($device, "lldpRemoteSystemsData", array(), "LLDP-MIB");
$dot1d_array = snmpwalk_cache_oid($device, "dot1dBasePortIfIndex", array(), "BRIDGE-MIB");

if ($lldp_array)
{
  $lldp_links = "";
  foreach (array_keys($lldp_array) as $key)
  {
    $lldp_if_array = $lldp_array[$key];
    foreach (array_keys($lldp_if_array) as $entry_key)
    {
      if (is_numeric($dot1d_array[$entry_key]['dot1dBasePortIfIndex']))
      {
        $ifIndex = $dot1d_array[$entry_key]['dot1dBasePortIfIndex'];
      } else {
        $ifIndex = $entry_key;
      }

      $port = dbFetchRow("SELECT * FROM `ports` WHERE device_id = ? AND `ifIndex` = ?", array($device['device_id'], $ifIndex));
      $lldp_instance = $lldp_if_array[$entry_key];
      foreach (array_keys($lldp_instance) as $entry_instance)
      {
        $lldp = $lldp_instance[$entry_instance];
        $remote_device = dbFetchRow("SELECT `device_id`, `hostname` FROM `devices` WHERE `sysName` = ? OR `hostname` = ?", array($lldp['lldpRemSysName'],$lldp['lldpRemSysName']));
        $remote_device_id = $remote_device['device_id']; 

        // Overwrite remote hostname with the one we know, for devices that we identify by sysName
        $lldp['lldpRemSysName'] = $remote_device['hostname'];

        if (!$remote_device_id && is_valid_hostname($lldp['lldpRemSysName']))
        {
          $remote_device_id = discover_new_device($lldp['lldpRemSysName']);
          if ($remote_device_id)
          {
            humanize_port($port);
            log_event("Device autodiscovered through LLDP on " . $device['hostname'] . " (port " . $port['label'] . ")", $remote_device_id, 'interface', $port['port_id']);
          }
        }

        if ($remote_device_id)
        {
          $if = $lldp['lldpRemPortDesc']; $id = $lldp['lldpRemPortId'];
          $remote_port_id = dbFetchCell("SELECT port_id FROM `ports` WHERE `ifIndex`= ? AND `device_id` = ?",array($id,$remote_device_id));
          if (!$remote_port_id)
          {
            $remote_port_id = dbFetchCell("SELECT port_id FROM `ports` WHERE (`ifDescr`= ? OR `ifName`= ?) AND `device_id` = ?",array($id,$id,$remote_device_id));
            if (!$remote_port_id)
            {
              $remote_port_id = dbFetchCell("SELECT port_id FROM `ports` WHERE (`ifDescr`= ? OR `ifName`= ?) AND `device_id` = ?",array($if,$if,$remote_device_id));
              if (!$remote_port_id)
              {
                if ($lldp['lldpRemChassisIdSubtype'] == 'macAddress')
                { // Find the port by MAC address, still matches multiple ports sometimes, we use the first one and hope we're lucky
                  $remote_port_id = dbFetchCell("SELECT port_id FROM `ports` WHERE `ifPhysAddress` = ? AND `device_id` = ?", array(str_replace(' ','',$lldp['lldpRemChassisId']),$remote_device_id));
                }
              }
            }
          }
        } else {
          $remote_port_id = "0";
        }

        if (is_numeric($port['port_id']) && isset($lldp['lldpRemSysName']) && isset($lldp['lldpRemPortId']))
        {
          discover_link($port['port_id'], 'lldp', $remote_port_id, $lldp['lldpRemSysName'], $lldp['lldpRemPortId'], NULL, $lldp['lldpRemSysDesc']);
        }
      }
    }
  }
}

if ($debug) { print_r($link_exists); }

/// FIXME dbFacile
$sql = "SELECT * FROM `links` AS L, `ports` AS I WHERE L.local_port_id = I.port_id AND I.device_id = '".$device['device_id']."'";
if ($query = mysql_query($sql))
{
  while ($test = mysql_fetch_assoc($query))
  {
    $local_port_id = $test['local_port_id'];
    $remote_hostname = $test['remote_hostname'];
    $remote_port = $test['remote_port'];
    if ($debug) { echo("$local_port_id -> $remote_hostname -> $remote_port \n"); }
    if (!$link_exists[$local_port_id][$remote_hostname][$remote_port])
    {
      echo("-");
      mysql_query("DELETE FROM `links` WHERE id = '" . $test['id'] . "'");
      if ($debug) { echo(mysql_affected_rows()." deleted "); }
    }
  }
}

unset($link_exists);
echo("\n");

?>
