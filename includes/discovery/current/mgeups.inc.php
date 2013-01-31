<?php

// MGE UPS
if ($device['os'] == "mgeups")
{
  echo("MGE ");
  $oids = trim(snmp_walk($device, "mgoutputCurrent", "-OsqnU", "MG-SNMP-UPS-MIB"));
  if ($debug) { echo($oids."\n"); }
  $numPhase = count(explode("\n",$oids));
  for($i = 1; $i <= $numPhase;$i++)
  {
    unset($current);
    $current_oid   = ".1.3.6.1.4.1.705.1.7.2.1.5.$i";
    $descr      = "Output"; if ($numPhase > 1) $descr .= " Phase $i";
    $current    = snmp_get($device, $current_oid, "-Oqv");
    if (!$current)
    {
      $current_oid .= ".0";
      $current    = snmp_get($device, $current_oid, "-Oqv");
    }
    $current   /= 10;
    $type       = "mge-ups";
    $precision  = 10;
    $index      = $i;
    $warnlimit  = NULL;
    $lowlimit   = 0;
    $limit      = NULL;
    $lowwarnlimit = NULL;

    discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, '10', '1', $lowlimit, $lowwarnlimit, $warnlimit, $limit, $current);
  }

  $oids = trim(snmp_walk($device, "mginputCurrent", "-OsqnU", "MG-SNMP-UPS-MIB"));
  if ($debug) { echo($oids."\n"); }
  $numPhase = count(explode("\n",$oids));
  for($i = 1; $i <= $numPhase;$i++)
  {
    unset($current);
    $current_oid   = ".1.3.6.1.4.1.705.1.6.2.1.6.$i";
    $descr      = "Input"; if ($numPhase > 1) $descr .= " Phase $i";
    $current    = snmp_get($device, $current_oid, "-Oqv");
    if (!$current)
    {
      $current_oid .= ".0";
      $current    = snmp_get($device, $current_oid, "-Oqv");
    }
    $current   /= 10;
    $type       = "mge-ups";
    $precision  = 10;
    $index      = 100+$i;
    $warnlimit  = NULL;
    $lowlimit   = 0;
    $limit      = NULL;
    $lowwarnlimit = NULL;

    discover_sensor($valid['sensor'], 'current', $device, $current_oid, $index, $type, $descr, '10', '1', $lowlimit, $lowwarnlimit, $warnlimit, $limit, $current);
  }
}

?>
