<?php

// FIXME queries such as the one below should probably go into index.php?

foreach (dbFetchRows("SELECT * FROM `devices` ORDER BY `hostname`") as $device)
{
  if (get_dev_attrib($device,'override_sysLocation_bool'))
  {
    $device['real_location'] = $device['location'];
    $device['location'] = get_dev_attrib($device,'override_sysLocation_string');
  }

  $devices['count']++;

  $cache['devices']['hostname'][$device['hostname']] = $device['device_id'];
  $cache['devices']['id'][$device['device_id']] = $device;

  $cache['device_types'][$device['type']]++;
}

if($_SESSION['userlevel'] >= 5)
{
  $devices['up']        = dbFetchCell("SELECT COUNT(*) FROM devices  WHERE status = '1' AND `ignore` = '0'  AND `disabled` = '0'");
  $devices['down']      = dbFetchCell("SELECT COUNT(*) FROM devices WHERE status = '0' AND `ignore` = '0'  AND `disabled` = '0'");
  $devices['ignored']   = dbFetchCell("SELECT COUNT(*) FROM devices WHERE `ignore` = '1'  AND `disabled` = '0'");
  $devices['disabled']  = dbFetchCell("SELECT COUNT(*) FROM devices WHERE `disabled` = '1'");

  $ports['count']       = dbFetchCell("SELECT COUNT(*) FROM ports WHERE deleted = '0'");
  $ports['up']          = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D WHERE I.deleted = '0' AND I.ifOperStatus = 'up' AND I.ignore = '0' AND I.device_id = D.device_id AND D.ignore = '0'");
  $ports['down']        = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D WHERE I.deleted = '0' AND I.ifOperStatus = 'down' AND I.ifAdminStatus = 'up' AND I.ignore = '0' AND D.device_id = I.device_id AND D.ignore = '0'");
  $ports['shutdown']    = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D WHERE I.deleted = '0' AND I.ifAdminStatus = 'down' AND I.ignore = '0' AND D.device_id = I.device_id AND D.ignore = '0'");
  $ports['ignored']     = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D WHERE I.deleted = '0' AND D.device_id = I.device_id AND (I.ignore = '1' OR D.ignore = '1')");
  $ports['errored']     = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D WHERE I.deleted = '0' AND D.device_id = I.device_id AND (I.ignore = '0' OR D.ignore = '0') AND (I.ifInErrors_delta > '0' OR I.ifOutErrors_delta > '0')");

  $services['count']    = dbFetchCell("SELECT COUNT(service_id) FROM services");
  $services['up']       = dbFetchCell("SELECT COUNT(service_id) FROM services WHERE service_status = '1' AND service_ignore ='0'");
  $services['down']     = dbFetchCell("SELECT COUNT(service_id) FROM services WHERE service_status = '0' AND service_ignore = '0'");
  $services['ignored']  = dbFetchCell("SELECT COUNT(service_id) FROM services WHERE service_ignore = '1'");
  $services['disabled'] = dbFetchCell("SELECT COUNT(service_id) FROM services WHERE service_disabled = '1'");
}
else
{
  $devices['count']       = dbFetchCell("SELECT COUNT(D.device_id) FROM devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id", array($_SESSION['user_id']));
  $devices['up']          = dbFetchCell("SELECT COUNT(D.device_id) FROM devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND D.status = '1' AND D.ignore = '0'", array($_SESSION['user_id']));
  $devices['down']        = dbFetchCell("SELECT COUNT(D.device_id) FROM devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND D.status = '0' AND D.ignore = '0'", array($_SESSION['user_id']));
  $devices['ignored']     = dbFetchCell("SELECT COUNT(D.device_id) FROM devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND D.ignore = '1' AND D.disabled = '0'", array($_SESSION['user_id']));
  $devices['disabled']    = dbFetchCell("SELECT COUNT(D.device_id) FROM devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND D.ignore = '1'", array($_SESSION['user_id']));

  $ports['count']    = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id", array($_SESSION['user_id']));
  $ports['up']       = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND ifOperStatus = 'up'", array($_SESSION['user_id']));
  $ports['down']     = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND ifOperStatus = 'down' AND ifAdminStatus = 'up'", array($_SESSION['user_id']));
  $ports['shutdown'] = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND I.ifAdminStatus = 'down' AND I.ignore = '0'", array($_SESSION['user_id']));
  $ports['ignored']  = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND D.device_id = I.device_id AND (I.ignore = '1' OR D.ignore = '1')", array($_SESSION['user_id']));
  $ports['disabled'] = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND ifAdminStatus = 'down'", array($_SESSION['user_id']));
  $ports['errored']  = dbFetchCell("SELECT COUNT(*) FROM ports AS I, devices AS D, devices_perms AS P WHERE I.deleted = '0' AND P.user_id = ? AND P.device_id = D.device_id AND I.device_id = D.device_id AND (I.in_errors > '0' OR I.out_errors > '0')", array($_SESSION['user_id']));

  $services['count']      = dbFetchCell("SELECT COUNT(service_id) FROM services AS S, devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND S.device_id = D.device_id", array($_SESSION['user_id']));
  $services['up']         = dbFetchCell("SELECT COUNT(service_id) FROM services AS S, devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND S.device_id = D.device_id AND service_status = '1' AND service_ignore ='0'", array($_SESSION['user_id']));
  $services['down']       = dbFetchCell("SELECT COUNT(service_id) FROM services AS S, devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND S.device_id = D.device_id AND service_status = '0' AND service_ignore = '0'", array($_SESSION['user_id']));
  $services['ignored']       = dbFetchCell("SELECT COUNT(service_id) FROM services AS S, devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND S.device_id = D.device_id AND service_ignore = '1'", array($_SESSION['user_id']));
  $services['disabled']   = dbFetchCell("SELECT COUNT(service_id) FROM services AS S, devices AS D, devices_perms AS P WHERE P.user_id = ? AND P.device_id = D.device_id AND S.device_id = D.device_id AND service_disabled = '1'", array($_SESSION['user_id']));
}

