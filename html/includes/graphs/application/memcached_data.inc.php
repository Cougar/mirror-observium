<?php

include("memcached.inc.php");
include("includes/graphs/common.inc.php");

$scale_min    = 0;
$colours      = "mixed";
$nototal      = 0;
$unit_text    = "Packets/sec";
$array        = array(
                      'limit_maxbytes' => array('descr' => 'Capacity', 'colour' => '555555'),
                      'bytes' => array('descr' => 'Used', 'colour' => 'cc0000', 'areacolour' => 'ff999955'),
                     );

$i            = 0;

if (is_file($rrd_filename))
{
  foreach ($array as $ds => $vars)
  {
    $rrd_list[$i]['filename'] = $rrd_filename;
    $rrd_list[$i]['descr']    = $vars['descr'];
    $rrd_list[$i]['ds']       = $ds;
    $rrd_list[$i]['colour']   = $vars['colour'];
    if (!empty($vars['areacolour'])) { $rrd_list[$i]['areacolour']   = $vars['areacolour']; }
    $i++;
  }
} else {
  echo("file missing: $file");
}

include("includes/graphs/generic_multi_line.inc.php");

?>
