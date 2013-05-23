<?php
unset($vars['page']);

// Setup here

if(isset($_SESSION['widescreen']))
{
  $graph_width=1700;
  $thumb_width=180;
} else {
  $graph_width=1075;
  $thumb_width=113;
}

$timestamp_pattern = '/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/';
if (isset($vars['timestamp_from']) && preg_match($timestamp_pattern, $vars['timestamp_from']))
{
  $vars['from'] = strtotime($vars['timestamp_from']);
}
if (isset($vars['timestamp_to']) && preg_match($timestamp_pattern, $vars['timestamp_to']))
{
  $vars['to'] = strtotime($vars['timestamp_to']);
}
if (!is_numeric($vars['from'])) { $vars['from'] = $config['time']['day']; }
if (!is_numeric($vars['to']))   { $vars['to']   = $config['time']['now']; }

preg_match('/^(?P<type>[a-z0-9A-Z-]+)_(?P<subtype>.+)/', $vars['type'], $graphtype);

if($debug) print_r($graphtype);

$type = $graphtype['type'];
$subtype = $graphtype['subtype'];
$id = $vars['id'];

if(is_numeric($vars['device']))
{
  $device = device_by_id_cache($vars['device']);
} elseif(!empty($vars['device'])) {
  $device = device_by_name($vars['device']);
}

if (is_file("includes/graphs/".$type."/auth.inc.php"))
{
  include("includes/graphs/".$type."/auth.inc.php");
}

