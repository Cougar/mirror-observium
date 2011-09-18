<?php

$graph_type = "storage_usage";

echo("<div style='padding: 5px;'>
        <table width=100% cellspacing=0 cellpadding=6 class='sortable'>");

echo("<tr class=tablehead>
        <th width=280>Device</th>
        <th>Storage</th>
        <th width=100></th>
        <th width=280>Usage</th>
        <th width=50>Used</th>
      </tr>");

$row = 1;

foreach (dbFetchRows("SELECT * FROM `storage` AS S, `devices` AS D WHERE S.device_id = D.device_id ORDER BY D.hostname, S.storage_descr") as $drive)
{
  if (device_permitted($drive['device_id']))
  {
    $skipdrive = 0;

    if ($drive["os"] == "junos")
    {
      foreach ($config['ignore_junos_os_drives'] as $jdrive)
      {
        if (preg_match($jdrive, $drive["storage_descr"]))
        {
          $skipdrive = 1;
        }
      }
      $drive["storage_descr"] = preg_replace("/.*mounted on: (.*)/", "\\1", $drive["storage_descr"]);
    }

    if ($drive['os'] == "freebsd")
    {
      foreach ($config['ignore_bsd_os_drives'] as $jdrive)
      {
        if (preg_match($jdrive, $drive["storage_descr"]))
        {
          $skipdrive = 1;
        }
      }
    }

    if ($skipdrive) { continue; }
    if (is_integer($row/2)) { $row_colour = $list_colour_a; } else { $row_colour = $list_colour_b; }

    $perc  = round($drive['storage_perc'], 0);
    $total = formatStorage($drive['storage_size']);
    $free = formatStorage($drive['storage_free']);
    $used = formatStorage($drive['storage_used']);

    $graph_array['type']        = $graph_type;
    $graph_array['id']          = $drive['storage_id'];
    $graph_array['from']        = $config['time']['day'];
    $graph_array['to']          = $config['time']['now'];
    $graph_array['height']      = "20";
    $graph_array['width']       = "80";
    $graph_array_zoom           = $graph_array;
    $graph_array_zoom['height'] = "150";
    $graph_array_zoom['width']  = "400";
    $link = "graphs/" . $graph_array['id'] . "/" . $graph_array['type'] . "/" . $graph_array['from'] . "/" . $graph_array['to'] . "/";
    $mini_graph = overlib_link($link, generate_graph_tag($graph_array), generate_graph_tag($graph_array_zoom), NULL);

    $background = get_percentage_colours($perc);

    echo("<tr bgcolor='$row_colour'><td>" . generate_device_link($drive) . "</td><td class=tablehead>" . $drive['storage_descr'] . "</td>
         <td>$mini_graph</td>
         <td>
          <a href='#' $store_popup>".print_percentage_bar (400, 20, $perc, "$used / $total", "ffffff", $background['left'], $free, "ffffff", $background['right'])."</a>
          </td><td>$perc"."%</td></tr>");

    if ($vars['view'] == "graphs")
    {

      echo("<tr bgcolor='$row_colour'><td colspan=5>");

      $graph_array['height'] = "100";
      $graph_array['width']  = "216";
      $graph_array['to']     = $config['time']['now'];
      $graph_array['id']     = $drive['storage_id'];
      $graph_array['type']   = $graph_type;

      include("includes/print-quadgraphs.inc.php");

      echo("</td></tr>");

    } # endif graphs

    $row++;
  }
}

echo("</table></div>");

?>
