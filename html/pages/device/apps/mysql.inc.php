<?php
global $config;

$app_sections = array('system' => "System",
                      'queries' => "Queries",
                      'innodb' => "InnoDB");

include("app_navbar.inc.php");

$graphs['system'] = array(
                'mysql_connections' => 'Connections',
                'mysql_status' => 'Process List',
                'mysql_files_tables' => 'Files and Tables',
                'mysql_myisam_indexes' => 'MyISAM Indexes',
                'mysql_network_traffic' => 'Network Traffic',
                'mysql_table_locks' => 'Table Locks',
                'mysql_temporary_objects' => 'Temporary Objects'
                );

$graphs['queries'] = array(
                'mysql_command_counters' => 'Command Counters',
                'mysql_query_cache' => 'Query Cache',
                'mysql_query_cache_memory' => 'Query Cache Memory',
                'mysql_select_types' => 'Select Types',
                'mysql_slow_queries' => 'Slow Queries',
                'mysql_sorts' => 'Sorts',
                );

$graphs['innodb'] = array(
                'mysql_innodb_buffer_pool' => 'InnoDB Buffer Pool',
                'mysql_innodb_buffer_pool_activity' => 'InnoDB Buffer Pool Activity',
                'mysql_innodb_insert_buffer' => 'InnoDB Insert Buffer',
                'mysql_innodb_io' => 'InnoDB IO',
                'mysql_innodb_io_pending' => 'InnoDB IO Pending',
                'mysql_innodb_log' => 'InnoDB Log',
                'mysql_innodb_row_operations' => 'InnoDB Row Operations',
                'mysql_innodb_semaphores' => 'InnoDB semaphores',
                'mysql_innodb_transactions' => 'InnoDB Transactions',
                );

foreach ($graphs[$vars['app_section']] as $key => $text)
{
  $graph_type = $key;
  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $app['app_id'];
  $graph_array['type']   = "application_".$key;
  echo('<h4>'.$text.'</h4>');

  echo("<tr bgcolor='$row_colour'><td colspan=5>");

  include("includes/print-graphrow.inc.php");

  echo("</td></tr>");
}

?>
