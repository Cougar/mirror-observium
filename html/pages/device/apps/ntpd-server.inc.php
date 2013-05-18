<?php

global $config;

$graphs = array('ntpdserver_stats'  => 'NTPD Server - Statistics',
                'ntpdserver_freq' => 'NTPD Server - Frequency',
                'ntpdserver_stratum' => 'NTPD Server - Stratum',
                'ntpdserver_buffer' => 'NTPD Server - Buffer',
                'ntpdserver_bits' => 'NTPD Server - Packets Sent/Received',
                'ntpdserver_packets' => 'NTPD Server - Packets Dropped/Ignored',
                'ntpdserver_uptime' => 'NTPD Server - Uptime');

foreach ($graphs as $key => $text) {
  $graph_type            = $key;
  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $app['app_id'];
  $graph_array['type']   = "application_".$key;
  echo('<h4>'.$text.'</h3>');
  echo("<tr bgcolor='$row_colour'><td colspan=5>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>
