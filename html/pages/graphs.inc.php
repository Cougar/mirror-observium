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

  echo('<div style="float: right;">');
  ?>
  <form action="">
  <select name='type' id='type'
    onchange="window.open(this.options[this.selectedIndex].value,'_top')" >
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


  // css and js for datetimepicker
  echo("
    <link type='text/css' href='css/ui-lightness/jquery-ui-1.8.18.custom.css' rel='stylesheet' />
    <script type='text/javascript' src='js/jquery-ui.min.js'></script>
    <script type='text/javascript' src='js/jquery-ui-timepicker-addon.js'></script>
    <script type='text/javascript' src='js/jquery-ui-sliderAccess.js'></script>
    <script type='text/javascript'>
      $(function()
      {
        $('#dtpickerfrom').datetimepicker({
          showOn: 'button',
          buttonImage: 'images/16/date.png',
          buttonImageOnly: true,
          dateFormat: 'yy-mm-dd',
          hourGrid: 4,
          minuteGrid: 10,
          onClose: function(dateText, inst) {
            var toDateTextBox = $('#dtpickerto');
            if (toDateTextBox.val() != '') {
              var testStartDate = new Date(dateText);
              var testEndDate = new Date(toDateTextBox.val());
              if (testStartDate > testEndDate)
                toDateTextBox.val(dateText);
            }
            else {
              toDateTextBox.val(dateText);
            }
          },
          onSelect: function (selectedDateTime) {
            var toDateTextBox = $('#dtpickerto');
            var toValue = toDateTextBox.val();
            var start = $(this).datetimepicker('getDate');
            toDateTextBox.datetimepicker('option', 'minDate', new Date(start.getTime()));
            // we do this so the above datetimepicker call doesn't strip the time from the pre-set value in the text box.
            toDateTextBox.val(toValue);
          }
        });
        $('#dtpickerto').datetimepicker({
          showOn: 'button',
          buttonImage: 'images/16/date.png',
          buttonImageOnly: true,
          dateFormat: 'yy-mm-dd',
          hourGrid: 4,
          minuteGrid: 10,
          maxDate: 0,
          onClose: function(dateText, inst) {
            var startDateTextBox = $('#dtpickerfrom');
            if (startDateTextBox.val() != '') {
              var testStartDate = new Date(startDateTextBox.val());
              var testEndDate = new Date(dateText);
                if (testStartDate > testEndDate)
                  startDateTextBox.val(dateText);
            }
            else {
              startDateTextBox.val(dateText);
            }
          },
          onSelect: function (selectedDateTime) {
            var fromDateTextBox = $('#dtpickerfrom');
            var fromValue = fromDateTextBox.val();
            var end = $(this).datetimepicker('getDate');
            fromDateTextBox.datetimepicker('option', 'maxDate', new Date(end.getTime()) );
            // we do this so the above datetimepicker call doesn't strip the time from the pre-set value in the text box.
            fromDateTextBox.val(fromValue);
          }
        });
      });

      function submitCustomRange(frmdata) {
        var reto = /to=([0-9])+/g;
        var refrom = /from=([0-9])+/g;
        var tsto = new Date(frmdata.dtpickerto.value.replace(' ','T'));
        var tsfrom = new Date(frmdata.dtpickerfrom.value.replace(' ','T'));
        tsto = tsto.getTimezoneOffset() * 60 + tsto.getTime() / 1000;
        tsfrom = tsfrom.getTimezoneOffset() * 60 + tsfrom.getTime() / 1000;
        frmdata.selfaction.value = frmdata.selfaction.value.replace(reto, 'to=' + tsto);
        frmdata.selfaction.value = frmdata.selfaction.value.replace(refrom, 'from=' + tsfrom);
        frmdata.action = frmdata.selfaction.value
        return true;
      }

      function applyPreset(frmdata) {
        var link = frmdata.preset.value;
        document.location = link;
      }
    </script>
    <style type='text/css'>
      /* css for timepicker */
      .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
      .ui-timepicker-div dl { text-align: left; }
      .ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
      .ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
      .ui-timepicker-div td { font-size: 90%; }
      .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
    </style>
  ");

  // Start form for the custom range.
  echo('<form id="customrange" action="test">');


  print_optionbar_start();

  $thumb_array = array('sixhour' => '6 Hours', 'day' => '24 Hours', 'twoday' => '48 Hours', 'week' => 'One Week', 'twoweek' => 'Two Weeks',
                       'month' => 'One Month', 'twomonth' => 'Two Months','year' => 'One Year', 'twoyear' => 'Two Years');

  echo('<table width=100%><tr>');

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

  echo("<hr />");

  // datetime range picker
  echo("<input type=hidden id='selfaction' value='" . $_SERVER['REQUEST_URI'] . "'>");

  $preset_array = array("today", "yesterday", "tweek", "lweek", "tmonth", "lmonth", "tyear", "lyear");
  function presetDate($preset) {
    switch($preset) {
      case 'today':
        $tsto = mktime(23, 59 ,59, date("m"), date("d"), date("Y"));
        $tsfrom = mktime(0, 0 ,0, date("m"), date("d"), date("Y"));
        $text = "Today";
        break;
      case 'yesterday':
        $tsto = mktime(23, 59 ,59, date("m"), date("d")-1, date("Y"));
        $tsfrom = mktime(0, 0 ,0, date("m"), date("d")-1, date("Y"));
        $text = "Yesterday";
        break;
      case 'tweek':
        $tsto = strtotime(date("Y-m-d 23:59", strtotime("next week")));
        $tsfrom = strtotime(date("Y-m-d 00:00", strtotime("this week")));
        $text = "This week";
        break;
      case 'lweek':
        $tsto = strtotime(date("Y-m-d 23:59", strtotime("this week")));
        $tsfrom = strtotime(date("Y-m-d 00:00", strtotime("last week")));
        $text = "Last week";
        break;
      case 'tmonth':
        $tsto = mktime(23, 59 ,59, date("m")+1, 0, date("Y"));
        $tsfrom = mktime(0, 0 ,0, date("m"), 1, date("Y"));
        $text = "This month";
        break;
      case 'lmonth':
        $tsto = mktime(23, 59 ,59, date("m"), 0, date("Y"));
        $tsfrom = mktime(0, 0 ,0, date("m")-1, 1, date("Y"));
        $text = "Last month";
        break;
      case 'tyear':
        $tsto = mktime(23, 59 ,59, 13, 0, date("Y"));
        $tsfrom = mktime(0, 0 ,0, 1, 1, date("Y"));
        $text = "This year";
        break;
      case 'lyear':
        $tsto = mktime(23, 59 ,59, 13, 0, date("Y")-1);
        $tsfrom = mktime(0, 0 ,0, 1, 1, date("Y")-1);
        $text = "Last year";
        break;
    }
    $res = array("from" => $tsfrom, "to" => $tsto, "desc" => $text);
    return $res;
  }
  echo("
    <strong>Presets:</strong>
    <select name=\"preset\" onchange=\"applyPreset(this.form);\">
      <option value=\"\">Select preset</option>");
  foreach ($preset_array as $item=>$value) {
    $preset = presetDate($value);
    $link_array = $vars;
    $link_array['from'] = $preset['from'];
    $link_array['to'] = $preset['to'];
    $link_array['page'] = "graphs";
    $link = generate_url($link_array);
    echo("<option value=\"".$link."\">".$preset['desc']."</option>");
  }
  echo("
    </select>
    <strong>From:</strong> <input type='text' id='dtpickerfrom' maxlength=16 value='" . date('Y-m-d H:i', $graph_array['from']) . "'>
    <strong>To:</strong> <input type='text' id='dtpickerto' maxlength=16 value='" . date('Y-m-d H:i', $graph_array['to']) . "'>
    <input class='btn' type='submit' id='submit' value='Update' onclick='javascript:submitCustomRange(this.form);'>
  ");

  print_optionbar_end();

  echo('</form>');

/// Print options navbar

$navbar = array();
$navbar['brand'] = "Options";
$navbar['class'] = "navbar-narrow";

$navbar['options']['legend']   =  array('text' => 'Show Legend', 'inverse' => TRUE);
$navbar['options']['previous'] =  array('text' => 'Graph Previous');
$navbar['options']['trend'] =  array('text' => 'Graph Trend');
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

  if (isset($config['graph_descr'][$vars['type']]))
  {

    print_optionbar_start();
    echo('<div style="float: left; width: 30px;">
          <div style="margin: auto auto;">
            <img valign=absmiddle src="images/16/information.png" />
          </div>
          </div>');
    echo($config['graph_descr'][$vars['type']]);
    print_optionbar_end();
  }

  if (isset($vars['showcommand']))
  {
    $_GET = $graph_array;
    $command_only = 1;

    include("includes/graphs/graph.inc.php");
  }
}

?>
