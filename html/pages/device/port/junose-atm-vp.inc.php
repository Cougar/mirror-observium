<?php

  $row = 0;

  if($_GET['optc']) { $graph_type =  "atmvp_".$_GET['optc']; }
  if(!$graph_type) { $graph_type = "atmvp_bits"; }

  echo("<table cellspacing=0 cellpadding=5 border=0>");

  $vps = mysql_query("SELECT * FROM juniAtmVp WHERE interface_id = '".$interface['interface_id']."'");
  while($vp = mysql_fetch_array($vps)) {
    if(is_integer($row/2)) { $row_colour = $list_colour_a; } else { $row_colour = $list_colour_b; }
    echo('<tr bgcolor="'.$row_colour.'">');
    echo('<td><span class=list-bold>'.$row.'. VP'.$vp['vp_id'].' '.$vp['vp_descr'].'</span></td>');
    echo('</tr>');

    $graph_array['height'] = "100";
    $graph_array['width']  = "215";
    $graph_array['to']     = $now;
    $graph_array['id']     = $vp['juniAtmVp_id'];
    $graph_array['type']   = $graph_type;

    $periods = array('day', 'week', 'month', 'year');

    echo('<tr bgcolor="'.$row_colour.'"><td>');
    foreach($periods as $period) {
      $graph_array['from']     = $$period;
      $graph_array_zoom   = $graph_array; $graph_array_zoom['height'] = "150"; $graph_array_zoom['width'] = "400";
      echo(overlib_link($_SERVER['REQUEST_URI'], generate_graph_tag($graph_array), generate_graph_tag($graph_array_zoom),  NULL));
    }
    echo("</td></tr>");

    $row++;

  }

  echo("</table>");

?>
