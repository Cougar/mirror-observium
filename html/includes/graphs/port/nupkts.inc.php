<?php

# FIXME uhh..
if (1)
{
#  $rrd_list[1]['filename'] = $config['rrd_dir'] . "/" . $device['hostname'] . "/port-" . safename($port['ifIndex'] . ".rrd");
#  $rrd_list[1]['descr'] = $int['ifDescr'];
#  $rrd_list[1]['rra_in'] = "INNUCASTPKTS";
#  $rrd_list[1]['rra_out'] = "OUTNUCASTPKTS";
#  $rrd_list[1]['descr']   = "NonUnicast";
#  $rrd_list[1]['colour_area_in'] = "BB77BB";
#  $rrd_list[1]['colour_area_out'] = "FFDD88";

  $rrd_list[2]['filename'] = $config['rrd_dir'] . "/" . $device['hostname'] . "/port-" . safename($port['ifIndex'] . ".rrd");
  $rrd_list[2]['descr'] = $int['ifDescr'];
  $rrd_list[2]['rra_in'] = "INBROADCASTPKTS";
  $rrd_list[2]['rra_out'] = "OUTBROADCASTPKTS";
  $rrd_list[2]['descr']   = "Broadcast";
  $rrd_list[2]['colour_area_in'] = "aa37BB";
  $rrd_list[2]['colour_area_out'] = "ee9D88";

  $rrd_list[4]['filename'] = $config['rrd_dir'] . "/" . $device['hostname'] . "/port-" . safename($port['ifIndex'] . ".rrd");
  $rrd_list[4]['descr'] = $int['ifDescr'];
  $rrd_list[4]['rra_in'] = "INMULTICASTPKTS";
  $rrd_list[4]['rra_out'] = "OUTMULTICASTPKTS";
  $rrd_list[4]['descr']   = "Multicast";
  $rrd_list[4]['colour_area_in'] = "905090";
  $rrd_list[4]['colour_area_out'] = "c0a060";

  $units='';
  $units_descr='Packets/sec';
  $total_units='B';
  $colours_in='purples';
  $multiplier = "1";
  $colours_out = 'oranges';

  $nototal = 1;

  include("includes/graphs/generic_multi_seperated.inc.php");
}
elseif (is_file($config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename($port['ifIndex'] . ".rrd"))) 
{
  $rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename($port['ifIndex'] . ".rrd");

  $rra_in = "INNUCASTPKTS";
  $rra_out = "OUTNUCASTPKTS";

  $colour_area_in = "AA66AA";
  $colour_line_in = "330033";
  $colour_area_out = "FFDD88";
  $colour_line_out = "FF6600";

  $colour_area_in_max = "cc88cc";
  $colour_area_out_max = "FFefaa";

  $unit_text = "Packets";

  $graph_max = 1;

  include("includes/graphs/generic_duplex.inc.php");
}

?>
