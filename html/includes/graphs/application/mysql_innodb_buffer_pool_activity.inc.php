<?php

include('includes/graphs/common.inc.php');

$rrd_filename = $config["rrd_dir"] . '/' . $device["hostname"] . '/app-mysql-'.$app["app_id"].'.rrd';

$array = array('IBRd'  => 'Pages Read',
               'IBCd'  => 'Pages Created',
               'IBWr'  => 'Pages Written',
);

$i = 0;
if (is_file($rrd_filename))
{
  foreach ($array as $ds => $vars)
  {
    $rrd_list[$i]['filename'] = $rrd_filename;
    if (is_array($vars))
    {
      $rrd_list[$i]['descr'] = $vars['descr'];
    } else {
      $rrd_list[$i]['descr'] = $vars;
    }
    $rrd_list[$i]['ds'] = $ds;
    $i++;
  }
} else { echo("file missing: $file");  }

$colours   = "mixed";
$nototal   = 1;
$unit_text = "activity";

include("includes/graphs/generic_multi_simplex_seperated.inc.php");

?>
