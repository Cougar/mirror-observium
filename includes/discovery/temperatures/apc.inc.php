<?php

if ($device['os'] == "apc")
{
  $oids = snmp_get($device, "1.3.6.1.4.1.318.1.1.1.2.2.2.0", "-OsqnU", "");
  if ($debug) { echo($oids."\n"); }
  if ($oids)
  {
    echo("APC UPS Battery ");
    list($oid,$current) = explode(' ',$oids);
    $precision = 1;
    $sensorType = "apc";
    $index = 0;
    $descr = "Battery Temperature";

    discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, $sensorType, $descr, '1', '1', NULL, NULL, NULL, NULL, $current);
  }

  # Environmental monitoring on UPSes etc
  // FIXME emConfigProbesTable may also be used? But not filled out on my device...
  $apc_env_data = snmpwalk_cache_oid($device, "iemConfigProbesTable", array(), "PowerNet-MIB");
  $apc_env_data = snmpwalk_cache_oid($device, "iemStatusProbesTable", $apc_env_data, "PowerNet-MIB");

  foreach (array_keys($apc_env_data) as $index)
  {
    $descr           = $apc_env_data[$index]['iemStatusProbeName'];
    $current         = $apc_env_data[$index]['iemStatusProbeCurrentTemp'];
    $sensorType      = 'apc';
    $oid             = '.1.3.6.1.4.1.318.1.1.10.2.3.2.1.4.' . $index;
    $low_limit       = ($apc_env_data[$index]['iemConfigProbeMinTempEnable']  != 'disabled' ? $apc_env_data[$index]['iemConfigProbeMinTempThreshold'] : NULL);
    $low_warn_limit  = ($apc_env_data[$index]['iemConfigProbeLowTempEnable']  != 'disabled' ? $apc_env_data[$index]['iemConfigProbeLowTempThreshold'] : NULL);
    $high_warn_limit = ($apc_env_data[$index]['iemConfigProbeHighTempEnable'] != 'disabled' ? $apc_env_data[$index]['iemConfigProbeHighTempThreshold'] : NULL);
    $high_limit      = ($apc_env_data[$index]['iemConfigProbeMaxTempEnable']  != 'disabled' ? $apc_env_data[$index]['iemConfigProbeMaxTempThreshold'] : NULL);

    discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, $sensorType, $descr, '1', '1', $low_limit, $low_warn_limit, $high_warn_limit, $high_limit , $current);
  }

  # Environmental monitoring on rPDU2
  $apc_env_data = snmpwalk_cache_oid($device, "rPDU2SensorTempHumidityConfigTable", array(), "PowerNet-MIB");
  $apc_env_data = snmpwalk_cache_oid($device, "rPDU2SensorTempHumidityStatusTable", $apc_env_data, "PowerNet-MIB");

  foreach (array_keys($apc_env_data) as $index)
  {
    $descr           = $apc_env_data[$index]['rPDU2SensorTempHumidityStatusName'];
    $current         = $apc_env_data[$index]['rPDU2SensorTempHumidityStatusTempC'];
    $divisor = 10;
    $sensorType      = 'apc';
    $oid             = ' .1.3.6.1.4.1.318.1.1.26.10.2.2.1.8.' . $index;
    $high_warn_limit = ($apc_env_data[$index]['rPDU2SensorTempHumidityConfigTemperatureAlarmEnable'] != 'disabled' ? $apc_env_data[$index]['rPDU2SensorTempHumidityConfigTempHighThreshC'] : NULL);
    $high_limit      = ($apc_env_data[$index]['rPDU2SensorTempHumidityConfigTemperatureAlarmEnable']  != 'disabled' ? $apc_env_data[$index]['rPDU2SensorTempHumidityConfigTempMaxThreshC'] : NULL);

    discover_sensor($valid['sensor'], 'temperature', $device, $oid, $index, $sensorType, $descr, $divisor, '1', NULL, NULL, $high_warn_limit, $high_limit , $current);
  }

/*
            [iemConfigProbeHighHumidThreshold] => -1
            [iemConfigProbeLowHumidThreshold] => -1
            [iemConfigProbeHighHumidEnable] => disabled
            [iemConfigProbeLowHumidEnable] => disabled
            [iemConfigProbeMaxHumidThreshold] => -1
            [iemConfigProbeMinHumidThreshold] => -1
            [iemConfigProbeMaxHumidEnable] => disabled
            [iemConfigProbeMinHumidEnable] => disabled
            [iemConfigProbeHumidHysteresis] => -1

            [iemStatusProbeStatus] => connected
            [iemStatusProbeCurrentTemp] => 25
            [iemStatusProbeTempUnits] => celsius

            [iemStatusProbeCurrentHumid] => 0
*/

  # InRow Chiller.
  # A silly check to find out if it's the right hardware.
  $oids = snmp_get($device, "airIRRCGroupSetpointsCoolMetric.0", "-OsqnU", "PowerNet-MIB");
  if ($oids)
  {
    echo("APC InRow Chiller ");
    $temps = array();
    $temps['airIRRCUnitStatusRackInletTempMetric'] = "Rack Inlet";
    $temps['airIRRCUnitStatusSupplyAirTempMetric'] = "Supply Air";
    $temps['airIRRCUnitStatusReturnAirTempMetric'] = "Return Air";
    $temps['airIRRCUnitStatusEnteringFluidTemperatureMetric'] = "Entering Fluid";
    $temps['airIRRCUnitStatusLeavingFluidTemperatureMetric'] = "Leaving Fluid";
    foreach ($temps as $obj => $descr)
    {
      $oids = snmp_get($device, $obj . ".0", "-OsqnU", "PowerNet-MIB");
      list($oid,$current) = explode(' ',$oids);
      $divisor = 10;
      $sensorType = substr($descr, 0, 2);
      echo(discover_sensor($valid['sensor'], 'temperature', $device, $oid, '0', $sensorType, $descr, $divisor, '1', NULL, NULL, NULL, NULL, $current));
    }
  }
}

?>
