<?php

if (is_numeric($id))
{

  $data = dbFetchRow("SELECT * FROM bgpPeers WHERE bgpPeer_id = ?", array($id));

  if (is_numeric($data['device_id']) && ($config['allow_unauth_graphs'] || device_permitted($data['device_id'])))
  {
    $device = device_by_id_cache($data['device_id']);

    $title  = generate_device_link($device);
    $title .= " :: BGP :: " . htmlentities($data['bgp_peerid']);
    $auth = TRUE;
  }
}




?>
