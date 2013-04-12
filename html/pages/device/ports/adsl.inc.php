<?php

    echo('<table class="table-striped table-rounded table-bordered table-condensed">');

    echo("<thead><tr><th>Port</th><th>Traffic</th><th>Sync Speed</th><th>Attainable Speed</th><th>Attenuation</th><th>SNR Margin</th><th>Output Powers</th></tr></thead>");
    $i = "0";
    $ports = dbFetchRows("select * from `ports` AS P, `ports_adsl` AS A WHERE P.device_id = ? AND A.port_id = P.port_id AND P.deleted = '0' ORDER BY `ifIndex` ASC", array($device['device_id']));
    foreach ($ports as $port)
    {
      include("includes/print-interface-adsl.inc.php");

      $i++;
    }
    echo("</table>");

?>
