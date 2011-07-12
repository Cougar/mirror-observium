<?php

## Common Functions

function delete_port($int_id)
{
  global $config;

  $interface = dbFetchRow("SELECT * FROM `ports` AS P, `devices` AS D WHERE P.interface_id = ? AND D.device_id = P.device_id", array($int_id));

  $interface_tables = array('adjacencies', 'ipaddr', 'ip6adjacencies', 'ip6addr', 'mac_accounting', 'bill_ports', 'pseudowires', 'ports');

  foreach($interface_tables as $table) {
    dbDelete($table, "`interface_id` =  ?", array($int_id));
  }

  dbDelete('links', "`local_interface_id` =  ?", array($int_id));
  dbDelete('links', "`remote_interface_id` =  ?", array($int_id));
  dbDelete('bill_ports', "`port_id` =  ?", array($int_id));

  unlink(trim($config['rrd_dir'])."/".trim($interface['hostname'])."/port-".$interface['ifIndex'].".rrd");
}

function sgn($int)
{
  if ($int < 0)
  {
    return -1;
  } elseif ($int == 0) {
    return 0;
  } else {
    return 1;
  }
}

function get_sensor_rrd($device, $sensor)
{
  global $config;

  # For IPMI, sensors tend to change order, and there is no index, so we prefer to use the description as key here.
  if ($config['os'][$device['os']]['sensor_descr'] || $sensor['poller_type'] == "ipmi") 
  {
    $rrd_file = $config['rrd_dir']."/".$device['hostname']."/".safename("sensor-".$sensor['sensor_class']."-".$sensor['sensor_type']."-".$sensor['sensor_descr'] . ".rrd");
  } else {
    $rrd_file = $config['rrd_dir']."/".$device['hostname']."/".safename("sensor-".$sensor['sensor_class']."-".$sensor['sensor_type']."-".$sensor['sensor_index'] . ".rrd");
  }

  return($rrd_file);
}

function get_port_by_index_cache($device_id, $ifIndex)
{
  global $port_index_cache;

  if (isset($port_index_cache[$device_id][$ifIndex]) && is_array($port_index_cache[$device_id][$ifIndex]))
  {
    $port = $port_index_cache[$device_id][$ifIndex];
  } else {
    $port = get_port_by_ifIndex($device_id, $ifIndex);
    $port_index_cache[$device_id][$ifIndex] = $port;
  }

  return $port;
}

function get_port_by_ifIndex($device_id, $ifIndex)
{
  return dbFetchRow("SELECT * FROM `ports` WHERE `device_id` = ? AND `ifIndex` = ?", array($device_id, $ifIndex));
}


function port_by_id_cache($port_id)
{
  global $port_cache;

  if (isset($port_cache[$port_id]) && is_array($port_cache[$device_id]))
  {
    $port = $port_cache[$port_id];
  } else {
    $port = dbFetchRow("SELECT * FROM `ports` WHERE `interface_id` = ?", array($port_id));
    $port_cache[$port_id] = $port;
  }
  return $port;
}


function get_port_by_id($port_id)
{
  if (is_numeric($port_id))
  {
    $port = dbFetchRow("SELECT * FROM `ports` WHERE `interface_id` = ?", array($port_id));
  }
  if (is_array($port))
  {
    return $port;
  } else {
    return FALSE;
  }
}

function get_application_by_id($application_id)
{
  if (is_numeric($application_id))
  {
    $application = dbFetchRow("SELECT * FROM `applications` WHERE `app_id` = ?", array($application_id));
  }
  if (is_array($application))
  {
    return $application;
  } else {
    return FALSE;
  }
}

function get_sensor_by_id($sensor_id)
{
  if (is_numeric($sensor_id))
  {
    $sensor = dbFetchRow("SELECT * FROM `sensors` WHERE `sensor_id` = ?", array($sensor_id));
  }
  if (is_array($sensor))
  {
    return $sensor;
  } else {
    return FALSE;
  }
}

function get_device_id_by_interface_id($interface_id)
{
  if (is_numeric($interface_id))
  {
    $device_id = dbFetchCell("SELECT `device_id` FROM `ports` WHERE `interface_id` = ?", array($interface_id));
  }
  if (is_numeric($device_id))
  {
    return $device_id;
  } else {
    return FALSE;
  }
}

function ifclass($ifOperStatus, $ifAdminStatus)
{
  $ifclass = "interface-upup";

  if ($ifAdminStatus == "down") { $ifclass = "interface-admindown"; }
  if ($ifAdminStatus == "up" && $ifOperStatus== "down") { $ifclass = "interface-updown"; }
  if ($ifAdminStatus == "up" && $ifOperStatus== "up") { $ifclass = "interface-upup"; }

  return $ifclass;
}

function device_by_id_cache($device_id)
{
  global $device_cache;

  if (isset($device_cache[$device_id]) && is_array($device_cache[$device_id]))
  {
    $device = $device_cache[$device_id];
  } else {
    $device = dbFetchRow("SELECT * FROM `devices` WHERE `device_id` = ?", array($device_id));
    if (get_dev_attrib($device,'override_sysLocation_bool'))
    {
      $device['real_location'] = $device['location'];
      $device['location'] = get_dev_attrib($device,'override_sysLocation_string');
    }
    $device_cache[$device_id] = $device;
  }

  return $device;
}

