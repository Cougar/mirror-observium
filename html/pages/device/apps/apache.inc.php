<?php

global $config;

$graphs = array('apache_bits' => 'Traffic',
                'apache_hits' => 'Hits',
                'apache_cpu'  => 'CPU Utilisation',
                'apache_scoreboard' => 'Scoreboard Statistics');

foreach ($graphs as $key => $text)
{
  $graph_type = "apache_scoreboard";

  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $app['app_id'];
  $graph_array['type']   = "application_".$key;

  echo('<h4>'.$text.'</h3>');

  echo("<tr bgcolor='$row_colour'><td colspan=5>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>
