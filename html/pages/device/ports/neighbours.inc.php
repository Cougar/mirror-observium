<?php

echo('<table class="table table-striped table-condensed">');

$i = "1";

echo('<thead><th>Local Port</th>
          <th>Remote Port</th>
          <th>Remote Device</th>
          <th>Protocol</th>
      </thead>');

foreach (dbFetchRows("SELECT * FROM links AS L, ports AS I WHERE I.device_id = ? AND I.port_id = L.local_port_id", array($device['device_id'])) as $neighbour)
{

  if ($bg_colour == $list_colour_b) { $bg_colour = $list_colour_a; } else { $bg_colour = $list_colour_b; }

  echo('<tr bgcolor="'.$bg_colour.'">');
  echo('<td><span style="font-weight: bold;">'.generate_port_link($neighbour).'</span><br />'.$neighbour['ifAlias'].'</td>');

  if (is_numeric($neighbour['remote_port_id']) && $neighbour['remote_port_id'])
  {
    $remote_port   = get_port_by_id($neighbour['remote_port_id']);
    $remote_device = device_by_id_cache($remote_port['device_id']);
    echo("<td>".generate_port_link($remote_port)."<br />".$remote_port['ifAlias']."</td>");
    echo("<td>".generate_device_link($remote_device)."<br />".$remote_device['hardware']."</td>");
  } else {
    echo("<td>".$neighbour['remote_port']."</td>");
    echo("<td>".$neighbour['remote_hostname']."
          <br />".$neighbour['remote_platform']."</td>");
  }
  echo("<td>".strtoupper($neighbour['protocol'])."</td>");
  echo("</tr>");
  $i++;
}

echo("</table>");

?>
