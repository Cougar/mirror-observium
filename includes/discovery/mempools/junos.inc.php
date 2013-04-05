<?php

// JUNOS mempools
if ($device['os'] == "junos")
{
  echo("JUNOS : ");
  $mempools_array = snmpwalk_cache_multi_oid($device, "jnxOperatingBuffer",   $mempools_array, "JUNIPER-MIB", mib_dirs('junos'));
  $mempools_array = snmpwalk_cache_multi_oid($device, "jnxOperatingDRAMSize", $mempools_array, "JUNIPER-MIB", mib_dirs('junos'));
  $mempools_array = snmpwalk_cache_multi_oid($device, "jnxOperatingMemory",   $mempools_array, "JUNIPER-MIB", mib_dirs('junos'));
  $mempools_array = snmpwalk_cache_multi_oid($device, "jnxOperatingDescr",    $mempools_array, "JUNIPER-MIB", mib_dirs('junos'));
  if ($debug) { print_r($mempools_array); }

  if (is_array($mempools_array))
  {
    foreach ($mempools_array as $index => $entry)
    {
      if ($entry['jnxOperatingDRAMSize'] || $entry['jnxOperatingMemory'])
      {
        if (stripos($entry['jnxOperatingDescr'], "sensor") || stripos($entry['jnxOperatingDescr'], "fan")) continue;
        if ($debug) { echo($index . " " . $entry['jnxOperatingDescr'] . " -> " . $entry['jnxOperatingBuffer'] . " -> " . $entry['jnxOperatingDRAMSize'] . "\n"); }
        $usage_oid = ".1.3.6.1.4.1.2636.3.1.13.1.8." . $index;
        $descr = $entry['jnxOperatingDescr'];
        $usage = $entry['jnxOperatingBuffer'];
        if (!strstr($descr, "No") && !strstr($usage, "No") && $descr != "")
        {
          discover_mempool($valid_mempool, $device, $index, "junos", $descr, "1", NULL, NULL);
        }
      } // End if checks
    } // End Foreach
  } // End if array
  else
  {
    $srx_mempools_array = snmpwalk_cache_multi_oid($device, "jnxJsSPUMonitoringMemoryUsage", $srx_mempools_array, "JUNIPER-SRX5000-SPU-MONITORING-MIB", mib_dirs('junos'));

    if (is_array($srx_mempools_array))
    {
      foreach ($srx_mempools_array as $index => $entry)
      {
        if ($index)
        {
          $usage_oid = ".1.3.6.1.4.1.2636.3.39.1.12.1.1.1.5." . $index;
          $descr = "Memory"; # No description in the table?
          $usage = $entry['jnxJsSPUMonitoringMemoryUsage'];

          discover_mempool($valid_mempool, $device, $index, "junos", $descr, "1", NULL, NULL);
        }
      }
    }
  }
} // End JUNOS mempools

unset ($mempools_array);
unset ($srx_mempools_array);

?>
