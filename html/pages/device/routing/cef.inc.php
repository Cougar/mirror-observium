<?php

print_optionbar_start();

$menu_options = array('basic' => 'Basic',
		      'graphs' => 'Graphs',
                      );

if (!$_GET['optb']) { $_GET['opta'] = "basic"; }

echo('<span style="font-weight: bold;">CEF</span> &#187; ');

$sep = "";
foreach ($menu_options as $option => $text)
{
  echo($sep);
  if ($_GET['optb'] == $option) { echo("<span class='pagemenu-selected'>"); }
  echo('<a href="device/' . $device['device_id'] . '/routing/cef/' . $option . ($_GET['optb'] ? '/' . $_GET['optb'] : ''). '/">' . $text
 . '</a>');
  if ($_GET['optb'] == $option) { echo("</span>"); }
  $sep = " | ";
}

unset($sep);

print_optionbar_end();


echo('<div id="content">
        <table  border="0" cellspacing="0" cellpadding="5" width="100%">');

$cef_query = mysql_query("SELECT * FROM `cef_switching` WHERE `device_id` = '".$device['device_id']."' ORDER BY `entPhysicalIndex`, `afi`, `cef_index`");

echo('<tr><th><a title="Physical hardware entity">Entity</a></th>
          <th><a title="Address Family">AFI</a></th>
          <th><a title="CEF Switching Path">Path</a></th>
          <th><a title="Number of packets dropped.">Drop</a></th>
          <th><a title="Number of packets that could not be switched in the normal path and were punted to the next-fastest switching vector.">Punt</a></th>
          <th><a title="Number of packets that could not be switched in the normal path and were punted to the host.<br />For switch paths other than a centralized turbo switch path, punt and punt2host function the same way. With punt2host from a centralized turbo switch path (PAS and RSP), punt will punt the packet to LES, but punt2host will bypass LES and punt directly to process switching.">Punt2Host</a></th>
      </tr>');

$i=0;

while ($cef = mysql_fetch_assoc($cef_query))
{

  $entity_query = mysql_query("SELECT * FROM `entPhysical` WHERE device_id = '".$device['device_id']."' AND `entPhysicalIndex` = '".$cef['entPhysicalIndex']."'");
  $entity = mysql_fetch_assoc($entity_query);

  if (!is_integer($i/2)) { $bg_colour = $list_colour_a; } else { $bg_colour = $list_colour_b; }

  $interval = $cef['updated'] - $cef['updated_prev'];

  if(!$entity['entPhysicalModelName'] && $entity['entPhysicalContainedIn']) 
  {
    $parent_entity_query = mysql_query("SELECT * FROM `entPhysical` WHERE device_id = '".$device['device_id']."' AND `entPhysicalIndex` = '".$entity['entPhysicalContainedIn']."'");
    $parent_entity = mysql_fetch_assoc($parent_entity_query); 
    $entity_descr = $entity['entPhysicalName'] . " (" . $parent_entity['entPhysicalModelName'] .")";
  } else {
    $entity_descr = $entity['entPhysicalName'] . " (" . $entity['entPhysicalModelName'] .")";
  }


  echo("<tr bgcolor=$bg_colour><td>".$entity_descr."</td>
            <td>".$cef['afi']."</td>
            <td>");

  switch ($cef['cef_path']) {
    case "RP RIB":
      echo '<a title="Process switching with Cisco Express Forwarding assistance.">RP RIB</a>';
      break;
    case "RP LES":
      echo '<a title="Low-end switching. Centralized Cisco Express Forwarding switch path.">RP LES</a>';
      break;
    case "RP PAS":
      echo '<a title="Cisco Express Forwarding turbo switch path.">RP PAS</a>';
      break;
    default:
       echo $cef['cef_path'];
  }

  echo("</td>");
  echo("<td>".format_si($cef['drop']));
  if($cef['drop'] > $cef['drop_prev']) { echo(" <span style='color:red;'>(".round(($cef['drop']-$cef['drop_prev'])/$interval,2)."/sec)</span>"); }
  echo("</td>");
  echo("<td>".format_si($cef['punt']));
  if($cef['punt'] > $cef['punt_prev']) { echo(" <span style='color:red;'>(".round(($cef['punt']-$cef['punt_prev'])/$interval,2)."/sec)</span>"); }
  echo("</td>");
  echo("<td>".format_si($cef['punt']));
  if($cef['punt2host'] > $cef['punt2host_prev']) { echo(" <span style='color:red;'>(".round(($cef['punt2host']-$cef['punt2host_prev'])/$interval,2)."/sec)</span>"); }
  echo("</td>");

        echo("</tr>
       ");

  if($_GET['opta'] == "graphs")
  {
    $graph_array['height'] = "100";
    $graph_array['width']  = "215";
    $graph_array['to']     = $now;
    $graph_array['id']     = $cef['cef_switching_id'];
    $graph_array['type']   = "cefswitching_graph";

    echo("<tr bgcolor='$bg_colour'><td colspan=6>");

    include("includes/print-quadgraphs.inc.php");

    echo("</td></tr>");
  }


  $i++;
}

echo("</table></div>");

?>

<script class="content_tooltips" type="text/javascript"> 
$(document).ready(function() { $('#content a[title]').qtip({ content: { text: false }, style: 'light' }); });
</script> 
