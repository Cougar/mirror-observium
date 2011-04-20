<?php

$scale_min = "0";

include("includes/graphs/common.inc.php");

$rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/power-" . safename($sensor['sensor_type']."-".$sensor['sensor_index']) . ".rrd";

$rrd_options .= " -A ";
$rrd_options .= " COMMENT:'                           Last    Max\\n'";

$sensor['sensor_descr_fixed'] = substr(str_pad($sensor['sensor_descr'], 22),0,22);
$sensor['sensor_descr_fixed'] = str_replace(':','\:',str_replace('\*','*',$sensor['sensor_descr_fixed']));

$rrd_options .= " DEF:sensor=$rrd_filename:sensor:AVERAGE";
$rrd_options .= " DEF:sensor_max=$rrd_filename:sensor:MAX";
$rrd_options .= " DEF:sensor_min=$rrd_filename:sensor:MIN";

$rrd_options .= " AREA:sensor_max#c5c5c5";
$rrd_options .= " AREA:sensor_min#ffffffff";

#$rrd_options .= " AREA:sensor#FFFF99";
$rrd_options .= " LINE1.5:sensor#cc0000:'" . $sensor['sensor_descr_fixed']."'";
$rrd_options .= " GPRINT:sensor:LAST:%6.2lfW";
$rrd_options .= " GPRINT:sensor:MAX:%6.2lfW\\\\l";

if (is_numeric($sensor['sensor_limit'])) $rrd_options .= " HRULE:".$sensor['sensor_limit']."#999999::dashes";
if (is_numeric($sensor['sensor_limit_low'])) $rrd_options .= " HRULE:".$sensor['sensor_limit_low']."#999999::dashes";

?>
