<?
foreach ($ports as $port)
{
  if (is_integer($row/2)) { $row_colour = $list_colour_a; } else { $row_colour = $list_colour_b; }

  $speed = humanspeed($port['ifSpeed']);
  $type = humanmedia($port['ifType']);

  $port['in_rate'] = formatRates($port['ifInOctets_rate'] * 8);
  $port['out_rate'] = formatRates($port['ifOutOctets_rate'] * 8);

  if ($port['in_errors'] > 0 || $port['out_errors'] > 0)
  {
    $error_img = generate_port_link($port,"<img src='images/16/chart_curve_error.png' alt='Interface Errors' border=0>",errors);
  } else { $error_img = ""; }

  if (port_permitted($port['interface_id'], $port['device_id']))
  {
    $port = ifLabel($port, $device);

    $graph_type = "port_" . $subformat;

    $graph_array           = array();
    $graph_array['height'] = "100";
    $graph_array['width']  = "210";
    $graph_array['to']     = $now;
    $graph_array['id']     = $port['interface_id'];
    $graph_array['type']   = $graph_type;
    $graph_array['from']   = $day;
    $graph_array['legend'] = "no";

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width'], $link_array['legend']);
    $link = generate_url($link_array);
    $overlib_content = generate_overlib_content($graph_array, $port['hostname'] . " - " . $port['label']);
    $graph_array['title']  = "yes";
    $graph_array['width'] = 315; $graph_array['height'] = 119;
    $graph =  generate_graph_tag($graph_array);

    echo("<div style='display: block; padding: 1px; margin: 2px; min-width: 393px; max-width:393px; min-height:180px; max-height:180px; text-align: center; float: left; background-color: #f5f5f5;'>");
    echo(overlib_link($link, $graph, $overlib_content));
    echo("</div>");

#    echo("<div style='display: block; padding: 1px; margin: 2px; min-width: 393px; max-width:393px; min-height:180px; max-height:180px; text-align: center; float: left; background-color: #f5f5f5;'>
#    <a href='".generate_port_url($port)."/' onmouseover=\"return overlib('\
#    <div style=\'font-size: 16px; padding:5px; font-weight: bold; color: #e5e5e5;\'>".$device['hostname']." - ".$port['ifDescr']."</div>\
#    <img src=\'graph.php?type=$graph_type&amp;id=".$port['interface_id']."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=450&amp;height=150&amp;title=yes\'>\
#    ', CENTER, LEFT, FGCOLOR, '#e5e5e5', BGCOLOR, '#e5e5e5', WIDTH, 400, HEIGHT, 150);\" onmouseout=\"return nd();\"  >".
#    "<img src='graph.php?type=$graph_type&amp;id=".$port['interface_id']."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=315&amp;height=110&amp;legend=no&amp;title=yes'>
#    </a>
#    </div>");
  }
}
?>
