<?php

// Ignore this discovery module if we have already discovered things in CISCO-ENHANCED-MEMPOOL-MIB. Dirty duplication.

$cemp_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `mempools` WHERE `device_id` = '".$device['device_id']."' AND `mempool_type` = 'cemp'"),0);

if (($device['os_group'] == "cisco") && $cemp_count == "0")
{
  echo("OLD-CISCO-MEMORY-POOL: ");

  $cmp_array = snmpwalk_cache_oid($device, 'ciscoMemoryPool', NULL, "CISCO-MEMORY-POOL-MIB");

  if (is_array($cmp_array)) {
    foreach ($cmp_array as $index => $cmp) {
      if (is_numeric($cmp['ciscoMemoryPoolUsed']) && is_numeric($index)) {
        discover_mempool($valid_mempool, $device, $index, "cmp", $cmp['ciscoMemoryPoolName'], "1", NULL, NULL);
      }
    }
  }
}

?>
