<?php

if ($port_stats[$port['ifIndex']] && $port['ifType'] == "ethernetCsmacd"
   && isset($port_stats[$port['ifIndex']]['dot3StatsIndex']))
{ // Check to make sure Port data is cached.

  $this_port = &$port_stats[$port[ifIndex]];

  $old_rrdfile = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("etherlike-".$port['ifIndex'].".rrd");
  $rrdfile = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("port-".$port['ifIndex']."-dot3.rrd");

  $rrd_create .= "RRA:AVERAGE:0.5:1:600 RRA:AVERAGE:0.5:6:700 RRA:AVERAGE:0.5:24:775 RRA:AVERAGE:0.5:288:797 RRA:MAX:0.5:1:600 \
                  RRA:MAX:0.5:6:700 RRA:MAX:0.5:24:775 RRA:MAX:0.5:288:797";

  if (!file_exists($rrdfile))
  {
    if (file_exists($old_rrdfile))
    {
      rename($old_rrdfile,$rrd_file);
    }
    else
    {
      foreach ($etherlike_oids as $oid)
      {
        $oid = truncate(str_replace("dot3Stats", "", $oid), 19, '');
        $rrd_create .= " DS:$oid:COUNTER:600:U:100000000000";
      }
      rrd_create($rrdfile, $rrd_create);
    }
  }

  $rrdupdate = "N";
  foreach ($etherlike_oids as $oid)
  {
    $data = $this_port[$oid] + 0;
    $rrdupdate .= ":$data";
  }
  rrdtool_update($rrdfile, $rrdupdate);

  echo("EtherLike ");
}

?>