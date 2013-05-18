<?php

// Set Defaults here

if(!isset($vars['format'])) { $vars['format'] = "detail"; }
if (!$config['web_show_disabled'] && !isset($vars['disabled'])) { $vars['disabled'] = '0'; }

/// FIXME - new style of searching here

$sql_param = array();
$where = ' WHERE 1 ';
foreach ($vars as $var => $value)
{
  if ($value != '')
  {
    switch ($var)
    {
      case 'hostname':
        $where .= ' AND `hostname` LIKE ?';
        $sql_param[] = '%'.$value.'%';
        break;
      case 'sysname':
        $where .= ' AND `sysName` LIKE ?';
        $sql_param[] = '%'.$value.'%';
        break;
      case 'os':
      case 'version':
      case 'hardware':
      case 'features':
      case 'type':
      case 'status':
      case 'ignore':
      case 'disabled':
      case 'location_country':
      case 'location_state':
      case 'location_county':
      case 'location_city':
      case 'location':
        $where .= ' AND `'.$var.'` = ?';
        $sql_param[] = $value;
        break;
    }
  }
}

$pagetitle[] = "Devices";

echo('<div class="well" style="padding: 10px;">');

if($vars['searchbar'] != "hide")
{

?>

<form method="post" action="" style="margin-bottom: 0;">
  <table style="width: 100%" class="table-transparent">
    <tr>
      <td width="290">
        <div class="input-prepend" style="margin-right: 3px; margin-bottom: 10px;">
          <span class="add-on" style="width: 80px;">Hostname</span>
          <input type="text" name="hostname" id="hostname" class="input" value="<?php echo($vars['hostname']); ?>" />
        </div>

        <div class="input-prepend" style="margin-right: 3px;  margin-bottom: 10px;">
          <span class="add-on" style="width: 80px;">sysName</span>
          <input type="text" name="sysname" id="sysname" class="input" value="<?php echo($vars['sysname']); ?>" />
        </div>

      </td>
      <td width="200">
        <select name='os' id='os'>
          <option value=''>All OSes</option>
          <?php

$where_form = ($config['web_show_disabled']) ? '' : 'AND disabled = 0';
foreach (dbFetch('SELECT `os` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `os` ORDER BY `os`') as $data)
{
  if ($data['os'])
  {
    echo("<option value='".$data['os']."'");
    if ($data['os'] == $vars['os']) { echo(" selected"); }
    echo(">".$config['os'][$data['os']]['text']."</option>");
  }
}
          ?>
        </select>
        <br />
        <select name='version' id='version'>
          <option value=''>All Versions</option>
          <?php

foreach (dbFetch('SELECT `version` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `version` ORDER BY `version`') as $data)
{
  if ($data['version'])
  {
    echo("<option value='".$data['version']."'");
    if ($data['version'] == $vars['version']) { echo(" selected"); }
    echo(">".$data['version']."</option>");
  }
}
          ?>
        </select>
      </td>
      <td width="200">
        <select name="hardware" id="hardware">
          <option value="">All Platforms</option>
          <?php
foreach (dbFetch('SELECT `hardware` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `hardware` ORDER BY `hardware`') as $data)
{
  if ($data['hardware'])
  {
    echo('<option value="'.$data['hardware'].'"');
    if ($data['hardware'] == $vars['hardware']) { echo(" selected"); }
    echo(">".$data['hardware']."</option>");
  }
}
          ?>
        </select>
        <br />
        <select name="features" id="features">
          <option value="">All Featuresets</option>
          <?php

foreach (dbFetch('SELECT `features` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `features` ORDER BY `features`') as $data)
{
  if ($data['features'])
  {
    echo('<option value="'.$data['features'].'"');
    if ($data['features'] == $vars['features']) { echo(" selected"); }
    echo(">".$data['features']."</option>");
  }
}
          ?>
        </select>
      </td>
      <td>
        <select name="location" id="location">
          <option value="">All Locations</option>
          <?php
// fix me function?

foreach (getlocations() as $location) // FIXME function name sucks maybe get_locations ?
{
  if ($location)
  {
    echo('<option value="'.$location.'"');
    if ($location == $vars['location']) { echo(" selected"); }
    echo(">".$location."</option>");
  }
}
?>
        </select>
<br />
        <select name="type" id="type">
          <option value="">All Device Types</option>
          <?php

foreach (dbFetch('SELECT `type` FROM `devices` AS D WHERE 1 '.$where_form.' GROUP BY `type` ORDER BY `type`') as $data)
{
  if ($data['type'])
  {
    echo("<option value='".$data['type']."'");
    if ($data['type'] == $vars['type']) { echo(" selected"); }
    echo(">".ucfirst($data['type'])."</option>");
  }
}
          ?>
        </select>

      </td>
      <td align="center">
        <button type="submit" class="btn btn-large"><i class="icon-search"></i> Search</button>
        <br />
        <a href="<?php echo(generate_url($vars)); ?>" title="Update the browser URL to reflect the search criteria." >Update URL</a> |
        <a href="<?php echo(generate_url(array('page' => 'devices', 'section' => $vars['section'], 'bare' => $vars['bare']))); ?>" title="Reset critera to default." >Reset</a>
      </td>
    </tr>
  </table>
</form>

<hr style="margin: 0px 0px 10px 0px;">

<?php

}

