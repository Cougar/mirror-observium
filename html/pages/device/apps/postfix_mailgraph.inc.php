<?php

global $config;

$graphs = array('postfix_sent' => 'Postfix - Sent / Received',
                'postfix_spam' => 'Postfix - Spam / Virus',
                'postfix_reject' => 'Postfix - Rejected / Bounced');

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
