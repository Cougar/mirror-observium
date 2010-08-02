<?php

$sql  = "SELECT * FROM `ports` WHERE `port_descr_type` = 'cust' GROUP BY `port_descr_descr` ORDER BY `port_descr_descr`";
$cust_query = mysql_query($sql);

echo("<table border=0 cellspacing=0 cellpadding=2 class=devicetable width=100%>");

echo("
      <tr bgcolor='$list_colour_a'>
        <th width='7'></th>
        <th width='250'><span style='font-weight: bold;' class=interface>Customer</span></th>
        <th width='150'>Device</th>
        <th width='100'>Interface</th>
        <th width='100'>Speed</th>
        <th width='100'>Circuit</th>
        <th>Notes</th>
      </tr>
     ");

$i = 1;

while($customer = mysql_fetch_array($cust_query)) {

  $i++;
  
  $port_sql = "SELECT * FROM `ports` WHERE `port_descr_type` = 'cust' AND `port_descr_descr` = '".$customer['port_descr_descr']."'";
  $port_query = mysql_query($port_sql);

  $customer_name = $customer['port_descr_descr'];

  if(!is_integer($i/2)) { $bg_colour = $list_colour_a; } else { $bg_colour = $list_colour_b; }

  while($port = mysql_fetch_array($port_query)) {
    $device    = device_by_id_cache($port['device_id']);

    unset($class);

    $ifname = fixifname($device['ifDescr']);

    $ifclass = ifclass($port['ifOperStatus'], $port['ifAdminStatus']);

    if($device['os'] == "ios") {

      if($port['ifTrunk']) { $vlan = "<span class=box-desc><span class=red>" . $port['ifTrunk'] . "</span></span>";
      } elseif ($port['ifVlan']) { $vlan = "<span class=box-desc><span class=blue>VLAN " . $port['ifVlan'] . "</span></span>"; 
      } else { $vlan = ""; }

    }

    echo("
           <tr bgcolor='$bg_colour'>
             <td width='7'></td>
             <td width='250'><span style='font-weight: bold;' class=interface>".$customer_name."</span></td>
             <td width='150'>" . generate_device_link($device) . "</td>
             <td width='100'>" . generate_port_link($port, makeshortif($port['ifDescr'])) . "</td>
             <td width='100'>".$port['port_descr_speed']."</td>
             <td width='100'>".$port['port_descr_circuit']."</td>
             <td>".$port['port_descr_notes']."</td>
           </tr>
         ");


    unset($customer_name);

  }

     echo("<tr bgcolor='$bg_colour'><td></td><td colspan=6>
       <img src='".$config['base_url']."/graph.php?id=".rawurlencode($customer['port_descr_descr'])."&type=customer_bits&from=$day&to=$now&width=215&height=100'>
       <img src='".$config['base_url']."/graph.php?id=".rawurlencode($customer['port_descr_descr'])."&type=customer_bits&from=$week&to=$now&width=215&height=100'>
       <img src='".$config['base_url']."/graph.php?id=".rawurlencode($customer['port_descr_descr'])."&type=customer_bits&from=$month&to=$now&width=215&height=100'>
       <img src='".$config['base_url']."/graph.php?id=".rawurlencode($customer['port_descr_descr'])."&type=customer_bits&from=$year&to=$now&width=215&height=100'>
       </td></tr>");


}

  echo("</table>");

?>
