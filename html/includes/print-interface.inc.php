<?php

#  This file prints a table row for each interface

$port['device_id'] = $device['device_id'];
$port['hostname'] = $device['hostname'];

$if_id = $port['interface_id'];

$port = ifLabel($port);

if($int_colour)
{
  $row_colour = $int_colour;
} else {
  if (!is_integer($i/2)) { $row_colour = $list_colour_a; } else { $row_colour = $list_colour_b; }
}

$port_adsl = dbFetchRow("SELECT * FROM `ports_adsl` WHERE `interface_id` = ?", array($port['interface_id']));

if ($port['ifInErrors_delta'] > 0 || $port['ifOutErrors_delta'] > 0)
{
  $error_img = generate_port_link($port, "<img src='images/16/chart_curve_error.png' alt='Interface Errors' border=0>", "port_errors");
} else { $error_img = ""; }

if (dbFetchCell("SELECT COUNT(*) FROM `mac_accounting` WHERE `interface_id` = ?", array($port['interface_id'])))
{
  $mac = "<a href='" . generate_port_url($port, array('view' => 'macaccounting')) . "'><img src='/images/16/chart_curve.png' align='absmiddle'></a>";
} else { $mac = ""; }

echo("<tr style=\"background-color: $row_colour;\" valign=top onmouseover=\"this.style.backgroundColor='$list_highlight';\" onmouseout=\"this.style.backgroundColor='$row_colour';\" onclick=\"location.href='" . generate_port_url($port) . "/'\" style='cursor: pointer;'>
         <td valign=top width=350>");
echo("        <span class=list-large>
              " . generate_port_link($port, $port['ifIndex'] . ". ".$port['label']) . " $error_img $mac
           </span><br /><span class=interface-desc>".$port['ifAlias']."</span>");

if ($port['ifAlias']) { echo("<br />"); }

unset ($break);

if ($port_details)
{
  foreach (dbFetchRows("SELECT * FROM `ipv4_addresses` WHERE `interface_id` = ?", array($port['interface_id'])) as $ip)
  {
    echo("$break <a class=interface-desc href=\"javascript:popUp('/netcmd.php?cmd=whois&amp;query=$ip[ipv4_address]')\">".$ip['ipv4_address']."/".$ip['ipv4_prefixlen']."</a>");
    $break = "<br />";
  }
  foreach (dbFetchRows("SELECT * FROM `ipv6_addresses` WHERE `interface_id` = ?", array($port['interface_id'])) as $ip6)
  {
    echo("$break <a class=interface-desc href=\"javascript:popUp('/netcmd.php?cmd=whois&amp;query=".$ip6['ipv6_address']."')\">".Net_IPv6::compress($ip6['ipv6_address'])."/".$ip6['ipv6_prefixlen']."</a>");
    $break = "<br />";
  }
}

echo("</span>");

echo("</td><td width=100>");

if ($port_details)
{
  $port['graph_type'] = "port_bits";
  echo(generate_port_link($port, "<img src='graph.php?type=port_bits&amp;id=".$port['interface_id']."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=100&amp;height=20&amp;legend=no&amp;bg=".str_replace("#","", $row_colour)."'>"));
  $port['graph_type'] = "port_upkts";
  echo(generate_port_link($port, "<img src='graph.php?type=port_upkts&amp;id=".$port['interface_id']."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=100&amp;height=20&amp;legend=no&amp;bg=".str_replace("#","", $row_colour)."'>"));
  $port['graph_type'] = "port_errors";
  echo(generate_port_link($port, "<img src='graph.php?type=port_errors&amp;id=".$port['interface_id']."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=100&amp;height=20&amp;legend=no&amp;bg=".str_replace("#","", $row_colour)."'>"));
}

echo("</td><td width=120>");

if ($port['ifOperStatus'] == "up")
{
  $port['in_rate'] = $port['ifInOctets_rate'] * 8;
  $port['out_rate'] = $port['ifOutOctets_rate'] * 8;
  $in_perc = @round($port['in_rate']/$port['ifSpeed']*100);
  $out_perc = @round($port['in_rate']/$port['ifSpeed']*100);
  echo("<img src='images/16/arrow_left.png' align=absmiddle> <span style='color: " . percent_colour($in_perc) . "'>".formatRates($port['in_rate'])."<br />
        <img align=absmiddle src='images/16/arrow_out.png'> <span style='color: " . percent_colour($out_perc) . "'>".formatRates($port['out_rate']) . "<br />
        <img src='images/icons/arrow_pps_in.png' align=absmiddle> ".format_bi($port['ifInUcastPkts_rate'])."pps</span><br />
        <img src='images/icons/arrow_pps_out.png' align=absmiddle> ".format_bi($port['ifOutUcastPkts_rate'])."pps</span>");
}

echo("</td><td width=75>");
if ($port['ifSpeed']) { echo("<span class=box-desc>".humanspeed($port['ifSpeed'])."</span>"); }
echo("<br />");

if ($port[ifDuplex] != "unknown") { echo("<span class=box-desc>" . $port['ifDuplex'] . "</span>"); } else { echo("-"); }

if ($device['os'] == "ios" || $device['os'] == "iosxe")
{
  if ($port['ifTrunk']) {
    echo("<span class=box-desc><span class=red>" . $port['ifTrunk'] . "</span></span>");
  } elseif ($port['ifVlan']) {
    echo("<span class=box-desc><span class=blue>VLAN " . $port['ifVlan'] . "</span></span>");
  } elseif ($port['ifVrf']) {
    $vrf = dbFetchRow("SELECT * FROM vrfs WHERE vrf_id = ?", array($port['ifVrf']));
    echo("<span style='color: green;'>" . $vrf['vrf_name'] . "</span>");
  }
}

if ($port_adsl['adslLineCoding'])
{
  echo("</td><td width=150>");
  echo($port_adsl['adslLineCoding']."/" . rewrite_adslLineType($port_adsl['adslLineType']));
  echo("<br />");
  echo("Sync:".formatRates($port_adsl['adslAtucChanCurrTxRate']) . "/". formatRates($port_adsl['adslAturChanCurrTxRate']));
  echo("<br />");
  echo("Max:".formatRates($port_adsl['adslAtucCurrAttainableRate']) . "/". formatRates($port_adsl['adslAturCurrAttainableRate']));
  echo("</td><td width=150>");
  echo("Atten:".$port_adsl['adslAtucCurrAtn'] . "dB/". $port_adsl['adslAturCurrAtn'] . "dB");
  echo("<br />");
  echo("SNR:".$port_adsl['adslAtucCurrSnrMgn'] . "dB/". $port_adsl['adslAturCurrSnrMgn']. "dB");
} else {
  echo("</td><td width=150>");
  if ($port['ifType'] && $port['ifType'] != "") { echo("<span class=box-desc>" . fixiftype($port['ifType']) . "</span>"); } else { echo("-"); }
  echo("<br />");
  if ($ifHardType && $ifHardType != "") { echo("<span class=box-desc>" . $ifHardType . "</span>"); } else { echo("-"); }
  echo("</td><td width=150>");
  if ($port['ifPhysAddress'] && $port['ifPhysAddress'] != "") { echo("<span class=box-desc>" . formatMac($port['ifPhysAddress']) . "</span>"); } else { echo("-"); }
  echo("<br />");
  if ($port['ifMtu'] && $port['ifMtu'] != "") { echo("<span class=box-desc>MTU " . $port['ifMtu'] . "</span>"); } else { echo("-"); }
}

echo("</td>");
echo("<td width=375 valign=top class=interface-desc>");
if (strpos($port['label'], "oopback") === false && !$graph_type)
{
  foreach(dbFetchRows("SELECT * FROM `links` AS L, `ports` AS I, `devices` AS D WHERE L.local_interface_id = ? AND L.remote_interface_id = I.interface_id AND I.device_id = D.device_id", array($if_id)) as $link)
  {
#         echo("<img src='images/16/connect.png' align=absmiddle alt='Directly Connected' /> " . generate_port_link($link, makeshortif($link['label'])) . " on " . generate_device_link($link, shorthost($link['hostname'])) . "</a><br />");
#         $br = "<br />";
     $int_links[$link['interface_id']] = $link['interface_id'];
     $int_links_phys[$link['interface_id']] = 1;
  }

  unset($br);

  if ($port_details)
  { ## Show which other devices are on the same subnet as this interface
    foreach (dbFetchRows("SELECT `ipv4_network_id` FROM `ipv4_addresses` WHERE `interface_id` = ? AND `ipv4_address` NOT LIKE '127.%'", array($port['interface_id'])) as $net)
    {
      $ipv4_network_id = $net['ipv4_network_id'];
      $sql = "SELECT I.interface_id FROM ipv4_addresses AS A, ports AS I, devices AS D
           WHERE A.interface_id = I.interface_id
           AND A.ipv4_network_id = ? AND D.device_id = I.device_id
           AND D.device_id != ?";
      $array = array($net['ipv4_network_id'], $device['device_id']);
      foreach(dbFetchRows($sql, $array) AS $new)
      {
        echo($new['ipv4_network_id']);
        $this_ifid = $new['interface_id'];
        $this_hostid = $new['device_id'];
        $this_hostname = $new['hostname'];
        $this_ifname = fixifName($new['label']);
        $int_links[$this_ifid] = $this_ifid;
        $int_links_v4[$this_ifid] = 1;
      }
    }

    foreach (dbFetchRows("SELECT ipv6_network_id FROM ipv6_addresses WHERE interface_id = ?", array($port['interface_id'])) as $net)
    {
      $ipv6_network_id = $net['ipv6_network_id'];
      $sql = "SELECT I.interface_id FROM ipv6_addresses AS A, ports AS I, devices AS D
           WHERE A.interface_id = I.interface_id
           AND A.ipv6_network_id = ? AND D.device_id = I.device_id
           AND D.device_id != ? AND A.ipv6_origin != 'linklayer' AND A.ipv6_origin != 'wellknown'";
      $array = array($net['ipv6_network_id'], $device['device_id']);

      foreach(dbFetchRows($sql, $array) AS $new)
      {
        echo($new['ipv6_network_id']);
          $this_ifid = $new['interface_id'];
          $this_hostid = $new['device_id'];
          $this_hostname = $new['hostname'];
          $this_ifname = fixifName($new['label']);
          $int_links[$this_ifid] = $this_ifid;
          $int_links_v6[$this_ifid] = 1;
      }
    }
  }

  foreach ($int_links as $int_link)
  {
    $link_if = dbFetchRow("SELECT * from ports AS I, devices AS D WHERE I.device_id = D.device_id and I.interface_id = ?", array($int_link));

    echo("$br");

    if ($int_links_phys[$int_link]) { echo("<img align=absmiddle src='images/16/connect.png'> "); } else {
                                      echo("<img align=absmiddle src='images/16/bullet_go.png'> "); }

    echo("<b>" . generate_port_link($link_if, makeshortif($link_if['label'])) . " on " . generate_device_link($link_if, shorthost($link_if['hostname'])));

    if ($int_links_v6[$int_link]) { echo(" <b style='color: #a10000;'>v6</b>"); }
    if ($int_links_v4[$int_link]) { echo(" <b style='color: #00a100'>v4</b>"); }
    $br = "<br />";
  }
#     unset($int_links, $int_links_v6, $int_links_v4, $int_links_phys, $br);
}

foreach (dbFetchRows("SELECT * FROM `pseudowires` WHERE `interface_id` = ?", array($port['interface_id'])) as $pseudowire)
{
#`interface_id`,`peer_device_id`,`peer_ldp_id`,`cpwVcID`,`cpwOid`
  $pw_peer_dev = dbFetchRow("SELECT * FROM `devices` WHERE `device_id` = ?", array($pseudowire['peer_device_id']));
  $pw_peer_int = dbFetchRow("SELECT * FROM `ports` AS I, pseudowires AS P WHERE I.device_id = ? AND P.cpwVcID = ? AND P.interface_id = I.interface_id", array($pseudowire['peer_device_id'], $pseudowire['cpwVcID']));

  $pw_peer_int = ifNameDescr($pw_peer_int);
  echo("$br<img src='images/16/arrow_switch.png' align=absmiddle><b> " . generate_port_link($pw_peer_int, makeshortif($pw_peer_int['label'])) ." on ". generate_device_link($pw_peer_dev, shorthost($pw_peer_dev['hostname'])) . "</b>");
  $br = "<br />";
}

foreach(dbFetchRows("SELECT * FROM `ports` WHERE `pagpGroupIfIndex` = ? and `device_id` = ?", array($port['ifIndex'], $device['device_id'])) as $member)
{
  echo("$br<img src='images/16/brick_link.png' align=absmiddle> <strong>" . generate_port_link($member) . " (PAgP)</strong>");
  $br = "<br />";
}

if ($port['pagpGroupIfIndex'] && $port['pagpGroupIfIndex'] != $port['ifIndex'])
{
  $parent = dbFetchRow("SELECT * FROM `ports` WHERE `ifIndex` = ? and `device_id` = ?", array($port['pagpGroupIfIndex'], $device['device_id']));
  echo("$br<img src='images/16/bricks.png' align=absmiddle> <strong>" . generate_port_link($parent) . " (PAgP)</strong>");
  $br = "<br />";
}

foreach(dbFetchRows("SELECT * FROM `ports_stack` WHERE `interface_id_low` = ? and `device_id` = ?", array($port['ifIndex'], $device['device_id'])) as $higher_if)
{
  if ($higher_if['interface_id_high'])
  {
    $this_port = get_port_by_index_cache($device['device_id'], $higher_if['interface_id_high']);
    echo("$br<img src='images/16/arrow_divide.png' align=absmiddle> <strong>" . generate_port_link($this_port) . "</strong>");
    $br = "<br />";
  }
}

foreach(dbFetchRows("SELECT * FROM `ports_stack` WHERE `interface_id_high` = ? and `device_id` = ?", array($port['ifIndex'], $device['device_id'])) as $lower_if)
{
  if ($lower_if['interface_id_low'])
  {
    $this_port = get_port_by_index_cache($device['device_id'], $lower_if['interface_id_low']);
    echo("$br<img src='images/16/arrow_join.png' align=absmiddle> <strong>" . generate_port_link($this_port) . "</strong>");
    $br = "<br />";
  }
}



unset($int_links, $int_links_v6, $int_links_v4, $int_links_phys, $br);

echo("</td></tr>");

// If we're showing graphs, generate the graph and print the img tags

if ($graph_type == "etherlike")
{
  $graph_file = $config['rrd_dir'] . "/" . $device['hostname'] . "/port-". safename($port['ifIndex']) . "-dot3.rrd";
} else {
  $graph_file = $config['rrd_dir'] . "/" . $device['hostname'] . "/port-". safename($port['ifIndex']) . ".rrd";
}

if ($graph_type && is_file($graph_file))
{
  $type = $graph_type;

  echo("<tr style='background-color: $row_colour; padding: 0px;'><td colspan=7>");

  include("includes/print-interface-graphs.inc.php");

  echo("</td></tr>");
}

?>
