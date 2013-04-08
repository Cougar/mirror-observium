<?php

  echo('<table class="table table-hover table-striped-two table-bordered table-condensed table-rounded" style="margin-top: 10px;">');
  echo("  <thead>
      <tr>
        <th width='250'><span style='font-weight: bold;' class=interface>Customer</span></th>
        <th width='150'>Device</th>
        <th width='100'>Interface</th>
        <th width='100'>Speed</th>
        <th width='100'>Circuit</th>
        <th>Notes</th>
      </tr>
  </thead>\n");

$pagetitle[] = "Customers";

foreach (dbFetchRows("SELECT * FROM `ports` WHERE `port_descr_type` = 'cust' GROUP BY `port_descr_descr` ORDER BY `port_descr_descr`") as $customer)
{
  $customer_name = $customer['port_descr_descr'];

  foreach (dbFetchRows("SELECT * FROM `ports` WHERE `port_descr_type` = 'cust' AND `port_descr_descr` = ?", array($customer['port_descr_descr'])) as $port)
  {
    $device = device_by_id_cache($port['device_id']);

    unset($class);

    $ifname = fixifname($device['ifDescr']);
    $ifclass = ifclass($port['ifOperStatus'], $port['ifAdminStatus']);

    if ($device['os'] == "ios")
    {
      if ($port['ifTrunk']) { $vlan = "<span class=box-desc><span class=red>" . $port['ifTrunk'] . "</span></span>"; }
      elseif ($port['ifVlan']) { $vlan = "<span class=box-desc><span class=blue>VLAN " . $port['ifVlan'] . "</span></span>"; }
      else { $vlan = ""; }
    }

    echo("
           <tr bgcolor='$bg_colour'>
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

  if ($config['int_customers_graphs'])
  {
    echo("<tr><td colspan=7>");

    $graph_array['type']   = "customer_bits";
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $customer['port_descr_descr'];

    include("includes/print-graphrow.inc.php");

    echo("</tr>");
  }

}

echo("</table>");

?>
