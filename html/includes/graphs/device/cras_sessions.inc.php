<?php

  include("includes/graphs/common.inc.php");
  $device = device_by_id_cache($id);

  $rrd_filename = $config['rrd_dir'] . "/" . $device['hostname'] . "/cras_sessions.rrd";

  $rrd_options .= " DEF:email=$rrd_filename:email:AVERAGE";
  $rrd_options .= " DEF:ipsec=$rrd_filename:ipsec:AVERAGE";
  $rrd_options .= " DEF:l2l=$rrd_filename:l2l:AVERAGE";
  $rrd_options .= " DEF:lb=$rrd_filename:lb:AVERAGE";
  $rrd_options .= " DEF:svc=$rrd_filename:svc:AVERAGE";
  $rrd_options .= " DEF:webvpn=$rrd_filename:webvpn:AVERAGE";

  $rrd_options .= " COMMENT:'Sessions         Current    Average   Maximum\\n'";
  $rrd_options .= " LINE1.25:email#660000:'Email         '";
  $rrd_options .= " GPRINT:email:LAST:%6.2lf%s";
  $rrd_options .= " GPRINT:email:AVERAGE:\ %6.2lf%s";
  $rrd_options .= " GPRINT:email:MAX:\ %6.2lf%s\\\\n";

  $rrd_options .= " LINE1.25:ipsec#006600:'IPSEC         '";
  $rrd_options .= " GPRINT:ipsec:LAST:%6.2lf%s";
  $rrd_options .= " GPRINT:ipsec:AVERAGE:\ %6.2lf%s";
  $rrd_options .= " GPRINT:ipsec:MAX:\ %6.2lf%s\\\\n";

  $rrd_options .= " LINE1.25:l2l#000066:'Lan-to-Lan    '";
  $rrd_options .= " GPRINT:l2l:LAST:%6.2lf%s";
  $rrd_options .= " GPRINT:l2l:AVERAGE:\ %6.2lf%s";
  $rrd_options .= " GPRINT:l2l:MAX:\ %6.2lf%s\\\\n";

  $rrd_options .= " LINE1.25:lb#660066:'Load Balancer '";
  $rrd_options .= " GPRINT:lb:LAST:%6.2lf%s";
  $rrd_options .= " GPRINT:lb:AVERAGE:\ %6.2lf%s";
  $rrd_options .= " GPRINT:lb:MAX:\ %6.2lf%s\\\\n";

  $rrd_options .= " LINE1.25:svc#666600:'SSLVPN        '";
  $rrd_options .= " GPRINT:svc:LAST:%6.2lf%s";
  $rrd_options .= " GPRINT:svc:AVERAGE:\ %6.2lf%s";
  $rrd_options .= " GPRINT:svc:MAX:\ %6.2lf%s\\\\n";

  $rrd_options .= " LINE1.25:webvpn#006666:'WebVPN        '";
  $rrd_options .= " GPRINT:webvpn:LAST:%6.2lf%s";
  $rrd_options .= " GPRINT:webvpn:AVERAGE:\ %6.2lf%s";
  $rrd_options .= " GPRINT:webvpn:MAX:\ %6.2lf%s\\\\n";



?>
