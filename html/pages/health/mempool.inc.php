<?php

$graph_type = "mempool_usage";

echo("<div style='margin-top: 5px; padding: 0px;'>");
echo("<table width=100% cellpadding=6 cellspacing=0>");

echo("<tr class=tablehead>
        <th width=280>Device</th>
        <th>Memory</th>
        <th width=100></th>
        <th width=280>Usage</th>
        <th width=50>Used</th>
      </tr>");

$sql  = "SELECT *, `mempools`.`mempool_id` AS `mempool_id`";
$sql .= " FROM  `mempools`";
$sql .= " JOIN `devices` ON  `mempools`.`device_id` =  `devices`.`device_id`";
$sql .= " LEFT JOIN  `mempools-state` ON  `mempools`.mempool_id =  `mempools-state`.mempool_id";
$sql .= " ORDER BY `devices`.`hostname`";

foreach (dbFetchRows($sql) as $mempool)
{
  if (device_permitted($mempool['device_id']))
  {
    $text_descr = $mempool['mempool_descr'];

    $mempool_url = "device/device=".$mempool['device_id']."/tab=health/metric=mempool/";
    $mini_url = "graph.php?id=".$mempool['mempool_id']."&amp;type=".$graph_type."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=80&amp;height=20&amp;bg=f4f4f4";

    $mempool_popup  = "onmouseover=\"return overlib('<div class=list-large>".$device['hostname']." - ".$text_descr;
    $mempool_popup .= "</div><img src=\'graph.php?id=" . $mempool['mempool_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=125\'>";
    $mempool_popup .= "', RIGHT".$config['overlib_defaults'].");\" onmouseout=\"return nd();\"";

    $total = formatStorage($mempool['mempool_total']);
    $used = formatStorage($mempool['mempool_used']);
    $free = formatStorage($mempool['mempool_free']);

    $background = get_percentage_colours($mempool['mempool_perc']);

    echo("<tr class='health'>
           <td>".generate_device_link($mempool)."</td>
           <td class=tablehead><a href='".$mempool_url."' $mempool_popup>" . $text_descr . "</a></td>
           <td width=90><a href='".$mempool_url."'  $mempool_popup><img src='$mini_url'></a></td>
           <td width=200><a href='".$mempool_url."' $mempool_popup>
           ".print_percentage_bar (400, 20, $mempool['mempool_perc'], "$used / $total", "ffffff", $background['left'], $free , "ffffff", $background['right'])."
            </a></td>
            <td width=50>".$mempool['mempool_perc']."%</td>
         </tr>");

    if ($vars['view'] == "graphs")
    {
      echo("<tr></tr><tr class='health'><td colspan=5>");

      $daily_graph   = "graph.php?id=" . $mempool['mempool_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
      $daily_url       = "graph.php?id=" . $mempool['mempool_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['day']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

      $weekly_graph  = "graph.php?id=" . $mempool['mempool_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['week']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
      $weekly_url      = "graph.php?id=" . $mempool['mempool_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['week']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

      $monthly_graph = "graph.php?id=" . $mempool['mempool_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
      $monthly_url     = "graph.php?id=" . $mempool['mempool_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['month']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

      $yearly_graph  = "graph.php?id=" . $mempool['mempool_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['yearh']."&amp;to=".$config['time']['now']."&amp;width=211&amp;height=100";
      $yearly_url  = "graph.php?id=" . $mempool['mempool_id'] . "&amp;type=".$graph_type."&amp;from=".$config['time']['yearh']."&amp;to=".$config['time']['now']."&amp;width=400&amp;height=150";

      echo("<a onmouseover=\"return overlib('<img src=\'$daily_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$daily_graph' border=0></a> ");
      echo("<a onmouseover=\"return overlib('<img src=\'$weekly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$weekly_graph' border=0></a> ");
      echo("<a onmouseover=\"return overlib('<img src=\'$monthly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$monthly_graph' border=0></a> ");
      echo("<a onmouseover=\"return overlib('<img src=\'$yearly_url\'>', LEFT);\" onmouseout=\"return nd();\">
        <img src='$yearly_graph' border=0></a>");
      echo("</td></tr>");
    } # endif graphs
  }
}

echo("</table>");
echo("</div>");

?>
