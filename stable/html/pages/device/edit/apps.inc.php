<?php

# Load our list of available applications
if ($handle = opendir($config['install_dir'] . "/includes/polling/applications/"))
{
  while (false !== ($file = readdir($handle)))
  {
    if ($file != "." && $file != ".." && strstr($file, ".inc.php"))
    {
      $applications[] = str_replace(".inc.php", "", $file);
    }
  }
  closedir($handle);
}

# Check if the form was POSTed
if ($_POST['device'])
{
  $updated = 0;
  $param[] = $device['device_id'];
  foreach (array_keys($_POST) as $key)
  {
    if (substr($key,0,4) == 'app_')
    {
      $param[] = substr($key,4);
      $enabled[] = substr($key,4);
      $replace[] = "?";
    }
  }

  if (count($enabled)) {
    $updated += dbDelete('applications', "`device_id` = ? AND `app_type` NOT IN (".implode(",",$replace).")", $param);
  } else {
    $updated += dbDelete('applications', "`device_id` = ?", array($param));
  }

  foreach (dbFetchRows( "SELECT `app_type` FROM `applications` WHERE `device_id` = ?", array($device['device_id'])) as $row)
  {
    $app_in_db[] = $row['app_type'];
  }

  foreach ($enabled as $app)
  {
    if (!in_array($app,$app_in_db))
    {
      $updated += dbInsert(array('device_id' => $device['device_id'], 'app_type' => $app), 'applications');
    }
  }

  if ($updated)
  {
    print_message("Applications updated!");
  }
  else
  {
    print_message("No changes.");
  }
}

# Show list of apps with checkboxes
echo('<div style="padding: 10px;">');

$apps_enabled = dbFetchRows("SELECT * from `applications` WHERE `device_id` = ? ORDER BY app_type", array($device['device_id']));
if (count($apps_enabled))
{
  foreach ($apps_enabled as $application)
  {
    $app_enabled[] = $application['app_type'];
  }
}

echo("<div style='float: left; width: 100%'>
<form id='appedit' name='appedit' method='post' action=''>
  <input type=hidden name=device value='".$device['device_id']."'>
  <table cellpadding=3 cellspacing=0 width=100%>
    <tr align=center>
      <th width=100>Enable</th>
      <th align=left>Application</th>
    </tr>
");

$row = 1;

foreach ($applications as $app)
{
  if (is_integer($row/2)) { $row_colour = $list_colour_a; } else { $row_colour = $list_colour_b; }

  echo("    <tr bgcolor=$row_colour>");
  echo("      <td align=center>");
  echo("        <input type=checkbox" . (in_array($app,$app_enabled) ? ' checked="1"' : '') . " name='app_". $app ."'>");
  echo("      </td>");
  echo("      <td align=left>". ucfirst($app) . "</td>");
  echo("    </tr>
");

  $row++;
}

echo('<tr><td></td><td><input type="submit" value="Save"></td></tr>');
echo('</table>');
echo('</form>');
echo('</div>');

?>
