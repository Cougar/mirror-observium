<?php

global $config;

$graphs = array('mailscanner_sent' => 'Mailscanner - Sent / Received',
                'mailscanner_spam' => 'Mailscanner - Spam / Virus',
                'mailscanner_reject' => 'Mailscanner - Rejected / Waiting / Relayed');

foreach ($graphs as $key => $text)
{
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
