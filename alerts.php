#!/usr/bin/env php
<?php

include("includes/defaults.inc.php");
include("config.php");
include("includes/functions.php");

foreach (dbFetchRows("SELECT *, A.id AS id FROM `alerts` AS A, `devices` AS D WHERE A.device_id = D.device_id AND alerted = '0'") as $alert)
{
  $id = $alert['id'];
  $host = $alert['hostname'];
  $date = $alert['time_logged'];
  $msg = $alert['message'];
  $alert_text .= "$date $host $msg";

  dbUpdate(array('alerted' => '1'), 'alerts', '`id` = ?' array($id))

}

if ($alert_text)
{
  echo("$alert_text");
  #  `echo '$alert_text' | gnokii --sendsms <NUMBER>`;
}

?>