echo('<span style="font-weight: bold;">Lists</span> &#187; ');

$menu_options = array('basic'      => 'Basic',
                      'detail'     => 'Detail',
                      'status'     => 'Status');

$sep = "";
foreach ($menu_options as $option => $text)
{
  echo($sep);
  if ($vars['format'] == $option)
  {
    echo("<span class='pagemenu-selected'>");
  }
  echo('<a href="' . generate_url($vars, array('format' => $option)) . '">' . $text . '</a>');
  if ($vars['format'] == $option)
  {
    echo("</span>");
  }
  $sep = " | ";
}

?>

 |

<span style="font-weight: bold;">Graphs</span> &#187;

<?php

$menu_options = array('bits'      => 'Bits',
                      'processor' => 'CPU',
                      'mempool'   => 'Memory',
                      'uptime'    => 'Uptime',
                      'storage'   => 'Storage',
                      'diskio'    => 'Disk I/O'
                      );
$sep = "";
foreach ($menu_options as $option => $text)
{
  echo($sep);
  if ($vars['format'] == 'graph_'.$option)
  {
    echo("<span class='pagemenu-selected'>");
  }
  echo('<a href="' . generate_url($vars, array('format' => 'graph_'.$option)) . '">' . $text . '</a>');
  if ($vars['format'] == 'graph_'.$option)
  {
    echo("</span>");
  }
  $sep = " | ";
}

?>

<div style="float: right;">

<?php

  if ($vars['searchbar'] == "hide")
  {
    echo('<a href="'. generate_url($vars, array('searchbar' => '')).'">Restore Search</a>');
  } else {
    echo('<a href="'. generate_url($vars, array('searchbar' => 'hide')).'">Remove Search</a>');
  }

  echo("  | ");

  if ($vars['bare'] == "yes")
  {
    echo('<a href="'. generate_url($vars, array('bare' => '')).'">Restore Header</a>');
  } else {
    echo('<a href="'. generate_url($vars, array('bare' => 'yes')).'">Remove Header</a>');
  }

?>

  </div>
</div>

<?php

$query = "SELECT * FROM `devices` " . $where . " ORDER BY hostname";

list($format, $subformat) = explode("_", $vars['format']);

$devices = dbFetchRows($query, $sql_param);

if(count($devices))
{
  if (file_exists('pages/devices/'.$format.'.inc.php'))
  {
    include('pages/devices/'.$format.'.inc.php');
  } else {
?>

<div class="alert alert-error">
  <h4>Error</h4>
  This should not happen. Please ensure you are on the latest release and then report this to the Observium developers if it continues.
</div>

<?php
  }

} else {

?>
<div class="alert alert-error">
  <h4>No devices found</h4>
  Please try adjusting your search parameters.
</div>

<?php
}
