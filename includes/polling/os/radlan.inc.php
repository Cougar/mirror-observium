<?php


if($sysDescr == "Neyland 24T") {
  #$hardware = snmp_get($device, "productIdentificationVendor.0", "-Ovq", "Dell-Vendor-MIB");
  $hardware = "Dell ".snmp_get($device, "productIdentificationDisplayName.0", "-Ovq", "Dell-Vendor-MIB");
  $version  = snmp_get($device, "productIdentificationVersion.0", "-Ovq", "Dell-Vendor-MIB");
} else {
  $version  = snmp_get($device, "rndBrgVersion.0", "-Ovq", "RADLAN-MIB");
  $hardware = str_replace("ATI", "Allied Telesis", $sysDescr);
}
$features = snmp_get($device, "rndBaseBootVersion.00", "-Ovq", "RADLAN-MIB");

$version = str_replace("\"","", $version);
$features = str_replace("\"","", $features);
$hardware = str_replace("\"","", $hardware);

?>
