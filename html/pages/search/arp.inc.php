<?php print_optionbar_start(28); ?>

  <form method="post" action="" class="form-inline">
      <span style="font-weight: bold;">ARP Search</span> &#187;
      <div class="input-prepend" style="margin-right: 3px;">
        <span class="add-on">Device</span>
        <select name="device_id" id="device_id">
          <option value="">All Devices</option>
<?php

$pagetitle[] = "ARP Search";

//ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
//error_reporting(E_ALL);

// Select the devices only with ARP tables
foreach (dbFetchRows("SELECT D.device_id AS device_id, `hostname` FROM `ipv4_mac` AS M, `ports` AS P, `devices` AS D WHERE M.port_id = P.port_id AND P.device_id = D.device_id GROUP BY `device_id` ORDER BY `hostname`;") as $data)
{
  echo('<option value="'.$data['device_id'].'"');
  if ($data['device_id'] == $_POST['device_id']) { echo("selected"); }
  echo(">".$data['hostname']."</option>");
}
?>
        </select>
      </div>
      <div class="input-prepend" style="margin-right: 3px;">
        <span class="add-on">Search By</span>
        <select name="searchby" id="searchby">
        <option value="mac" <?php if ($_POST['searchby'] != "ip") { echo("selected"); } ?> >MAC Address</option>
        <option value="ip" <?php if ($_POST['searchby'] == "ip") { echo("selected"); } ?> >IP Address</option>
        </select>
     </div>
     <div class="input-prepend" style="margin-right: 3px;">
       <span class="add-on">String</span>
       <input type="text" name="address" id="address" size=40 value="<?php echo($_POST['address']); ?>" />
     </div>
    <button type="submit" class="btn"><i class="icon-search"></i> Search</button>
  </form>

<?php

print_optionbar_end();

echo("<table class=\"table table-striped table-condensed\" style=\"margin-top: 10px;\">\n");

$query = "SELECT * FROM `ipv4_mac` AS M, `ports` AS P, `devices` AS D WHERE M.port_id = P.port_id AND P.device_id = D.device_id ";
if (isset($_POST['searchby']) && $_POST['searchby'] == "ip")
{
  $query .= " AND `ipv4_address` LIKE ?";
  $param = array("%".trim($_POST['address'])."%");
} else {
  $query .= " AND `mac_address` LIKE ?";
  $param = array("%".str_replace(array(':', ' ', '-', '.', '0x'),'',mres($_POST['address']))."%");
}

if (is_numeric($_POST['device_id']))
{
  $query  .= " AND P.device_id = ?";
  $param[] = $_POST['device_id'];
}
$query .= " ORDER BY M.mac_address";

echo('<thead><tr><th>MAC Address</th><th>IP Address</th><th>Device</th><th>Interface</th><th>Remote device</th><th>Remote interface</th></tr></thead>');
foreach (dbFetchRows($query, $param) as $entry)
{
  if (!$ignore)
  {
    //why are they here for?
    //$speed = humanspeed($entry['ifSpeed']);
    //$type = humanmedia($entry['ifType']);

    if ($entry['ifInErrors'] > 0 || $entry['ifOutErrors'] > 0)
    {
      $error_img = generate_port_link($entry,"<img src='images/16/chart_curve_error.png' alt='Interface Errors' border=0>",errors);
    } else { $error_img = ""; }

    $arp_host = dbFetchRow("SELECT * FROM ipv4_addresses AS A, ports AS I, devices AS D WHERE A.ipv4_address = ? AND I.port_id = A.port_id AND D.device_id = I.device_id", array($entry['ipv4_address']));
    if ($arp_host) { $arp_name = generate_device_link($arp_host); } else { unset($arp_name); }
    if ($arp_host) { $arp_if = generate_port_link($arp_host); } else { unset($arp_if); }
    if ($arp_host['device_id'] == $entry['device_id']) { $arp_name = "Localhost"; }
    if ($arp_host['port_id'] == $entry['port_id']) { $arp_if = "Local port"; }

    echo('<tr>
        <td width="160">' . formatMac($entry['mac_address']) . '</td>
        <td width="140">' . $entry['ipv4_address'] . '</td>
            <td width="200" class="list-bold">' . generate_device_link($entry) . '</td>
        <td class="list-bold">' . generate_port_link($entry, makeshortif(fixifname($entry['ifDescr']))) . ' ' . $error_img . '</td>
            <td width="200">'.$arp_name.'</td>
        <td class="list-bold">'.$arp_if.'</td>
            </tr>');
  }

  unset($ignore);
}

echo("</table>");

?>
