<?php

$sql   = "SELECT * FROM `ospf_instances` WHERE `device_id` = '".$device['device_id']."'";
$query = mysql_query($sql);

$i_i = "0";

echo('<table width=100% border=0 cellpadding=5>');

#### Loop Instances

while ($instance = mysql_fetch_assoc($query))
{
  if (!is_integer($i_i/2)) { $instance_bg = $list_colour_a; } else { $instance_bg = $list_colour_b; }

  $area_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `ospf_areas` WHERE `device_id` = '".$device['device_id']."'"),0);
  $port_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `ospf_ports` WHERE `device_id` = '".$device['device_id']."'"),0);
  $port_count_enabled = mysql_result(mysql_query("SELECT COUNT(*) FROM `ospf_ports` WHERE `ospfIfAdminStat` = 'enabled' AND `device_id` = '".$device['device_id']."'"),0);
  $nbr_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `ospf_nbrs` WHERE `device_id` = '".$device['device_id']."'"),0);

  $query = "SELECT * FROM ipv4_addresses AS A, ports AS I WHERE ";
  $query .= "(A.ipv4_address = '".$peer['bgpPeerIdentifier']."' AND I.interface_id = A.interface_id)";
  $query .= " AND I.device_id = '".$device['device_id']."'";
  $ipv4_host = mysql_fetch_assoc(mysql_query($query));

  if ($instance['ospfAdminStat'] == "enabled") { $enabled = '<span style="color: #00aa00">enabled</span>'; } else { $enabled = '<span style="color: #aaaaaa">disabled</span>'; }
  if ($instance['ospfAreaBdrRtrStatus'] == "true") { $abr = '<span style="color: #00aa00">yes</span>'; } else { $abr = '<span style="color: #aaaaaa">no</span>'; }
  if ($instance['ospfASBdrRtrStatus'] == "true") { $asbr = '<span style="color: #00aa00">yes</span>'; } else { $asbr = '<span style="color: #aaaaaa">no</span>'; }

  echo('<tr><th>Router Id</th><th>Status</th><th>ABR</th><th>ASBR</th><th>Areas</th><th>Ports</th><th>Neighbours</th></tr>');
  echo('<tr bgcolor="'.$instance_bg.'">');
  echo('  <td class="list-large">'.$instance['ospfRouterId'] . '</td>');
  echo('  <td>' . $enabled . '</td>');
  echo('  <td>' . $abr . '</td>');
  echo('  <td>' . $asbr . '</td>');
  echo('  <td>' . $area_count . '</td>');
  echo('  <td>' . $port_count . '('.$port_count_enabled.')</td>');
  echo('  <td>' . $nbr_count . '</td>');
  echo('</tr>');

  echo('<tr bgcolor="'.$instance_bg.'">');
  echo('<td colspan=7>');
  echo('<table width=100% border=0 cellpadding=5>');
  echo('<tr><th></th><th>Area Id</th><th>Status</th><th>Ports</th></tr>');

  ##### Loop Areas
  $i_a = 0;
  $a_sql   = "SELECT * FROM `ospf_areas` WHERE `device_id` = '".$device['device_id']."'";
  $a_query = mysql_query($a_sql);
  while ($area = mysql_fetch_assoc($a_query))
  {
    if (!is_integer($i_a/2)) { $area_bg = $list_colour_b_a; } else { $area_bg = $list_colour_b_b; }

    $area_port_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `ospf_ports` WHERE `device_id` = '".$device['device_id']."' AND `ospfIfAreaId` = '".$area['ospfAreaId']."'"),0);
    $area_port_count_enabled = mysql_result(mysql_query("SELECT COUNT(*) FROM `ospf_ports` WHERE `ospfIfAdminStat` = 'enabled' AND `device_id` = '".$device['device_id']."' AND `ospfIfAreaId` = '".$area['ospfAreaId']."'"),0);

    echo('<tr bgcolor="'.$area_bg.'">');
    echo('  <td width=5></td>');
    echo('  <td class="list-large">'.$area['ospfAreaId'] . '</td>');
    echo('  <td>' . $enabled . '</td>');
    echo('  <td>' . $area_port_count . '('.$area_port_count_enabled.')</td>');
    echo('</tr>');

    echo('<tr bgcolor="'.$area_bg.'">');
    echo('<td colspan=7>');
    echo('<table width=100% border=0 cellpadding=5>');
    echo('<tr><th></th><th>Port</th><th>Status</th><th>Port Type</th><th>Port State</th></tr>');

    ##### Loop Ports
    $i_p = $i_a + 1;
    $p_sql   = "SELECT * FROM `ospf_ports` AS O, `ports` AS P WHERE O.`ospfIfAdminStat` = 'enabled' AND O.`device_id` = '".$device['device_id']."' AND O.`ospfIfAreaId` = '".$area['ospfAreaId']."' AND P.interface_id = O.interface_id";
    $p_query = mysql_query($p_sql);
    while ($ospfport = mysql_fetch_assoc($p_query))
    {
      if (!is_integer($i_a/2))
      {
        if (!is_integer($i_p/2)) { $port_bg = $list_colour_b_b; } else { $port_bg = $list_colour_b_a; }
      } else {
        if (!is_integer($i_p/2)) { $port_bg = $list_colour_a_b; } else { $port_bg = $list_colour_a_a; }
      }

      if ($ospfport['ospfIfAdminStat'] == "enabled")
      {
        $port_enabled = '<span style="color: #00aa00">enabled</span>';
      } else {
        $port_enabled = '<span style="color: #aaaaaa">disabled</span>';
      }

      echo('<tr bgcolor="'.$port_bg.'">');
      echo('  <td width=15></td>');
      echo('  <td><strong>'. generate_port_link($ospfport) . '</strong></td>');
      echo('  <td>' . $port_enabled . '</td>');
      echo('  <td>' . $ospfport['ospfIfType'] . '</td>');
      echo('  <td>' . $ospfport['ospfIfState'] . '</td>');
      echo('</tr>');

      $i_p++;
    }

    echo('</table>');
    echo('</td>');
    echo('</tr>');

    $i_a++;
  } ### End loop areas

  echo('<tr bgcolor="#ffffff"><th></th><th>Router Id</th><th>Device</th><th>IP Address</th><th>Status</th></tr>');

  ## Loop Neigbours
  $i_n = 1;
  $n_sql   = "SELECT * FROM `ospf_nbrs` WHERE `device_id` = '".$device['device_id']."'";
  $n_query = mysql_query($n_sql);
  while ($nbr = mysql_fetch_assoc($n_query))
  {
    if (!is_integer($i_n/2)) { $nbr_bg = $list_colour_b_a; } else { $nbr_bg = $list_colour_b_b; }

    $host = @mysql_fetch_assoc(mysql_query("SELECT * FROM ipv4_addresses AS A, ports AS I, devices AS D WHERE A.ipv4_address = '".$nbr['ospfNbrRtrId']."'
                                            AND I.interface_id = A.interface_id AND D.device_id = I.device_id"));

    if(is_array($host)) { $rtr_id = generate_dev_link($host, $nbr['ospfNbrRtrId']); } else { $rtr_id = "unknown"; }

    echo('<tr bgcolor="'.$nbr_bg.'">');
    echo('  <td width=5></td>');
    echo('  <td><span class="list-large">' . $nbr['ospfNbrRtrId'] . '</span></td>');
    echo('  <td>' . $rtr_id . '</td>');
    echo('  <td>' . $nbr['ospfNbrIpAddr'] . '</td>');
    echo('  <td>');
    switch ($nbr['ospfNbrState'])
    {
      case 'full':
        echo('<span class=green>'.$nbr['ospfNbrState'].'</span>');
        break;
      case 'down':
        echo('<span class=red>'.$nbr['ospfNbrState'].'</span>');
        break;
      default:
        echo('<span class=blue>'.$nbr['ospfNbrState'].'</span>');
        break;
    }
    echo('</td>');
    echo('</tr>');

    $i_n++;

  }

  echo('</table>');
  echo('</td>');
  echo('</tr>');

  $i_i++;
} ### End loop instances

echo('</table>');

?>
