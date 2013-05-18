<?php

global $config;

$graphs = array('zimbra_mtaqueue' => 'Zimbra - MTA Queue size',
                'zimbra_fdcount'  => 'Zimbra - Open file descriptors',
                'zimbra_threads'  => 'Zimbra - Threads');

foreach ($graphs as $key => $text)
{
  $graph_type            = $key;
  $graph_array['height'] = "100";
  $graph_array['width']  = "215";
  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $app['app_id'];
  $graph_array['type']   = "application_".$key;

  echo('<h4>'.$text.'</h3>');

  echo("<tr bgcolor='$row_colour'><td colspan=5>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>