if (!$auth)
{
  include("includes/error-no-perm.inc.php");
} else {
  if (isset($config['graph_types'][$type][$subtype]['descr']))
  {
    $title .= " :: ".$config['graph_types'][$type][$subtype]['descr'];
  } else {
    $title .= " :: ".ucfirst($subtype);
  }

  # Load our list of available graphtypes for this object
  // FIXME not all of these are going to be valid
  if ($handle = opendir($config['html_dir'] . "/includes/graphs/".$type."/"))
  {
    while (false !== ($file = readdir($handle)))
    {
      if ($file != "." && $file != ".." && $file != "auth.inc.php" &&strstr($file, ".inc.php"))
      {
        $types[] = str_replace(".inc.php", "", $file);
      }
    }
    closedir($handle);
  }

  $graph_array = $vars;
  $graph_array['height'] = "60";
  $graph_array['width']  = $thumb_width;
  $graph_array['legend'] = "no";
  $graph_array['to']     = $config['time']['now'];

  print_optionbar_start();
  echo($title);

  echo('<div class="pull-right">');
  ?>
  <form action="" style="margin-top: -5px;">
  <select name='type' id='type' onchange="window.open(this.options[this.selectedIndex].value,'_top')" >
  <?php

  sort($types);

  foreach ($types as $avail_type)
  {
    echo("<option value='".generate_url($vars, array('type' => $type."_".$avail_type, 'page' => "graphs"))."'");
    if ($avail_type == $subtype) { echo(" selected"); }
    echo(">".nicecase($avail_type)."</option>");
  }
          ?>
    </select>
  </form>
  <?php
  echo('</div>');

  print_optionbar_end();

  // Start form for the custom range.

  print_optionbar_start();

  $thumb_array = array('sixhour' => '6 Hours',
                       'day' => '24 Hours',
                       'twoday' => '48 Hours',
                       'week' => 'One Week',
                       //'twoweek' => 'Two Weeks',
                       'month' => 'One Month',
                       //'twomonth' => 'Two Months',
                       'year' => 'One Year',
                       'twoyear' => 'Two Years'
                      );

  echo('<table width=100% style="background: transparent;"><tr>');

  foreach ($thumb_array as $period => $text)
  {
    $graph_array['from']   = $config['time'][$period];

    $link_array = $vars;
    $link_array['from'] = $graph_array['from'];
    $link_array['to'] = $graph_array['to'];
    $link_array['page'] = "graphs";
    $link = generate_url($link_array);

    echo('<td align=center>');
    echo('<span class="device-head">'.$text.'</span><br />');
    echo('<a href="'.$link.'">');
    echo(generate_graph_tag($graph_array));
    echo('</a>');
    echo('</td>');

  }

  echo('</tr></table>');

  $graph_array = $vars;
  $graph_array['height'] = "300";
  $graph_array['width']  = $graph_width;

  print_optionbar_end();

  $search = array();
  $search[] = array('type'    => 'datetime',
                    'id'      => 'timestamp',
                    'presets' => TRUE,
                    'min'     => '2007-04-03 16:06:59',  // Hehe, who will guess what this date/time means? --mike
                    'max'     => date('Y-m-d 23:59:59'), // Today
                    'from'    => $vars['timestamp_from'],
                    'to'      => $vars['timestamp_to']);
  print_search_simple($search, '', 'update');
  unset($search);

/// Run the graph to get data array out of it

$_GET = $graph_array;
$command_only = 1;
include("includes/graphs/graph.inc.php");

/// Print options navbar

$navbar = array();
$navbar['brand'] = "Options";
$navbar['class'] = "navbar-narrow";

$navbar['options']['legend']   =  array('text' => 'Show Legend', 'inverse' => TRUE);
$navbar['options']['previous'] =  array('text' => 'Graph Previous');
$navbar['options']['trend']    =  array('text' => 'Graph Trend');
$navbar['options']['max']      =  array('text' => 'Graph Maximum');

$navbar['options_right']['showcommand'] =  array('text' => 'RRD Command');

foreach(array('options' => $navbar['options'], 'options_right' => $navbar['options_right'] ) as $side => $options)
{
  foreach($options AS $option => $array)
  {
    if($array['inverse'] == TRUE)
    {
      if($vars[$option] == "no")
      {
        $navbar[$side][$option]['url'] = generate_url($vars, array('page' => "graphs", $option => NULL));
      } else {
        $navbar[$side][$option]['url'] = generate_url($vars, array('page' => "graphs", $option => 'no'));
        $navbar[$side][$option]['class'] .= " active";
      }
    } else {
      if($vars[$option] == "yes")
      {
        $navbar[$side][$option]['url'] = generate_url($vars, array('page' => "graphs", $option => NULL));
        $navbar[$side][$option]['class'] .= " active";
      } else {
        $navbar[$side][$option]['url'] = generate_url($vars, array('page' => "graphs", $option => 'yes'));
      }
    }
  }
}
print_navbar($navbar);
unset($navbar);

/// End options navbar

  echo generate_graph_js_state($graph_array);

  echo('<div style="width: '.$graph_array['width'].'; margin: auto;">');
  echo(generate_graph_tag($graph_array));
  echo("</div>");

  if (isset($graph_return['descr']))
  {

    print_optionbar_start();
    echo('<div style="float: left; width: 30px;">
          <div style="margin: auto auto;">
            <img valign=absmiddle src="images/16/information.png" />
          </div>
          </div>');
    echo($graph_return['descr']);
    print_optionbar_end();
  }

#print_r($graph_return);

  if (isset($vars['showcommand']))
  {
?>

  <div class="well info_box">
    <div id="title"><a href="device/device=12/tab=ports/">
      <i class="oicon-clock"></i> Performance & Output</a>
    </div>
    <div id="content">
      <?php echo("RRDTool Output: ".$return."<br />"); ?>
      <?php echo("<p>Total time: ".$graph_return['total_time']." | RRDtool time: ".$graph_return['rrdtool_time']."s</p>"); ?>
    </div>
  </div>


  <div class="well info_box">
    <div id="title"><a href="device/device=12/tab=ports/">
      <i class="oicon-application-terminal"></i> RRDTool Command</a>
    </div>
    <div id="content">
      <?php echo($graph_return['cmd']); ?>
    </div>
  </div>

  <div class="well info_box">
    <div id="title"><a href="device/device=12/tab=ports/">
      <i class="oicon-database"></i> RRDTool Files Used</a>
    </div>
    <div id="content">
      <?php
        foreach($graph_return['rrds'] as $rrd)
        {
          echo("$rrd <br />");
        }
      ?>
    </div>
  </div>

<?php

  }
}

?>
