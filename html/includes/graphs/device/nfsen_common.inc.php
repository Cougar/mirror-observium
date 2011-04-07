<?php
include("includes/graphs/common.inc.php");
$device = device_by_id_cache($id);

if (!is_array($config['nfsen_rrds'])) { $config['nfsen_rrds'] = array($config['nfsen_rrds']); }

foreach ( $config['nfsen_rrds'] as $nfsenrrds )
{ 
  if ($configs[strlen($nfsenrrds)-1] != '/') { $nfsenrrds .= '/'; }

  # convert dots in filename to underscores
  $basefilename_underscored = preg_replace('/\./', $config['nfsen_split_char'], $device['hostname']);
  if (is_file($nfsenrrds . $basefilename_underscored . ".rrd"))
  {
    $rrd_filename = $nfsenrrds . $basefilename_underscored . ".rrd"; 

    $flowtypes = array('tcp', 'udp', 'icmp', 'other');

    $rrd_list=array();
    $nfsen_iter=1;
    foreach ($flowtypes as $flowtype)
    {

      $rrd_list[$nfsen_iter]['filename'] = $rrd_filename;
      $rrd_list[$nfsen_iter]['descr'] = $flowtype;
      $rrd_list[$nfsen_iter]['rra'] = $rraprefix . $flowtype;

      # set a multiplier which in turn will create a CDEF if this var is set
      if ($rraprefix == "traffic_") { $multiplier = "8"; }

      $colours   = "blues";
      $nototal   = 0;
      $units="";
      $unit_text = $rradescr;
      $scale_min = "0";

      if ($_GET['debug']) { print_r($rrd_list); }
      $nfsen_iter++;
    }
  }
}

include("includes/graphs/generic_multi_simplex_seperated.inc.php");

?>
