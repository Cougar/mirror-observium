<?php

if ($_POST['editing'])
{
  if ($_SESSION['userlevel'] > "7")
  {
    $updated = 0;

    $descr = mres($_POST['descr']);
    $ignore = mres($_POST['ignore']);
    $type = mres($_POST['type']);
    $disabled = mres($_POST['disabled']);

    $override_sysLocation_bool = mres($_POST['override_sysLocation']);
    if (isset($_POST['sysLocation'])) { $override_sysLocation_string = mres($_POST['sysLocation']); }

    if (get_dev_attrib($device,'override_sysLocation_bool') != $override_sysLocation_bool
     || get_dev_attrib($device,'override_sysLocation_string') != $override_sysLocation_string)
    {
      $updated = 1;
    }

    if ($override_sysLocation_bool) { set_dev_attrib($device, 'override_sysLocation_bool', '1'); } else { del_dev_attrib($device, 'override_sysLocation_bool'); }
    if (isset($override_sysLocation_string)) { set_dev_attrib($device, 'override_sysLocation_string', $override_sysLocation_string); };

    #FIXME needs more sanity checking! and better feedback
    $sql = "UPDATE `devices` SET `purpose` = '" . $descr . "', `type` = '$type'";
    $sql .= ", `ignore` = '$ignore',  `disabled` = '$disabled'";
    $sql .= " WHERE `device_id` = '".$device['device_id']."'";
    $query = mysql_query($sql);

    $rows_updated = mysql_affected_rows();

    if ($rows_updated > 0 || $updated)
    {
      $update_message = "Device record updated.";
      $updated = 1;
    } elseif ($rows_updated = '-1') {
      $update_message = "Device record unchanged. No update necessary.";
      $updated = -1;
    } else {
      $update_message = "Device record update error.";
    }
  }
  else
  {
    include("includes/error-no-perm.inc.php");
  }
}

$descr  = $device['purpose'];

$override_sysLocation_bool = get_dev_attrib($device,'override_sysLocation_bool');
$override_sysLocation_string = get_dev_attrib($device,'override_sysLocation_string');

if ($updated && $update_message)
{
  print_message($update_message);
} elseif ($update_message) {
  print_error($update_message);
}

?>
<table cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <h5>
        <form id="delete_host" name="delete_host" method="post" action="<?php echo($config['base_url'].'/delhost/'); ?>">
          <img src="images/16/server_delete.png" align="absmiddle">
          <input type="hidden" name="id" value="<?php echo($device['device_id']); ?>">
          <input type="submit" class="submit" name="Submit" value="Delete Host">
        </form>
      </h5>

<form id="edit" name="edit" method="post" action="">
  <input type=hidden name="editing" value="yes">
  <table width="500" border="0">
    <tr>
      <td colspan="2" align="right">Description:</td>
      <td colspan="3"><input name="descr" size="32" value="<?php echo($device['purpose']); ?>"></input></td>
    </tr>
   <tr>
      <td colspan="2" align="right">
        Type:
      </td>
      <td>
        <select name="type">
<?php
$unknown = 1;
foreach ($device_types as $type)
{
  echo('          <option value="'.$type.'"');
  if ($device['type'] == $type)
  {
    echo('selected="1"');
    $unknown = 0;
  }
  echo(' >' . ucfirst($type) . '</option>');
}
  if ($unknown)
  {
    echo('          <option value="other">Other</option>');
  }
?>
        </select>
      </td>
    </tr>
    <tr>
      <td width="40"><div style="padding-right: 5px; text-align: right"><input onclick="edit.sysLocation.disabled=!edit.override_sysLocation.checked" type="checkbox" name="override_sysLocation"<?php if ($override_sysLocation_bool) { echo(' checked="1"'); } ?> /></div></td>
      <td width="150" align="right">Override sysLocation:</td>
      <td><input name="sysLocation" size="32"<?php if (!$override_sysLocation_bool) { echo(' disabled="1"'); } ?> value="<?php echo($override_sysLocation_string); ?>" /></td>
    </tr>
    <tr>
      <td colspan="2"><div align="right">Disable</div></td>
      <td><input name="disabled" type="checkbox" id="disabled" value="1" <?php if ($device["disabled"]) { echo("checked=checked"); } ?> /></td>
      <td><div align="right">Ignore</div></td>
      <td><input name="ignore" type="checkbox" id="disable" value="1" <?php if ($device['ignore']) { echo("checked=checked"); } ?> /></td>
    </tr>
  </table>
  <input type="submit" name="Submit" value="Save" />
  <label><br />
  </label>
</form>

</td>
<td width="50"></td><td></td></tr></table>
