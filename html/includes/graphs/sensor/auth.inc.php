<?php

if (is_numeric($id))
{
  $sensor = mysql_fetch_assoc(mysql_query("SELECT * FROM sensors WHERE sensor_id = '".mres($id)."'"));

  if (is_numeric($sensor['device_id']) && ($config['allow_unauth_graphs'] || device_permitted($sensor['device_id'])))
  {
    $device = device_by_id_cache($sensor['device_id']);

    ### This doesn't quite work for all yet.
    #$rrd_filename  = $config['rrd_dir'] . "/".$device['hostname']."/" . safename($sensor['sensor_class']."-" . $sensor['sensor_type'] . "-".$sensor['sensor_index'].".rrd");
    $rrd_filename = get_sensor_rrd($device, $sensor);

    $title  = generate_device_link($device);
    $title .= " :: Sensor :: " . htmlentities($sensor['sensor_descr']);
    $auth = TRUE;
  }
}

?>
