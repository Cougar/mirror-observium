<?php

global $valid_sensor;

if ($device['os_group'] == "unix")
{
  # FIXME snmp_walk
  # ObserverNMS-style temperature
  $oids = shell_exec($config['snmpwalk'] . " -M " . $config['mibdir'] . " -M " . $config['mibdir'] . " -".$device['snmpver']." -m SNMPv2-SMI -Osqn -CI -c ".$device['community']." ".$device['transport'].":".$device['hostname'].":".$device['port']." .1.3.6.1.4.1.2021.7891 | sed s/.1.3.6.1.4.1.2021.7891.// | grep '.1.1 ' | grep -v '.101.' | cut -d'.' -f 1");
  $oids = trim($oids);
  if ($oids) echo("Observer-Style ");
  foreach (explode("\n",$oids) as $oid)
  {
    $oid = trim($oid);
    if ($oid != "")
    {
      # FIXME snmp_get
      $descr_query = $config['snmpget'] . " -M " . $config['mibdir'] . " -".$device['snmpver']." -m SNMPv2-SMI -Osqn -c ".$device['community']." ".$device['hostname'].":".$device['port']." .1.3.6.1.4.1.2021.7891.$oid.2.1 | sed s/.1.3.6.1.4.1.2021.7891.$oid.2.1\ //";
      $descr = trim(str_replace("\"", "", shell_exec($descr_query)));
      $fulloid = ".1.3.6.1.4.1.2021.7891.$oid.101.1";
      discover_sensor($valid_sensor, 'temperature', $device, $fulloid, $oid, 'observium', $descr, '1', '1', NULL, NULL, NULL, NULL, $current);
    }
  }
}

?>