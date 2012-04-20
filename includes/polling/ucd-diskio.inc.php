<?php

$diskio_data = dbFetchRows("SELECT * FROM `ucd_diskio` WHERE `device_id`  = ?",array($device['device_id']));

if (count($diskio_data))
{
  $diskio_cache = array();
  $diskio_cache = snmpwalk_cache_oid($device, "diskIOEntry", $diskio_cache, "UCD-DISKIO-MIB");

  echo("Checking UCD DiskIO MIB: ");

  foreach ($diskio_data as $diskio)
  {
    $index = $diskio['diskio_index'];

    $entry = $diskio_cache[$index];

    echo($diskio['diskio_descr'] . " ");

    if ($debug) { print_r($entry); }

    $rrd_update = $entry['diskIONReadX'].":".$entry['diskIONWrittenX'];
    $rrd_update .= ":".$entry['diskIOReads'].":".$entry['diskIOWrites'];

    $rrd  = $config['rrd_dir'] . "/" . $device['hostname'] . "/" . safename("ucd_diskio-" . $diskio['diskio_descr'] .".rrd");

    if ($debug) { echo("$rrd "); }

    if (!is_file($rrd))
    {
      rrdtool_create ($rrd, "--step 300 \
      DS:read:DERIVE:600:0:125000000000 \
      DS:written:DERIVE:600:0:125000000000 \
      DS:reads:DERIVE:600:0:125000000000 \
      DS:writes:DERIVE:600:0:125000000000 ".$config['rrd_rra']);
    }

    rrdtool_update($rrd,"N:$rrd_update");
    unset($rrd_update);
  }

  echo("\n");
}

unset($diskio_data);
unset($diskio_cache);

?>
