<?php

## Generate a list of ports and then call the multi_bits grapher to generate from the list

$device = device_by_id_cache($id);

foreach (dbFetchRows("SELECT * FROM `ports` WHERE `device_id` = ?", array($id)) as $int)
{
  $ignore = 0;
  if (is_array($config['device_traffic_iftype']))
  {
    foreach ($config['device_traffic_iftype'] as $iftype)
    {
      if (preg_match($iftype ."i", $int['ifType']))
      {
        $ignore = 1;
      }
    }
  }
  if (is_array($config['device_traffic_descr']))
  {
    foreach ($config['device_traffic_descr'] as $ifdescr)
    {
      if (preg_match($ifdescr."i", $int['ifDescr']) || preg_match($ifdescr."i", $int['ifName']) || preg_match($ifdescr."i", $int['portName']))
      {
        $ignore = 1;
      }
    }
  }

  if (is_file($config['rrd_dir'] . "/" . $device['hostname'] . "/port-" . safename($int['ifIndex'] . ".rrd")) && $ignore != 1)
  {
    $rrd_filenames[] = $config['rrd_dir'] . "/" . $device['hostname'] . "/port-" . safename($int['ifIndex'] . ".rrd");
  }

  unset($ignore);
}

$rra_in  = "INOCTETS";
$rra_out = "OUTOCTETS";

$colour_line_in = "006600";
$colour_line_out = "000099";
$colour_area_in = "CDEB8B";
$colour_area_out = "C3D9FF";

include("includes/graphs/generic_multi_bits.inc.php");

?>