function truncate($substring, $max = 50, $rep = '...')
{
  if (strlen($substring) < 1){ $string = $rep; } else { $string = $substring; }
  $leave = $max - strlen ($rep);
  if (strlen($string) > $max){ return substr_replace($string, $rep, $leave); } else { return $string; }
}

function mres($string)
{ // short function wrapper because the real one is stupidly long and ugly. aestetics.
  return mysql_real_escape_string($string);
}

function getifhost($id)
{
  return dbFetchCell("SELECT `device_id` from `ports` WHERE `interface_id` = ?", array($id));
}

function gethostbyid($id)
{
  return dbFetchCell("SELECT `hostname` FROM `devices` WHERE `device_id` = ?", array($id));
}

function strgen ($length = 16)
{
  $entropy = array(0,1,2,3,4,5,6,7,8,9,'a','A','b','B','c','C','d','D','e',
  'E','f','F','g','G','h','H','i','I','j','J','k','K','l','L','m','M','n',
  'N','o','O','p','P','q','Q','r','R','s','S','t','T','u','U','v','V','w',
  'W','x','X','y','Y','z','Z');
  $string = "";

  for ($i=0; $i<$length; $i++)
  {
    $key = mt_rand(0,61);
    $string .= $entropy[$key];
  }

  return $string;
}

function getpeerhost($id)
{
  return dbFetchCell("SELECT `device_id` from `bgpPeers` WHERE `bgpPeer_id` = ?", array($id));
}

function getifindexbyid($id)
{
  return dbFetchCell("SELECT `ifIndex` FROM `ports` WHERE `interface_id` = ?", array($id));
}

function getifbyid($id)
{
  return dbFetchRow("SELECT * FROM `ports` WHERE `interface_id` = ?", array($id));
}

function getifdescrbyid($id)
{
  return dbFetchCell("SELECT `ifDescr` FROM `ports` WHERE `interface_id` = ?", array($id));
}

function getidbyname($domain)
{
  return dbFetchCell("SELECT `device_id` FROM `devices` WHERE `hostname` = ?", array($domain));
}

function gethostosbyid($id)
{
  return dbFetchCell("SELECT `os` FROM `devices` WHERE `device_id` = ?", array($id));
}

function safename($name)
{
  return preg_replace('/[^a-zA-Z0-9,._\-]/', '_', $name);
}

function zeropad($num, $length = 2)
{
  while (strlen($num) < $length)
  {
    $num = '0'.$num;
  }

  return $num;
}

function set_dev_attrib($device, $attrib_type, $attrib_value)
{
  if (dbFetchCell("SELECT COUNT(*) FROM devices_attribs WHERE `device_id` = ? AND `attrib_type` = ?", array($device['device_id'],$attrib_type)))
  {
    $return = dbUpdate(array('attrib_value' => $attrib_value), 'devices_attribs', 'device_id=? and attrib_type=?', array($device['device_id'], $attrib_type));
  }
  else
  {
    $return = dbInsert(array('device_id' => $device['device_id'], 'attrib_type' => $attrib_type, 'attrib_value' => $attrib_value), 'devices_attribs');
  }
  return $return;
}

function get_dev_attribs($device)
{
  $attribs = array();
  foreach(dbFetchRows("SELECT * FROM devices_attribs WHERE `device_id` = ?", array($device)) as $entry)
  {
    $attribs[$entry['attrib_type']] = $entry['attrib_value'];
  }
  return $attribs;
}

function get_dev_attrib($device, $attrib_type)
{
  if ($row = dbFetchRow("SELECT attrib_value FROM devices_attribs WHERE `device_id` = ? AND `attrib_type` = ?", array($device['device_id'], $attrib_type)))
  {
    return $row['attrib_value'];
  }
  else
  {
    return NULL;
  }
}

function del_dev_attrib($device, $attrib_type)
{
  return dbDelete('devices_attribs', "`device_id` = ? AND `attrib_type` = ?", array($device['device_id'], $attrib_type));
}

function formatRates($rate)
{
   $rate = format_si($rate) . "bps";
   return $rate;
}

function formatStorage($rate, $round = '2')
{
   $rate = format_bi($rate, $round) . "B";
   return $rate;
}

function format_si($rate)
{
  if ($rate >= "0.1")
  {
    $sizes = Array('', 'k', 'M', 'G', 'T', 'P', 'E');
    $round = Array('2','2','2','2','2','2','2','2','2');
    $ext = $sizes[0];
    for ($i = 1; (($i < count($sizes)) && ($rate >= 1000)); $i++) { $rate = $rate / 1000; $ext  = $sizes[$i]; }
  }
  else
  {
    $sizes = Array('', 'm', 'u', 'n');
    $round = Array('2','2','2','2');
    $ext = $sizes[0];
    for ($i = 1; (($i < count($sizes)) && ($rate != 0) && ($rate <= 0.1)); $i++) { $rate = $rate * 1000; $ext  = $sizes[$i]; }
  }

  return round($rate, $round[$i]).$ext;
}

function format_bi($size, $round = '2')
{
  $sizes = Array('', 'k', 'M', 'G', 'T', 'P', 'E');
  $ext = $sizes[0];
  for ($i = 1; (($i < count($sizes)) && ($size >= 1024)); $i++) { $size = $size / 1024; $ext  = $sizes[$i]; }
  return round($size, $round).$ext;
}

?>
