<?php

$sql   = "SELECT * FROM `ospf_instances` WHERE `ospfAdminStat` = 'enabled'";
$query = mysql_query($sql);

$i_i = "0";

echo('<table width=100% border=0 cellpadding=10>');
echo('<tr><th>Device</th><th>Router Id</th><th>Status</th><th>ABR</th><th>ASBR</th><th>Areas</th><th>Ports</th><th>Neighbours</th></tr>');

#### Loop Instances

while ($instance = mysql_fetch_assoc($query))
{
  if (!is_integer($i_i/2)) { $instance_bg = $list_colour_a; } else { $instance_bg = $list_colour_b; }

  $device = device_by_id_cache($instance['device_id']);

  $area_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `ospf_areas` WHERE `device_id` = '".$device['device_id']."'"),0);
  $port_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `ospf_ports` WHERE `device_id` = '".$device['device_id']."'"),0);
  $port_count_enabled = mysql_result(mysql_query("SELECT COUNT(*) FROM `ospf_ports` WHERE `ospfIfAdminStat` = 'enabled' AND `device_id` = '".$device['device_id']."'"),0);

  $ip_query = "SELECT * FROM ipv4_addresses AS A, ports AS I WHERE ";
  $ip_query .= "(A.ipv4_address = '".$peer['bgpPeerIdentifier']."' AND I.interface_id = A.interface_id)";
  $ip_query .= " AND I.device_id = '".$device['device_id']."'";
  $ipv4_host = mysql_fetch_assoc(mysql_query($ip_query));

  if ($instance['ospfAdminStat'] == "enabled") { $enabled = '<span style="color: #00aa00">enabled</span>'; } else { $enabled = '<span style="color: #aaaaaa">disabled</span>'; }
  if ($instance['ospfAreaBdrRtrStatus'] == "true") { $abr = '<span style="color: #00aa00">yes</span>'; } else { $abr = '<span style="color: #aaaaaa">no</span>'; }
  if ($instance['ospfASBdrRtrStatus'] == "true") { $asbr = '<span style="color: #00aa00">yes</span>'; } else { $asbr = '<span style="color: #aaaaaa">no</span>'; }

  echo('<tr bgcolor="'.$instance_bg.'">');
  echo('  <td class="list-large">'.generate_device_link($device, 0, "routing/ospf/"). '</td>');
  echo('  <td class="list-large">'.$instance['ospfRouterId'] . '</td>');
  echo('  <td>' . $enabled . '</td>');
  echo('  <td>' . $abr . '</td>');
  echo('  <td>' . $asbr . '</td>');
  echo('  <td>' . $area_count . '</td>');
  echo('  <td>' . $port_count . '('.$port_count_enabled.')</td>');
  echo('  <td>' . ($neighbour_count+0) . '</td>');
  echo('</tr>');

  $i_i++;
} ### End loop instances

echo('</table>');

?>
