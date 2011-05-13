#!/usr/bin/env php
<?php

include("includes/defaults.inc.php");
include("config.php");
include("includes/functions.php");

## Check all of our interface RRD files for errors

if ($argv[1]) { $where = "AND `interface_id` = ?"; $params = array($argv[1]); }

$i = '0';

foreach (dbFetchRows("SELECT * FROM `ports` AS I, `devices` AS D WHERE I.device_id = D.device_id $where", $params) as $interface)
{
  $errors = $interface['ifInErrors_delta'] + $interface['ifOutErrors_delta'];
  if ($errors > '1')
  {
    $errored[] = $interface['hostname'] . " - " . $interface['ifDescr'] . " - " . $interface['ifAlias'] . " - " . $interface['ifInErrors_delta'] . " - " . $interface['ifOutErrors_delta'];
  }
  $i++;
}

echo("Checked $i Interfaces\n");

if ($errored)
{ ## If there are errored ports
  $i = 0;
  $msg = "Interfaces with errors : \n\n";

  foreach ($errored as $int)
  {
    $msg .= "$int\n";  ## Add a line to the report email warning about them
    $i++;
  }
  ## Send the alert email
  notify($device, "Observium detected errors on $i interface" . ($i != 1 ? 's' : ''), $msg);
}

?>
