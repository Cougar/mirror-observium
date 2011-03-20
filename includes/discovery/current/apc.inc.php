<?php

global $valid_sensor;

## APC
if ($device['os'] == "apc")
{
  # PDU - Phase
  $oids = snmp_walk($device, "rPDUStatusPhaseIndex", "-OsqnU", "PowerNet-MIB");
  if ($oids)
  {
    if ($debug) { echo($oids."\n"); }
    $oids = trim($oids);
    if ($oids) echo("APC PowerNet-MIB Phase ");
    $type = "apc";
    $precision = "10";
    foreach (explode("\n", $oids) as $data)
    {
      $data = trim($data);
      if ($data)
      {
        list($oid,$kind) = explode(" ", $data);
        $split_oid = explode('.',$oid);
        $index = $split_oid[count($split_oid)-1];

        $current_oid   = "1.3.6.1.4.1.318.1.1.12.2.3.1.1.2.".$index;	#rPDULoadStatusLoad
        $phase_oid     = "1.3.6.1.4.1.318.1.1.12.2.3.1.1.4.".$index;	#rPDULoadStatusPhaseNumber
        $limit_oid     = "1.3.6.1.4.1.318.1.1.12.2.2.1.1.4.".$index;	#rPDULoadPhaseConfigOverloadThreshold
        $lowlimit_oid  = "1.3.6.1.4.1.318.1.1.12.2.2.1.1.2.".$index;	#rPDULoadPhaseConfigLowLoadThreshold
        $warnlimit_oid = "1.3.6.1.4.1.318.1.1.12.2.2.1.1.3.".$index;	#rPDULoadPhaseConfigNearOverloadThreshold

        $phase     = snmp_get($device, $phase_oid, "-Oqv", "");
        $current   = snmp_get($device, $current_oid, "-Oqv", "") / $precision;
        $limit     = snmp_get($device, $limit_oid, "-Oqv", "");			# No / $precision here! Nice, APC!
        $lowlimit  = snmp_get($device, $lowlimit_oid, "-Oqv", "");		# No / $precision here! Nice, APC!
        $warnlimit = snmp_get($device, $warnlimit_oid, "-Oqv", "");		# No / $precision here! Nice, APC!
        if (count(explode("\n",$oids)) != 1)
        {
          $descr     = "Phase $phase";
        }
        else
        {
          $descr     = "Output";
        }
        discover_sensor($valid_sensor, 'current', $device, $current_oid, $index, $type, $descr, '10', '1', $lowlimit, NULL, $warnlimit, $limit, $current);
      }
    }
  }

  unset($oids);

  #v2 firmware- first bank is total, v3 firmware, 3rd bank is total
  $oids = snmp_walk($device, "rPDULoadBankConfigIndex", "-OsqnU", "PowerNet-MIB");	# should work with firmware v2 and v3
  if ($oids)
  {
    echo("APC PowerNet-MIB Banks ");
    if ($debug) { echo($oids."\n"); }
    $oids = trim($oids);
    $type = "apc";
    $precision = "10";

    # version 2 does some stuff differently- total power is first oid in index instead of the last.
    # will look something like "AOS v2.6.4 / App v2.6.5"
    $baseversion = "3";
    if (stristr($device['version'], 'AOS v2') == TRUE) { $baseversion = "2"; }

    foreach (explode("\n", $oids) as $data)
    {
      $data = trim($data);
      if ($data)
      {
        list($oid,$kind) = explode(" ", $data);
        $split_oid = explode('.',$oid);

        $index = $split_oid[count($split_oid)-1];

        $banknum = $index -1;
        $descr = "Bank ".$banknum;
        if ($baseversion == "3")
        {
          if ($index == "1") { $descr = "Bank Total"; }
        }
        if ($baseversion == "2")
        {
          if ($index == "1") { $descr = "Bank Total"; }
        }

        $current_oid	= "1.3.6.1.4.1.318.1.1.12.2.3.1.1.2.".$index;		#rPDULoadStatusLoad
        $bank_oid	= "1.3.6.1.4.1.318.1.1.12.2.3.1.1.5.".$index;		#rPDULoadStatusBankNumber
        $limit_oid	= "1.3.6.1.4.1.318.1.1.12.2.4.1.1.4.".$index;		#rPDULoadBankConfigOverloadThreshold
        $lowlimit_oid	= "1.3.6.1.4.1.318.1.1.12.2.4.1.1.2.".$index;		#rPDULoadBankConfigLowLoadThreshold
        $warnlimit_oid	= "1.3.6.1.4.1.318.1.1.12.2.4.1.1.3.".$index;		#rPDULoadBankConfigNearOverloadThreshold

        $bank      = snmp_get($device, $bank_oid, "-Oqv", "");
        $current   = snmp_get($device, $current_oid, "-Oqv", "") / $precision;
        $limit     = snmp_get($device, $limit_oid, "-Oqv", "");
        $lowlimit  = snmp_get($device, $lowlimit_oid, "-Oqv", "");
        $warnlimit = snmp_get($device, $warnlimit_oid, "-Oqv", "");

        discover_sensor($valid_sensor, 'current', $device, $current_oid, $index, $type, $descr, '10', '1', $lowlimit, NULL, $warnlimit, $limit, $current);
      }
    }

    unset($baseversion);
  }

  unset($oids);

  # ATS
  $oids = snmp_walk($device, "atsConfigPhaseTableIndex", "-OsqnU", "PowerNet-MIB");
  if ($oids)
  {
    $type = "apc";
    if ($debug) { print_r($oids); }
    $oids = trim($oids);
    if ($oids) echo("APC PowerNet-MIB ATS ");
    $current_oid   = "1.3.6.1.4.1.318.1.1.8.5.4.3.1.4.1.1.1";	#atsOutputCurrent
    $limit_oid     = "1.3.6.1.4.1.318.1.1.8.4.16.1.5.1";	#atsConfigPhaseOverLoadThreshold
    $lowlimit_oid  = "1.3.6.1.4.1.318.1.1.8.4.16.1.3.1";	#atsConfigPhaseLowLoadThreshold
    $warnlimit_oid = "1.3.6.1.4.1.318.1.1.8.4.16.1.4.1";	#atsConfigPhaseNearOverLoadThreshold
    $index         = 1;

    $current   = snmp_get($device, $current_oid, "-Oqv", "") / $precision;
    $limit     = snmp_get($device, $limit_oid, "-Oqv", "");	# No / $precision here! Nice, APC!
    $lowlimit  = snmp_get($device, $lowlimit_oid, "-Oqv", "");	# No / $precision here! Nice, APC!
    $warnlimit = snmp_get($device, $warnlimit_oid, "-Oqv", "");	# No / $precision here! Nice, APC!
    $descr     = "Output Feed";

    discover_sensor($valid_sensor, 'current', $device, $current_oid, $index, $type, $descr, '10', '1', $lowlimit, NULL, $warnlimit, $limit, $current);
  }

  unset($oids);
}

?>
