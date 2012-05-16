<?php

echo("IPv6 Addresses : ");

$oids = snmp_walk($device, "ipAddressIfIndex.ipv6", "-Ln -Osq", "IP-MIB");
$oids = str_replace("ipAddressIfIndex.ipv6.", "", $oids);
$oids = str_replace("\"", "", $oids);
$oids = str_replace("IP-MIB::", "", $oids);
$oids = trim($oids);

foreach (explode("\n", $oids) as $data)
{
  if ($data)
  {
    $data = trim($data);
    list($ipv6addr,$ifIndex) = explode(" ", $data);
    $oid = "";
    $sep = ''; $adsep = '';
    unset($ipv6_address);
    $do = '0';
    foreach (explode(":", $ipv6addr) as $part)
    {
      $n = hexdec($part);
      $oid = "$oid" . "$sep" . "$n";
      $sep = ".";
      $ipv6_address = $ipv6_address . "$adsep" . $part;
      $do++;
      if ($do == 2) { $adsep = ":"; $do = '0'; } else { $adsep = ""; }
    }

    $ipv6_prefixlen = snmp_get($device, ".1.3.6.1.2.1.4.34.1.5.2.16.$oid", "", "IP-MIB");
    $ipv6_prefixlen = explode(".", $ipv6_prefixlen);
    $ipv6_prefixlen = str_replace("\"", "", end($ipv6_prefixlen));

    $ipv6_origin = snmp_get($device, ".1.3.6.1.2.1.4.34.1.6.2.16.$oid", "-Ovq", "IP-MIB");

    discover_process_ipv6($valid, $ifIndex,$ipv6_address,$ipv6_prefixlen,$ipv6_origin);
  } // if $data
} // foreach

if (!$oids)
{
  $oids = snmp_walk($device, "ipv6AddrPfxLength", "-Ln -Osq -OnU", "IPV6-MIB");
  $oids = str_replace(".1.3.6.1.2.1.55.1.8.1.2.", "", $oids);
  $oids = str_replace("\"", "", $oids);  $oids = trim($oids);

  foreach (explode("\n", $oids) as $data)
  {
    if ($data)
    {
      $data = trim($data);
      list($if_ipv6addr,$ipv6_prefixlen) = explode(" ", $data);
      list($ifIndex,$ipv6addr) = explode(".",$if_ipv6addr,2);
      $ipv6_address = snmp2ipv6($ipv6addr);
      $ipv6_origin = snmp_get($device, "IPV6-MIB::ipv6AddrType.$if_ipv6addr", "-Ovq", "IPV6-MIB");
      discover_process_ipv6($valid, $ifIndex,$ipv6_address,$ipv6_prefixlen,$ipv6_origin);
    } // if $data
  } // foreach
} // if $oids

$sql   = "SELECT * FROM ipv6_addresses AS A, ports AS I WHERE I.device_id = '".$device['device_id']."' AND  A.port_id = I.port_id";
$data = mysql_query($sql);

while ($row = mysql_fetch_assoc($data))
{
  $full_address = $row['ipv6_address'] . "/" . $row['ipv6_prefixlen'];
  $port_id = $row['port_id'];
  $valid_address = $full_address  . "-" . $port_id;
  if (!$valid['ipv6'][$valid_address])
  {
    echo("-");
    $query = @mysql_query("DELETE FROM `ipv6_addresses` WHERE `ipv6_address_id` = '".$row['ipv6_address_id']."'");
    if (!mysql_result(mysql_query("SELECT count(*) FROM ipv6_addresses WHERE ipv6_network_id = '".$row['ipv6_network_id']."'"),0))
    {
      $query = @mysql_query("DELETE FROM `ipv6_networks` WHERE `ipv6_network_id` = '".$row['ipv6_network_id']."'");
    }
  }
}

echo("\n");

?>
