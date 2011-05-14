
<?php print_optionbar_start('25'); ?>

<form method="post" action="">
  <label><strong>Descr</strong>
    <input type="text" name="string" id="string" value="<?php echo($_POST['string']); ?>" />
  </label>
  <label>
    <strong>Part No</strong>
    <select name="part" id="part">
      <option value="">All Parts</option>
      <?php
        foreach (dbFetchRows("SELECT `entPhysicalModelName` FROM `entPhysical` GROUP BY `entPhysicalModelName` ORDER BY `entPhysicalModelName`") as $data)
        {
          echo("<option value='".$data['entPhysicalModelName']."'");
          if ($data['entPhysicalModelName'] == $_POST['part']) { echo("selected"); }
          echo(">".$data['entPhysicalModelName']."</option>");
        }
      ?>
    </select>
  </label>
  <label><strong>Serial</strong>
    <input type="text" name="serial" id="serial" value="<?php echo($_POST['serial']); ?>" />
  </label>
  <label>
    <strong>Device</strong>
    <select name="device" id="device">
      <option value="">All Devices</option>
      <?php
        foreach (dbFetchRows("SELECT * FROM `devices` ORDER BY `hostname`") as $data)
        {
          echo("<option value='".$data['device_id']."'");

          if ($data['device_id'] == $_POST['device']) { echo("selected"); }

          echo(">".$data['hostname']."</option>");
        }
      ?>
    </select>
  </label>
  <input type="text" size=24 name="device_string" id="device_string" value="<?php if($_POST['device_string']) { echo($_POST['device_string']); } ?>" />
  <input style type=submit class=submit value=Search>

<?php

print_optionbar_end();

$param = array();

if ($_SESSION['userlevel'] >= '5')
{
  $sql = "SELECT * from entPhysical AS E, devices AS D WHERE D.device_id = E.device_id";
} else {
  $sql = "SELECT * from entPhysical AS E, devices AS D, devices_perms AS P WHERE D.device_id = E.device_id AND P.device_id = D.device_id AND P.user_id = ?";
  $param[] = $_SESSION['user_id'];
}

if (isset($_POST['string']) && strlen($_POST['string']))
{
  $sql  .= " AND E.entPhysicalDescr LIKE ?";
  $param[] = "%".$_POST['string']."%"; 
}

if (isset($_POST['device_string']) && strlen($_POST['device_string']))
{
  $sql .= " AND D.hostname LIKE ?";
  $param[] = "%".$_POST['device_string']."%";
}

if (isset($_POST['part']) && strlen($_POST['part']))
{
  $sql .= " AND E.entPhysicalModelName = ?";
  $param[] = $_POST['part'];
}

if (isset($_POST['serial']) && strlen($_POST['serial']))
{
  $sql .= " AND E.entPhysicalSerialNum LIKE ?";
  $param[] = "%".$_POST['serial']."%";
}

if (isset($_POST['device']) && is_numeric($_POST['device']))
{
  $sql .= " AND D.device_id = ?";
  $param[] = $_POST['device'];
}

echo("<table cellspacing=0 cellpadding=2 width=100%>");
echo("<tr><th>Hostname</th><th>Description</th><th>Name</th><th>Part No</th><th>Serial No</th></tr>");

foreach (dbFetchRows($sql, $param) as $entry)
{
  if ($bg == $list_colour_a) { $bg = $list_colour_b; } else { $bg=$list_colour_a; }
  echo("<tr style=\"background-color: $bg\"><td>" . generate_device_link($entry, shortHost($entry['hostname'])) . "</td><td>" . $entry['entPhysicalDescr']  .
     "</td><td>" . $entry['entPhysicalName']  . "</td><td>" . $entry['entPhysicalModelName']  . "</td><td>" . $entry['entPhysicalSerialNum'] . "</td></tr>");
}
echo("</table>");

?>
</table>
