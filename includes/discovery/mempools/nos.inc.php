<?php

if ($device['os'] == "nos")
{
  echo("nos: ");

  $used = snmp_get($device, "1.3.6.1.4.1.1588.2.1.1.1.26.6.0", "-Ovq");
  $total = "2048";

  $percent = $used / $total * 100;

  if (is_numeric($total) && is_numeric($used))
  {
    discover_mempool($valid_mempool, $device, 0, "nos", "Memory", "1", NULL, NULL);
  }
}
?>
