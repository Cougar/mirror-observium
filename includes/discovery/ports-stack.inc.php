<?php

echo("Port Stack: ");

$sql = "SELECT * FROM `ports_stack` WHERE `device_id` = '".$device['device_id']."'";
$query = mysql_query($sql);
while ($entry = mysql_fetch_assoc($query))
{
  $stack_db_array[$entry['port_id_high']][$entry['port_id_low']]['ifStackStatus'] = $entry['ifStackStatus'];
}

$stack_poll_array = snmpwalk_cache_twopart_oid($device, "ifStackStatus", array());

foreach ($stack_poll_array as $port_id_high => $entry_high)
{
  foreach ($entry_high as $port_id_low => $entry_low)
  {
    $ifStackStatus = $entry_low['ifStackStatus'];
    if (isset($stack_db_array[$port_id_high][$port_id_low]))
    {
      if ($stack_db_array[$port_id_high][$port_id_low]['ifStackStatus'] == $ifStackStatus)
      {
        echo(".");
      } else {
        mysql_query("UPDATE `ports_stack` SET `ifStackStatus` = '".$ifStackStatus."' WHERE `device_id` = '".$device['device_id']."' AND `port_id_high` = '".$port_id_high."' AND `port_id_low` = '".$port_id_low."'");
        echo("U");
        if ($debug) { echo(mysql_error()); }
      }
      unset($stack_db_array[$port_id_high][$port_id_low]);
    } else {
      mysql_query("INSERT INTO `ports_stack` (`device_id`,`port_id_high`,`port_id_low`,`ifStackStatus`)  VALUES ('".$device['device_id']."','".$port_id_high."','".$port_id_low."','".$ifStackStatus."')");
      echo("+");
      if ($debug) { echo(mysql_error()); }
    }
  }
}

foreach ($stack_db_array AS $port_id_high => $array)
{
  foreach ($array AS $port_id_low => $blah)
  {
    echo($device['device_id']." ".$port_id_low." ".$port_id_high. "\n");
    dbDelete('ports_stack', "`device_id` =  ? AND port_id_high = ? AND port_id_low = ?", array($device['device_id'], $port_id_high, $port_id_low));
    echo("-");
  }
}

echo("\n");

?>
