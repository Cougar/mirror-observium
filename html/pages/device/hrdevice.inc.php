<?php

echo('<table width="100%">');

$hrdevices = mysql_query("SELECT * FROM `hrDevice` WHERE `device_id` = '".$device['device_id']."' ORDER BY `hrDeviceIndex`");

while ($hrdevice = mysql_fetch_assoc($hrdevices))
{
  echo("<tr><td>".$hrdevice['hrDeviceIndex']."</td>");

  if ($hrdevice['hrDeviceType'] == "hrDeviceProcessor")
  {
    $proc_id = mysql_result(mysql_query("SELECT processor_id FROM processors WHERE device_id = '".$device['device_id']."' AND hrDeviceIndex = '".$hrdevice['hrDeviceIndex']."'"),0);
    $proc_url   = "device/".$device['device_id']."/health/processor/";
    $proc_popup  = "onmouseover=\"return overlib('<div class=list-large>".$device['hostname']." - ".$hrdevice['hrDeviceDescr'];
    $proc_popup .= "</div><img src=\'graph.php?id=" . $proc_id . "&amp;type=processor_usage&amp;from=$month&amp;to=$now&amp;width=400&amp;height=125\'>";
    $proc_popup .= "', RIGHT".$config['overlib_defaults'].");\" onmouseout=\"return nd();\"";
    echo("<td><a href='$proc_url' $proc_popup>".$hrdevice['hrDeviceDescr']."</a></td>");

    $graph_array['height'] = "20";
    $graph_array['width']  = "100";
    $graph_array['to']     = $now;
    $graph_array['id']     = $proc_id;
    $graph_array['type']   = 'processor_usage';
    $graph_array['from']     = $day;
    $graph_array_zoom   = $graph_array; $graph_array_zoom['height'] = "150"; $graph_array_zoom['width'] = "400";

    $mini_graph = overlib_link($_SERVER['REQUEST_URI'], generate_graph_tag($graph_array), generate_graph_tag($graph_array_zoom),  NULL);

    echo('<td>'.$mini_graph.'</td>');
  }
  elseif ($hrdevice['hrDeviceType'] == "hrDeviceNetwork")
  {
    $int = str_replace("network interface ", "", $hrdevice['hrDeviceDescr']);
    $interface = mysql_fetch_assoc(mysql_query("SELECT * FROM ports WHERE device_id = '".$device['device_id']."' AND ifDescr = '".$int."'"));
    if ($interface['ifIndex'])
    {
      echo("<td>".generate_port_link($interface)."</td>");

      $graph_array['height'] = "20";
      $graph_array['width']  = "100";
      $graph_array['to']     = $now;
      $graph_array['id']     = $interface['interface_id'];
      $graph_array['type']   = 'port_bits';
      $graph_array['from']   = $day;
      $graph_array_zoom      = $graph_array; $graph_array_zoom['height'] = "150"; $graph_array_zoom['width'] = "400";

      $mini_graph = overlib_link($_SERVER['REQUEST_URI'], generate_graph_tag($graph_array), generate_graph_tag($graph_array_zoom),  NULL);

      echo("<td>$mini_graph</td>");
    } else {
      echo("<td>".$hrdevice['hrDeviceDescr']."</td>");
      echo("<td></td>");
    }
  } else {
    echo("<td>".$hrdevice['hrDeviceDescr']."</td>");
    echo("<td></td>");
  }

  echo("<td>".$hrdevice['hrDeviceType']."</td><td>".$hrdevice['hrDeviceStatus']."</td>");
  echo("<td>".$hrdevice['hrDeviceErrors']."</td><td>".$hrdevice['hrProcessorLoad']."</td>");
  echo("</tr>");
}

echo('</table>');

?>