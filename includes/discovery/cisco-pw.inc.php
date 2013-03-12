<?php

if ($config['enable_pseudowires'] && $device['os_group'] == "cisco")
{
  unset($cpw_count);
  unset($cpw_exists);

  echo("Cisco Pseudowires : ");

  // Pre-cache the existing state of pseudowires for this device from the database
  $pws_db_raw = dbFetchRows("SELECT * FROM `pseudowires` WHERE `device_id` = ?", array($device['device_id']));
  foreach ($pws_db_raw as $pw_db)
  {
    $device['pws_db'][$pw_db['cpwVcID']] = $pw_db['pseudowire_id'];
  }
  unset($pws_db_raw); unset($pw_db);

  $pws = snmpwalk_cache_oid($device, "cpwVcID", array(), "CISCO-IETF-PW-MPLS-MIB");
  $pws = snmpwalk_cache_oid($device, "cpwVcName", $pws, "CISCO-IETF-PW-MPLS-MIB");
  $pws = snmpwalk_cache_oid($device, "cpwVcType", $pws, "CISCO-IETF-PW-MPLS-MIB");
  $pws = snmpwalk_cache_oid($device, "cpwVcPsnType", $pws, "CISCO-IETF-PW-MPLS-MIB");
  $pws = snmpwalk_cache_oid($device, "cpwVcDescr", $pws, "CISCO-IETF-PW-MPLS-MIB");
  $pws = snmpwalk_cache_oid($device, "cpwVcPeerAddrType", $pws, "CISCO-IETF-PW-MPLS-MIB");
  $pws = snmpwalk_cache_oid($device, "cpwVcPeerAddr", $pws, "CISCO-IETF-PW-MPLS-MIB");

  // For MPLS pseudowires
  $pws = snmpwalk_cache_oid($device, "cpwVcMplsPeerLdpID", $pws, "CISCO-IETF-PW-MPLS-MIB");

  foreach ($pws as $pw_id => $pw)
  {
        if ($pw['cpwVcPeerAddrType'] == "ipv4") { $peer_addr = hexStringToIPv4( $pw['cpwVcPeerAddr']); }
        #if(!empty($pw['cpwVcMplsPeerLdpID'])    { list($peer_addr) = explode(":", $pw['cpwVcMplsPeerLdpID']); }

        $cpw_remote_device = @mysql_result(mysql_query("SELECT device_id FROM ipv4_addresses AS A, ports AS I WHERE A.ipv4_address = '".$peer_addr."' AND A.port_id = I.port_id"),0);
        $if_id = @mysql_result(mysql_query("SELECT `port_id` FROM `ports` WHERE `ifDescr` = '".$pw['cpwVcName']."' AND `device_id` = '".$device['device_id']."'"),0);

        if (!empty($device['pws_db'][$pw['cpwVcID']]))
        {
          $pseudowire_id = $device['pws_db'][$pw['cpwVcID']];
          echo(".");
        }
        else
        {
          $pseudowire_id = dbInsert(array('device_id' => $device['device_id'], 'port_id' => $if_id, 'peer_addr' => $peer_addr, 'peer_device_id' => $cpw_remote_device, 'peer_ldp_id' => $cpw_remote_id,
                                          'cpwVcID' => $pw['cpwVcID'], 'cpwOid' => $pw_id, 'pw_type' => $pw['cpwVcType'], 'pw_descr' => $pw['cpwVcDescr'], 'pw_psntype' => $pw['cpwVcPsnType']), 'pseudowires');
          echo("+");
        }
        $device['pws'][$pw['cpwVcID']] = $pseudowire_id;
  }

  // Cycle the list of pseudowires we cached earlier and make sure we saw them again.
  foreach ($device['pws_db'] as $pw_id => $pseudowire_id)
  {
    if (empty($device['pws'][$pw_id]))
    {
      dbDelete('vlans', "`pseudowire_id` = ?", array($pseudowire_id));
    }
  }
  echo("\n");

} # enable_pseudowires + os_group=cisco

?>
