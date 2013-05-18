<?php

$graph_type = "toner_usage";

$toners = dbFetchRows("SELECT * FROM `toner` WHERE device_id = ?", array($device['device_id']));

if (count($toners))
{
?>

   <div class="well info_box">
      <div id="title"><i class="oicon-drive"></i> Storage</div>
      <div id="content">

<?php
  echo('<table class="table table-condensed-more table-striped">');

  foreach ($toners as $toner)
  {
    $percent  = round($toner['toner_current'], 0);
    $total = formatStorage($toner['toner_size']);
    $free = formatStorage($toner['toner_free']);
    $used = formatStorage($toner['toner_used']);

    $background = toner2colour($toner['toner_descr'], $percent);

    $graph_array           = array();
    $graph_array['height'] = "100";
    $graph_array['width']  = "210";
    $graph_array['to']     = $config['time']['now'];
    $graph_array['id']     = $toner['toner_id'];
    $graph_array['type']   = $graph_type;
    $graph_array['from']   = $config['time']['day'];
    $graph_array['legend'] = "no";

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width'], $link_array['legend']);
    $link = generate_url($link_array);

    $overlib_content = generate_overlib_content($graph_array, $device['hostname'] . " - " . $toner['toner_descr']);

    $graph_array['width'] = 80; $graph_array['height'] = 20; $graph_array['bg'] = 'ffffff00'; # the 00 at the end makes the area transparent.

    $minigraph =  generate_graph_tag($graph_array);

    echo("<tr class=device-overview>
               <td class=strong>".overlib_link($link, $toner['toner_descr'], $overlib_content)."</td>
           <td width=90>".overlib_link($link, $minigraph, $overlib_content)."</td>
           <td width=200>".overlib_link($link, print_percentage_bar (200, 20, $percent, NULL, "ffffff", $background['left'], $percent . "%", "ffffff", $background['right']), $overlib_content)."
           </a></td>
         </tr>");
  }

  echo("</table>");
  echo("</div></div>");
}

unset ($toner_rows);

?>
