<?php

// Attempt to draw a graph out of DSes we've collected from Munin plugins.
// Reverse engineering ftw!

$scale_min = 0;

include("includes/graphs/common.inc.php");

if($width > "500")
{
  $descr_len=24;
} else {
  $descr_len=14;
  $descr_len += round(($width - 230) / 8.2);
}

if($width > "500")
{
  $rrd_options .= " COMMENT:'".substr(str_pad($mplug['mplug_vlabel'], $descr_len),0,$descr_len)."   Current   Average  Maximum\l'";
  $rrd_options .= " COMMENT:'\l'";
} else {
  $rrd_options .= " COMMENT:'".substr(str_pad($mplug['mplug_vlabel'], $descr_len),0,$descr_len)."   Current   Average  Maximum\l'";
}


$c_i = 0;
$dbq = dbFetchRows("SELECT * FROM `munin_plugins_ds` WHERE `mplug_id` = ?", array($mplug['mplug_id']));
foreach ($dbq as $ds)
{
  $ds_filename = $plugfile."_".$ds['ds_name'].".rrd";
  $ds_name = $ds['ds_name'];

  $cmd_def .= " DEF:".$ds['ds_name']."=".$ds_filename.":val:AVERAGE";

  if(!empty($ds['ds_cdef']))
  {
    $cmd_cdef .= "";
    $ds_name = $ds['ds_name']."_cdef";
  }

  if($ds['ds_graph'] == "yes")
  {
    if(empty($ds['colour']))
    {
      if (!$config['graph_colours']['mixed'][$c_i]) { $c_i = 0; }
      $colour=$config['graph_colours']['mixed'][$c_i]; $c_i++;
    } else {
      $colour = $ds['colour'];
    }

    $descr     = str_replace(":", "\:", substr(str_pad($ds['ds_label'], $descr_len),0,$descr_len));

    $cmd_graph .= ' '.$ds['ds_draw'].':'.$ds_name.'#'.$colour.':"'.$descr.'"';
    $cmd_graph .= ' GPRINT:'.$ds_name.':LAST:"%6.2lf%s"';
    $cmd_graph .= ' GPRINT:'.$ds_name.':AVERAGE:"%6.2lf%s"';
    $cmd_graph .= ' GPRINT:'.$ds_name.':MAX:"%6.2lf%s\\n"';

  }

}

$rrd_options .= $cmd_def . $cmd_cdef . $cmd_graph;

?>
