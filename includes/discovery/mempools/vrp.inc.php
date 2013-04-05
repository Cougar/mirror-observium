<?php

// Huawei VRP  mempools
if($device['os'] == "vrp")
{
  echo("VRP : ");
  $mempools_array = snmpwalk_cache_multi_oid($device, "hwEntityMemUsage", $mempools_array, "HUAWEI-ENTITY-EXTENT-MIB", mib_dirs('huawei'));
  $mempools_array = snmpwalk_cache_multi_oid($device, "hwEntityMemSize",  $mempools_array, "HUAWEI-ENTITY-EXTENT-MIB", mib_dirs('huawei'));
  $mempools_array = snmpwalk_cache_multi_oid($device, "hwEntityBomEnDesc",$mempools_array, "HUAWEI-ENTITY-EXTENT-MIB", mib_dirs('huawei'));
  if ($debug) { print_r($mempools_array); }

  if (is_array($mempools_array))
  {
    foreach ($mempools_array as $index => $entry)
    {
      if ($entry['hwEntityMemSize'] != 0 )
      {
        if ($debug) { echo($index . " " . $entry['hwEntityBomEnDesc'] . " -> " . $entry['hwEntityMemUsage'] . " -> " . $entry['hwEntityMemSize'] . "\n"); }
        $usage_oid = ".1.3.6.1.4.1.2011.5.25.31.1.1.1.1.7." . $index;
        $descr = $entry['hwEntityBomEnDesc'];
        $usage = $entry['hwEntityMemUsage'];
        if (!strstr($descr, "No") && !strstr($usage, "No") && $descr != "" )
        {
          discover_mempool($valid_mempool, $device, $index, "vrp", $descr, "1", NULL, NULL);
        }
      } // End if checks
    } // End Foreach
  } // End if array
} // End VRP mempools

unset ($mempools_array);

?>