if ($devices['down'])  { $devices['class'] = "error"; } else { $devices['class'] = ""; }
if ($ports['down'])    { $ports['class'] = "error"; } else { $ports['class'] = ""; }
if ($services['down']) { $services['class'] = "error"; } else { $services['class'] = ""; }

?>

<table class="table table-bordered table-condensed table-rounded" width=500 style="margin-bottom: 0;">
  <tr class="<?php echo($devices['class']); ?>">
    <td width="5"></td>
    <td><strong><a href="<?php echo(generate_url(array('page' => 'devices'))); ?>">Devices</a></strong></td>
    <td><?php echo($devices['count']) ?></td>
    <td> ( </td>
    <td style="text-align: right"> <a class="green" href="<?php echo(generate_url(array('page' => 'devices', 'status' => '1'))); ?>"><?php echo($devices['up']) ?> up</a></td>
    <td style="text-align: right"> <a class="red" href="<?php echo(generate_url(array('page' => 'devices', 'status' => '0'))); ?>"><?php echo($devices['down']) ?> down</a></td>
    <td style="text-align: right"> <a class="black" href="<?php echo(generate_url(array('page' => 'devices', 'ignore' => '1'))); ?>"><?php echo($devices['ignored']) ?> ignored</a> </td>
    <td style="text-align: right"> <a class="grey" href="<?php echo(generate_url(array('page' => 'devices', 'disabled' => '1'))); ?>"><?php echo($devices['disabled']) ?> disabled</a></td>
    <td> ) </td>
    <td width="5"></td>
  </tr>
  <tr class="<?php echo($ports['class']) ?>">
      <td width="5"></td>
      <td><strong><a href="/ports/">Ports</a></strong></td>
    <td><?php echo($ports['count']) ?></td>
    <td> ( </td>
    <td style="text-align: right"> <a class="green" href="<?php echo(generate_url(array('page' => 'ports', 'state' => 'up'))); ?>"><?php echo($ports['up']) ?> up </a></td>
    <td style="text-align: right"> <a class="red" href="<?php echo(generate_url(array('page' => 'ports', 'state' => 'down'))); ?>"><?php echo($ports['down']) ?> down </a></td>
    <td style="text-align: right"> <a class="black" href="<?php echo(generate_url(array('page' => 'ports', 'ignore' => '1'))); ?>"><?php echo($ports['ignored']) ?> ignored </a></td>
    <td style="text-align: right"> <a class="grey" href="<?php echo(generate_url(array('page' => 'ports', 'state' => 'admindown'))); ?>"><?php echo($ports['shutdown']) ?> shutdown</a></td>
    <td> ) </td>
    <td width="5"></td>
  </tr>
<?php if ($config['show_services'] && FALSE) { ?>
  <tr style="background-color: <?php echo($services['bgcolour']) ?>">
    <td width="5"></td>
    <td><strong>Services</strong></td>
    <td><?php echo($services['count']) ?></td>
    <td> ( </td>
    <td style="text-align: right"><span class="green"><?php echo($services['up']) ?> up</span></td>
    <td style="text-align: right"><span class="red"> <?php echo($services['down']) ?> down</span></td>
    <td style="text-align: right"><span class="black"> <?php echo($services['ignored']) ?> ignored</span> </td>
    <td style="text-align: right"><span class="grey"> <?php echo($services['disabled']) ?> disabled</span></td>
    <td> ) </td>
    <td width="5"></td>
  </tr>
<?php } ?>
</table>
