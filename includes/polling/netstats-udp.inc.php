<?php

if($device[os] != "Snom") {

  echo(" UDP");

  #### These are at the start of large trees that we don't want to walk the entirety of, so we snmpget_multi them

  $oids = array ('udpInDatagrams','udpOutDatagrams','udpInErrors','udpNoPorts');

  unset($snmpstring, $rrdupdate, $snmpdata, $snmpdata_cmd, $rrd_create);
  $rrd_file = $config['rrd_dir'] . "/" . $device['hostname'] . "/netstats-udp.rrd";

    $rrd_create = "RRA:AVERAGE:0.5:1:600 RRA:AVERAGE:0.5:6:700 RRA:AVERAGE:0.5:24:775 RRA:AVERAGE:0.5:288:797 RRA:MAX:0.5:1:600 \
                    RRA:MAX:0.5:6:700 RRA:MAX:0.5:24:775 RRA:MAX:0.5:288:797";

    foreach($oids as $oid){
      $oid_ds = truncate($oid, 19, '');
      $rrd_create .= " DS:$oid_ds:COUNTER:600:U:1000000"; ## Limit to 1MPPS?
      $snmpstring .= " $oid.0";
    }

    $data = snmp_get_multi($device, $snmpstring);
    
    $rrdupdate = "N";

    foreach($oids as $oid){
      if(is_numeric($data[0][$oid])) 
      { 
        $value = $data[0][$oid]; 
      } else { 
        $value = "0"; 
      }
      $rrdupdate .= ":$value";
    }

    if(isset($data[0]['udpInDatagrams']) && isset($data[0]['udpOutDatagrams'])) {
      if(!file_exists($rrd_file)) { rrdtool_create($rrd_file, $rrd_create); }
      rrdtool_update($rrd_file, $rrdupdate);
      $graphs['netstats-udp'] = TRUE;
    }
}
unset($oids, $data, $data_array, $oid, $protos, $snmpstring);

?>
