<?php

$i = 0;

foreach (explode(",", $id) as $ifid)
{
  $query = mysql_query("SELECT * FROM `ports` AS I, devices as D WHERE I.interface_id = '" . mres($ifid) . "' AND I.device_id = D.device_id");
  $port = mysql_fetch_array($query);
  if (is_file($config['rrd_dir'] . "/" . $port['hostname'] . "/port-" . safename($port['ifIndex'] . ".rrd"))) 
  {
    $rrd_list[$i]['filename'] = $config['rrd_dir'] . "/" . $port['hostname'] . "/port-" . safename($port['ifIndex'] . ".rrd");
    $rrd_list[$i]['descr'] = $port['hostname'] . " " . $port['ifDescr'];
    $i++;
  }
}

$units = 'bps';
$total_units='B';
$colours_in='greens';
$multiplier = "8";
$colours_out = 'blues';

$nototal = 1;
$rra_in  = "INOCTETS";
$rra_out = "OUTOCTETS";

include("includes/graphs/generic_multi_bits_separated.inc.php");

?>