<?php

# FIXME should come from somewhere else; these are also kind of duplicated more eloquently in device/apps
$graphs['apache']     = array('bits', 'hits', 'scoreboard', 'cpu');
$graphs['drbd']       = array('disk_bits', 'network_bits', 'queue', 'unsynced');
$graphs['mysql']      = array('network_traffic', 'connections', 'command_counters', 'select_types');
$graphs['memcached']  = array('bits', 'commands', 'data', 'items');
$graphs['powerdns']   = array('recursing', 'queries', 'querycache', 'latency');
$graphs['ntpd']       = array('stats', 'freq', 'stratum', 'bits');
$graphs['postgresql'] = array('xact', 'blks', 'tuples', 'tuples_query');
$graphs['shoutcast']  = array('multi_stats', 'multi_bits');
$graphs['nginx']      = array('connections', 'req');
# FIXME ^ recursing should be replaced by something else probably.

print_optionbar_start();

echo("<span style='font-weight: bold;'>Apps</span> &#187; ");

unset($sep);

$link_array = array('page'    => 'device',
                    'device'  => $device['device_id'],
                    'tab' => 'apps');

foreach ($app_list as $app)
{
  echo($sep);

#  if (!$vars['app']) { $vars['app'] = $app['app_type']; }

  if ($vars['app'] == $app['app_type'])
  {
    echo("<span class='pagemenu-selected'>");
    #echo('<img src="images/icons/'.$app['app_type'].'.png" class="optionicon" />');
  } else {
    #echo('<img src="images/icons/greyscale/'.$app['app_type'].'.png" class="optionicon" />');
  }
  echo(generate_link(nicecase($app['app_type']),array('page'=>'apps','app'=>$app['app_type'])));
  if ($vars['app'] == $app['app_type']) { echo("</span>"); }
  $sep = " | ";
}

print_optionbar_end();

if($vars['app'])
{
  if (is_file("pages/apps/".mres($vars['app']).".inc.php"))
  {
    include("pages/apps/".mres($vars['app']).".inc.php");
  } else {
    include("pages/apps/default.inc.php");
  }
} else {
  include("pages/apps/overview.inc.php");
}

$pagetitle[] = "Apps";
?>
