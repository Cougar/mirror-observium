<?php

$i = 0;

foreach (explode(",", $vars['id']) as $ifid)
{
  $port = dbFetchRow("SELECT * FROM `ports` AS I, devices as D WHERE I.port_id = ? AND I.device_id = D.device_id", array($ifid));
  $rrdfile = get_port_rrdfilename($port, $port);
  if (is_file($rrdfile))
  {
    humanize_port($port);
    $rrd_list[$i]['filename'] = $rrdfile;
    $rrd_list[$i]['descr'] = $port['hostname'] . " " . $port['ifDescr'];
    $rrd_list[$i]['descr_in'] = $port['hostname'];
    $rrd_list[$i]['descr_out'] = makeshortif($port['label']);
    $i++;
  }
}

$units = 'bps';
$total_units='B';
$colours_in='greens';
$multiplier = "8";
$colours_out = 'blues';

#$nototal = 1;

$ds_in  = "INOCTETS";
$ds_out = "OUTOCTETS";

include("includes/graphs/generic_multi_bits_separated.inc.php");

?>
