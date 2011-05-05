<?php

$sql   = "SELECT * FROM links AS L, ports AS I WHERE I.device_id = '".$device['device_id']."' AND I.interface_id = L.local_interface_id";
$query = mysql_query($sql);

echo('<table border="0" cellspacing="0" cellpadding="5" width="100%">');

$i = "1";

echo('<tr><th>Local Port</th>
          <th>Remote Port</th>
          <th>Remote Device</th>
          <th>Protocol</th>
      </tr>');

while($neighbour = mysql_fetch_assoc($query))
{

  if ($bg_colour == $list_colour_b) { $bg_colour = $list_colour_a; } else { $bg_colour = $list_colour_b; }

  echo('<tr bgcolor="'.$bg_colour.'">');
  echo('<td><span style="font-weight: bold;">'.generate_port_link($neighbour).'</span><br />'.$neighbour['ifAlias'].'</td>');

  if(is_numeric($neighbour['remote_interface_id']) && $neighbour['remote_interface_id'])  
  {
    $remote_port   = get_port_by_id($neighbour['remote_interface_id']);
    $remote_device = device_by_id_cache($remote_port['device_id']);
    echo("<td>".generate_port_link($remote_port)."<br />".$remote_port['ifAlias']."</td>");
    echo("<td>".generate_device_link($remote_device)."<br />".$remote_device['hardware']."</td>");
  } else {
    echo("<td>".$neighbour['remote_port']."</td>");
    echo("<td>".$neighbour['remote_hostname']."
          <br />".$neighbour['remote_platform']."</td>");
  }
  echo("<td>".$neighbour['protocol']."</td>");
  echo("</tr>");
  $i++;
}

echo("</table>");

?>
