<?php

/*
 * Try to discover any Virtual Machines.
 */

if ($device['os'] == "vmware")
{
  /*
   * Variable to hold the discovered Virtual Machines.
   */

  $vmw_vmlist = array();

  /*
   * CONSOLE: Start the VMware discovery process.
   */

  echo("VMware VM: ");

  /*
   * Fetch the list is Virtual Machines.
   *
   *  VMWARE-VMINFO-MIB::vmwVmVMID.224 = INTEGER: 224
   *  VMWARE-VMINFO-MIB::vmwVmVMID.416 = INTEGER: 416
   *  ...
   */

  $oids = snmp_walk($device, "VMWARE-VMINFO-MIB::vmwVmVMID", "-Osqnv", "+VMWARE-ROOT-MIB:VMWARE-VMINFO-MIB", "+" . $config["install_dir"] . "/mibs/vmware");
  $oids = explode("\n", $oids);

  foreach ($oids as $oid)
  {
    /*
     * Fetch the Virtual Machine information.
     *
     *  VMWARE-VMINFO-MIB::vmwVmDisplayName.224 = STRING: My First VM
     *  VMWARE-VMINFO-MIB::vmwVmDisplayName.416 = STRING: My Second VM
     *  VMWARE-VMINFO-MIB::vmwVmGuestOS.224 = STRING: windows7Server64Guest
     *  VMWARE-VMINFO-MIB::vmwVmGuestOS.416 = STRING: winLonghornGuest
     *  VMWARE-VMINFO-MIB::vmwVmMemSize.224 = INTEGER: 8192 megabytes
     *  VMWARE-VMINFO-MIB::vmwVmMemSize.416 = INTEGER: 8192 megabytes
     *  VMWARE-VMINFO-MIB::vmwVmState.224 = STRING: poweredOn
     *  VMWARE-VMINFO-MIB::vmwVmState.416 = STRING: poweredOn
     *  VMWARE-VMINFO-MIB::vmwVmVMID.224 = INTEGER: 224
     *  VMWARE-VMINFO-MIB::vmwVmVMID.416 = INTEGER: 416
     *  VMWARE-VMINFO-MIB::vmwVmCpus.224 = INTEGER: 2
     *  VMWARE-VMINFO-MIB::vmwVmCpus.416 = INTEGER: 2
     */

    $vmwVmDisplayName = snmp_get($device, "VMWARE-VMINFO-MIB::vmwVmDisplayName." . $oid, "-Osqnv", "+VMWARE-ROOT-MIB:VMWARE-VMINFO-MIB", "+" . $config["install_dir"] . "/mibs/vmware");
    $vmwVmGuestOS   = snmp_get($device, "VMWARE-VMINFO-MIB::vmwVmGuestOS."   . $oid, "-Osqnv", "+VMWARE-ROOT-MIB:VMWARE-VMINFO-MIB", "+" . $config["install_dir"] . "/mibs/vmware");
    $vmwVmMemSize   = snmp_get($device, "VMWARE-VMINFO-MIB::vmwVmMemSize."   . $oid, "-Osqnv", "+VMWARE-ROOT-MIB:VMWARE-VMINFO-MIB", "+" . $config["install_dir"] . "/mibs/vmware");
    $vmwVmState     = snmp_get($device, "VMWARE-VMINFO-MIB::vmwVmState."     . $oid, "-Osqnv", "+VMWARE-ROOT-MIB:VMWARE-VMINFO-MIB", "+" . $config["install_dir"] . "/mibs/vmware");
    $vmwVmCpus    = snmp_get($device, "VMWARE-VMINFO-MIB::vmwVmCpus."    . $oid, "-Osqnv", "+VMWARE-ROOT-MIB:VMWARE-VMINFO-MIB", "+" . $config["install_dir"] . "/mibs/vmware");

    /*
     * VMware does not return an INTEGER but a STRING of the vmwVmMemSize. This bug
     * might be resolved by VMware in the future making this code absolete.
     */

    if (preg_match("/^([0-9]+) .*$/", $vmwVmMemSize, $matches))
    {
      $vmwVmMemSize = $matches[1];
    }

    /*
     * Check whether the Virtual Machine is already known for this host.
     */

    if (mysql_result(mysql_query("SELECT COUNT(id) FROM vmware_vminfo WHERE device_id = '" . $device["device_id"] . "' AND vmwVmVMID = '" . $oid . "'"), 0) == 0)
    {
      mysql_query("INSERT INTO vmware_vminfo (device_id, vmwVmVMID, vmwVmDisplayName, vmwVmGuestOS, vmwVmMemSize, vmwVmCpus, vmwVmState) VALUES (" . $device["device_id"] . ", " . $oid . ", '" . mres($vmwVmDisplayName) . "', '" . mres($vmwVmGuestOS) . "', " . $vmwVmMemSize . ", " . $vmwVmCpus . ", '" . mres($vmwVmState) . "')");
      echo("+");
    } else {
      echo(".");
    }

    /*
     * Save the discovered Virtual Machine.
     */

    $vmw_vmlist[] = $oid;
  }

  /*
   * Get a list of all the known Virtual Machines for this host.
   */

  $db_vm_list = mysql_query("SELECT id, vmwVmVMID FROM vmware_vminfo WHERE device_id = '" . $device["device_id"] . "'");

  while ($db_vm = mysql_fetch_assoc($db_vm_list))
  {
    /*
     * Delete the Virtual Machines that are removed from the host.
     */

    if (!in_array($db_vm["vmwVmVMID"], $vmw_vmlist)) {
      mysql_query("DELETE FROM vmware_vminfo WHERE id = '" . $db_vm["id"] . "'");
      echo("-");
    }
  }

  /*
   * Finished discovering VMware information.
   */

  echo("\n");
}

?>