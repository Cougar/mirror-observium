#!/usr/bin/env php
<?php

include("config.php");
include("includes/functions.php");

if ($fd = @fopen($argv[1],'r'))
{
  $data = fread($fd,4096);
  while (!feof($fd))
  {
    $data .= fread($fd,4096);
  }

  foreach (explode("\n", $data) as $line)
  {
    $update = mysql_query($line);
    // FIXME check query success?
    echo("$line \n");
  }
}
else
{
  echo("ERROR: Could not open file \"$argv[1]\".\n");
}

?>