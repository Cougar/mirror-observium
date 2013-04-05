<?php

# NETSWITCH-MIB::hpLocalMemSlotIndex.1 = INTEGER: 1
# NETSWITCH-MIB::hpLocalMemSlabCnt.1 = Counter32: 3966
# NETSWITCH-MIB::hpLocalMemFreeSegCnt.1 = Counter32: 166
# NETSWITCH-MIB::hpLocalMemAllocSegCnt.1 = Counter32: 3803
# NETSWITCH-MIB::hpLocalMemTotalBytes.1 = INTEGER: 11337704
# NETSWITCH-MIB::hpLocalMemFreeBytes.1 = INTEGER: 9669104
# NETSWITCH-MIB::hpLocalMemAllocBytes.1 = INTEGER: 1668728

if (!is_array($mempool_cache['hpLocal']))
{
  $mempool_cache['hpLocal'] = snmpwalk_cache_oid($device, "hpLocal", NULL, "NETSWITCH-MIB", mib_dirs('hp'));
  if ($debug) { print_r($mempool_cache); }
}
else
{
  if ($debug) { echo("Cached!"); }
}

$entry = $mempool_cache['hpLocal'][$mempool[mempool_index]];

$mempool['units'] = "1";
$mempool['used']  = $entry['hpLocalMemAllocBytes'];
$mempool['total'] = $entry['hpLocalMemTotalBytes'];
$mempool['free']  = $entry['hpLocalMemFreeBytes'];

?>