<?php

if ($_GET['optb'] == "purge" && $_GET['optc'] == "all")
{
  foreach (dbFetchRows("SELECT * FROM `ports` AS P, `devices` as D WHERE P.`deleted` = '1' AND D.device_id = P.device_id") as $interface)
  {
    if (port_permitted($interface['interface_id'], $interface['device_id']))
    {
      delete_port($interface['interface_id']);
      echo("<div class=infobox>Deleted ".generate_device_link($interface)." - ".generate_port_link($interface)."</div>");
    }
  }
} elseif ($_GET['optb'] == "purge" && $_GET['optc']) {
  $interface = dbFetchRow("SELECT * from `ports` AS P, `devices` AS D WHERE `interface_id` = ? AND D.device_id = P.device_id", array($_GET['optc']));
  if (port_permitted($interface['interface_id'], $interface['device_id']))
  delete_port($interface['interface_id']);
  echo("<div class=infobox>Deleted ".generate_device_link($interface)." - ".generate_port_link($interface)."</div>");
}

$i_deleted = 1;

echo("<table cellpadding=5 cellspacing=0 border=0 width=100%>");
echo("<tr><td></td><td></td><td></td><td><a href='".$config['base_url'] . "/ports/deleted/purge/all/'><img src='images/16/cross.png' align=absmiddle></img> Purge All</a></td></tr>");

foreach (dbFetchRows("SELECT * FROM `ports` AS P, `devices` as D WHERE P.`deleted` = '1' AND D.device_id = P.device_id") as $interface)
{
  $interface = ifLabel($interface, $interface);
  if (port_permitted($interface['interface_id'], $interface['device_id']))
  {
    if (is_integer($i_deleted/2)) { $row_colour = $list_colour_a; } else { $row_colour = $list_colour_b; }

    echo("<tr bgcolor=$row_colour>");
    echo("<td width=250>".generate_device_link($interface)."</td>");
    echo("<td width=250>".generate_port_link($interface)."</td>");
    echo("<td></td>");
    echo("<td width=100><a href='".$config['base_url'] . "/ports/deleted/purge/".$interface['interface_id']."/'><img src='images/16/cross.png' align=absmiddle></img> Purge</a></td>");

    $i_deleted++;
  }
}

echo("</table>");

?>
