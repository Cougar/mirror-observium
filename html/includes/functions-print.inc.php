<?php

/**
 * Observium
 *
 *   This file is part of Observium.
 *
 * @package    observium
 * @subpackage web
 * @copyright  (C) 2006 - 2013 Adam Armstrong
 * @version    1.0.11
 *
 */

function print_graph_row_port($graph_array, $port)
{

  global $config;

  $graph_array['to']     = $config['time']['now'];
  $graph_array['id']     = $port['port_id'];

  print_graph_row($graph_array);

}

function print_graph_row($graph_array)
{

  global $config;

  if($_SESSION['widescreen'])
  {
    if ($_SESSION['big_graphs'])
    {
      if (!$graph_array['height']) { $graph_array['height'] = "110"; }
      if (!$graph_array['width']) { $graph_array['width']  = "353"; }
      $periods = array('sixhour', 'week', 'month', 'year');
    } else {
      if (!$graph_array['height']) { $graph_array['height'] = "110"; }
      if (!$graph_array['width']) { $graph_array['width']  = "215"; }
      $periods = array('sixhour', 'day', 'week', 'month', 'year', 'twoyear');
    }
  } else {
    if ($_SESSION['big_graphs'])
    {
      if (!$graph_array['height']) { $graph_array['height'] = "100"; }
      if (!$graph_array['width']) { $graph_array['width']  = "305"; }
      $periods = array('day', 'week', 'month');
    } else {
      if (!$graph_array['height']) { $graph_array['height'] = "100"; }
      if (!$graph_array['width']) { $graph_array['width']  = "208"; }
      $periods = array('day', 'week', 'month', 'year');
    }
  }

  if($graph_array['shrink']) { $graph_array['width'] = $graph_array['width'] - $graph_array['shrink']; }

  $graph_array['to']     = $config['time']['now'];

  foreach ($periods as $period)
  {
    $graph_array['from']        = $config['time'][$period];
    $graph_array_zoom           = $graph_array;
    $graph_array_zoom['height'] = "150";
    $graph_array_zoom['width']  = "400";

    $link_array = $graph_array;
    $link_array['page'] = "graphs";
    unset($link_array['height'], $link_array['width']);
    $link = generate_url($link_array);

    echo(overlib_link($link, generate_graph_tag($graph_array), generate_graph_tag($graph_array_zoom),  NULL));
  }

}


function print_vm_row($vm, $device = NULL)
{
  echo('<tr>');

  echo('<td>');

  if (getidbyname($vm['vmwVmDisplayName']))
  {
    echo(generate_device_link(device_by_name($vm['vmwVmDisplayName'])));
  } else {
    echo $vm['vmwVmDisplayName'];
  }

  echo("</td>");
  echo('<td>' . $vm['vmwVmState'] . "</td>");

  if ($vm['vmwVmGuestOS'] == "E: tools not installed")
  {
    echo('<td class="box-desc">Unknown (VMware Tools not installed)</td>');
  }
  else if ($vm['vmwVmGuestOS'] == "E: tools not running")
  {
    echo('<td class="box-desc">Unknown (VMware Tools not running)</td>');
  }
  else if ($vm['vmwVmGuestOS'] == "")
  {
    echo('<td class="box-desc"><i>(Unknown)</i></td>');
  }
  elseif (isset($config['vmware_guestid'][$vm['vmwVmGuestOS']]))
  {
    echo('<td>' . $config['vmware_guestid'][$vm['vmwVmGuestOS']] . "</td>");
  }
  else
  {
    echo('<td>' . $vm['vmwVmGuestOS'] . "</td>");
  }

  if ($vm['vmwVmMemSize'] >= 1024)
  {
    echo("<td class=list>" . sprintf("%.2f",$vm['vmwVmMemSize']/1024) . " GB</td>");
  } else {
    echo("<td class=list>" . sprintf("%.2f",$vm['vmwVmMemSize']) . " MB</td>");
  }

  echo('<td>' . $vm['vmwVmCpus'] . " CPU</td>");

}

/**
 * Generate Bootstrap-format Navbar
 *
 *   A little messy, but it works and lets us move to having no navbar markup on pages :)
 *   Examples:
 *   print_navbar(array('brand' => "Apps", 'class' => "navbar-narrow", 'options' => array('mysql' => array('text' => "MySQL", 'url' => generate_url($vars, 'app' => "mysql")))))
 *
 * @param array $vars
 * @return none
 *
 */

function print_navbar($navbar)
{
  global $config;

  $id = strgen();

  ?>

  <div class="navbar <?php echo $navbar['class']; ?>">
    <div class="navbar-inner">
      <div class="container">
        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target="#nav-<?php echo $id; ?>">
          <span class="oicon-bar"></span>
        </button>

  <?php

  if (isset($navbar['brand'])) { echo ' <a class="brand">'.$navbar['brand'].'</a>'; }
  echo('<div class="nav-collapse" id="nav-'.$id.'">');

  foreach (array('options', 'options_right') as $array_name)
  {
    if ($array_name == "options_right") {
      if (!$navbar[$array_name]) { break; }
      echo('<ul class="nav pull-right">');
    } else {
      echo('<ul class="nav">');
    }
    foreach ($navbar[$array_name] as $option => $array)
    {
      if ($array[''] == "pull-right") {
        $navbar['options_right'][$option] = $array;
      } else {
        if (!is_array($array['suboptions']))
        {
          echo('<li class="'.$array['class'].'">');
          echo('<a href="'.$array['url'].'">');
          if (isset($array['icon'])) { echo('<i class="'.$array['icon'].'"></i> '); }
          echo($array['text'].'</a>');
          echo('</li>');
        } else {
          echo('  <li class="dropdown">');
          echo('    <a class="dropdown-toggle" data-toggle="dropdown"  href="'.$array['url'].'">');
          if (isset($array['icon'])) { echo('<i class="'.$array['icon'].'"></i> '); }
          echo($array['text'].'
            <b class="caret"></b>
          </a>
        <ul class="dropdown-menu">');
          foreach ($array['suboptions'] as $suboption => $subarray)
          {
            echo('<li class="'.$subarray['class'].'">');
            echo('<a href="'.$subarray['url'].'">');
            if (isset($subarray['icon'])) { echo('<i class="'.$subarray['icon'].'"></i> '); }
            echo($subarray['text'].'</a>');
            echo('</li>');
          }
          echo('    </ul>
      </li>');
        }
      }
    }
    echo('</ul>');
  }

  ?>
        </div>
      </div>
    </div>
  </div>

 <?php

}

/**
 * Generate search form
 *
 * generates a search form.
 * types allowed: select, multiselect, text (or input), datetime, newline
 * 
 * Example of use:
 *  - array for 'select' item type
 *  $search[] = array('type'    => 'select',          // Type
 *                    'name'    => 'Search By',       // Displayed title for item
 *                    'id'      => 'searchby',        // Item id and name
 *                    'width'   => '120px',           // (Optional) Item width
 *                    'size'    => '15',              // (Optional) Maximum number of items to show in the menu (default 15)
 *                    'value'   => $vars['searchby'], // (Optional) Current value(-s) for item
 *                    'values'  => array('mac' => 'MAC Address',
 *                                       'ip'  => 'IP Address'));  // Array with option items
 *  - array for 'multiselect' item type (array keys same as above)
 *  $search[] = array('type'    => 'multiselect',
 *                    'name'    => 'Priorities',
 *                    'id'      => 'priority',
 *                    'width'   => '150px',
 *                    'subtext' => TRUE,              // (Optional) Display items value right of the item name
 *                    'value'   => $vars['priority'],
 *                    'values'  => $priorities);
 *  - array for 'text' or 'input' item type
 *  $search[] = array('type'  => 'text',
 *                    'name'  => 'Address',
 *                    'id'    => 'address',
 *                    'width' => '120px',
 *                    'value' => $vars['address']);
 *  - array for 'datetime' item type
 *  $search[] = array('type'  => 'datetime',
 *                    'id'    => 'timestamp',
 *                    'presets' => TRUE,                  // (optional) Show select field with timerange presets
 *                    'min'   => dbFetchCell('SELECT MIN(`timestamp`) FROM `syslog`'), // (optional) Minimum allowed date/time
 *                    'max'   => dbFetchCell('SELECT MAX(`timestamp`) FROM `syslog`'), // (optional) Maximum allowed date/time
 *                    'from'  => $vars['timestamp_from'], // (optional) Current 'from' value
 *                    'to'    => $vars['timestamp_to']);  // (optional) Current 'to' value
 *  - array for 'newline' item pseudo type
 *  $search[] = array('type' => 'newline')
 *  print_search_simple($search, 'Title here');
 *
 * @param array $data, string $title
 * @return none
 *
 */

function print_search_simple($data, $title = '', $button = 'search')
{
  // Form header
  $string = PHP_EOL . '<!-- START search form -->' . PHP_EOL;
  $string .= '<form method="POST" action="" class="form form-inline">' . PHP_EOL;
  $string .= '<div class="navbar">' . PHP_EOL;
  $string .= '<div class="navbar-inner">';
  $string .= '<div class="container">';
  if ($title) { $string .= '  <a class="brand">' . $title . '</a>' . PHP_EOL; }

  $string .= '<div class="nav" style="margin: 5px 0 5px 0;">';

  // Main
  foreach ($data as $item)
  {
    if (!isset($item['value'])) { $item['value'] = ''; }
    switch($item['type'])
    {
      case 'text':
      case 'input':
        $string .= '  <div class="input-prepend">' . PHP_EOL;
        if (!$item['name']) { $item['name'] = '<i class="icon-list"></i>'; }
        $string .= '    <span class="add-on">'.$item['name'].'</span>' . PHP_EOL;
        $string .= '    <input type="'.$item['type'].'" ';
        $string .= (isset($item['width'])) ? 'style="width:'.$item['width'].'" ' : '';
        $string .= 'name="'.$item['id'].'" id="'.$item['id'].'" class="input" value="'.$item['value'].'"/>' . PHP_EOL;
        $string .= '  </div>' . PHP_EOL;
        // End 'text' & 'input'
        break;
      case 'newline':
        $string .= '<div class="clearfix" id="'.$item['id'].'"><hr /></div>' . PHP_EOL;
        // End 'newline'
        break;
      case 'datetime':
        $id_from = $item['id'].'_from';
        $id_to = $item['id'].'_to';
        // Presets
        if ($item['presets'])
        {
          $presets = array('sixhours'  => 'Last 6 hours',
                           'today'     => 'Today',
                           'yesterday' => 'Yesterday',
                           'tweek'     => 'This week',
                           'lweek'     => 'Last week',
                           'tmonth'    => 'This month',
                           'lmonth'    => 'Last month',
                           'tyear'     => 'This year',
                           'lyear'     => 'Last year');
          $string .= '    <select id="'.$item['id'].'" class="selectpicker show-tick" data-size="false" data-width="auto">' . PHP_EOL . '      ';
          $string .= '<option value="" selected>Date/Time presets</option>';
          foreach ($presets as $k => $v)
          {
            $string .= '<option value="'.$k.'">'.$v.'</option> ';
          }
          $string .= PHP_EOL . '    </select>' . PHP_EOL;
        }
        // Date/Time input fields
        $string .= '  <div class="input-prepend" id="'.$id_from.'">' . PHP_EOL;
        $string .= '    <span class="add-on btn"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> From</span>' . PHP_EOL;
        $string .= '    <input type="text" class="input-medium" data-format="yyyy-MM-dd hh:mm:ss" ';
        $string .= 'name="'.$id_from.'" id="'.$id_from.'" value="'.$item['from'].'"/>' . PHP_EOL;
        $string .= '  </div>' . PHP_EOL;
        $string .= '  <div class="input-prepend" id="'.$id_to.'">' . PHP_EOL;
        $string .= '    <span class="add-on btn"><i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> To</span>' . PHP_EOL;
        $string .= '    <input type="text" class="input-medium" data-format="yyyy-MM-dd hh:mm:ss" ';
        $string .= 'name="'.$id_to.'" id="'.$id_to.'" value="'.$item['to'].'"/>' . PHP_EOL;
        $string .= '  </div>' . PHP_EOL;
        // JS
        $min = '-Infinity';
        $max = 'Infinity';
        $pattern = '/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/';
        if (!empty($item['min']))
        {
          if (preg_match($pattern, $item['min'], $matches))
          {
            $matches[2] = $matches[2] - 1;
            array_shift($matches);
            $min = 'new Date(' . implode(',', $matches) . ')';
          }
        } 
        if (!empty($item['max']))
        {
          if (preg_match($pattern, $item['max'], $matches))
          {
            $matches[2] = $matches[2] - 1;
            array_shift($matches);
            $max = 'new Date(' . implode(',', $matches) . ')';
          }
        } 
        $string .= '
    <script type="text/javascript">
      var startDate = '.$min.';
      var endDate   = '.$max.';
      $(document).ready(function() {
	$(\'#'.$id_from.'\').datetimepicker({
          //pickSeconds: false,
          weekStart: 1,
          startDate: startDate,
          endDate: endDate
        });
	$(\'#'.$id_to.'\').datetimepicker({
          //pickSeconds: false,
          weekStart: 1,
          startDate: startDate,
          endDate: endDate
        });
      });' . PHP_EOL;
        if ($item['presets'])
        {
          $string .= '
      $(\'select#'.$item['id'].'\').change(function() {
        var input_from = $(\'input#'.$id_from.'\');
        var input_to   = $(\'input#'.$id_to.'\');
        switch ($(this).val()) {' . PHP_EOL;
          foreach ($presets as $k => $v)
          {
            $preset = datetime_preset($k);
            $string .= "          case '$k':\n";
            $string .= "            input_from.val('".$preset['from']."');\n";
            $string .= "            input_to.val('".$preset['to']."');\n";
            $string .= "            break;\n";
          }
          $string .= '
          default:
            input_from.val("");
            input_to.val("");
            break;
        }
      });' . PHP_EOL;
        }
        $string .= '</script>' . PHP_EOL;
        // End 'datetime'
        break;
      case 'select':
      case 'multiselect':
        if ($item['type'] == 'multiselect')
        {
          $title = (isset($item['name'])) ? 'title="'.$item['name'].'" ' : '';
          $string .= '    <select multiple name="'.$item['id'].'[]" ' . $title;
        } else {
          $string .= '    <select name="'.$item['id'].'" ';
          if ($item['name'] && !isset($item['values']['']))
          {
            $item['values'] = array('' => $item['name']) + $item['values'];
          }
        }
        $string .= 'id="'.$item['id'].'" ';
        $data_width = ($item['width']) ? ' data-width="'.$item['width'].'"' : ' data-width="auto"';
        $data_size = (is_numeric($item['size'])) ? ' data-size="'.$item['size'].'"' : ' data-size="15"';
        $string .= 'class="selectpicker show-tick" data-selected-text-format="count>1"';
        $string .= $data_width . $data_size . '>' . PHP_EOL . '      ';
        if (!is_array($item['value'])) { $item['value'] = array($item['value']); }
        foreach ($item['values'] as $k => $v)
        {
          $k = (string)$k;
          $data_subtext = ($item['subtext']) ? ' data-subtext="('.$k.')"' : '';
          $string .= '<option value="'.$k.'"' . $data_subtext;
          $string .= (in_array($k, $item['value'])) ? ' selected>' : '>';
          $string .= $v.'</option> ';
        }
        $string .= PHP_EOL . '    </select>' . PHP_EOL;
        // End 'select' & 'multiselect'
        break;
    }
  }

  $string .= '</div>';

  // Form footer
  $string .= '    <ul class="nav pull-right"><li>' . PHP_EOL;
  $string .= '      <input type="hidden" name="pageno" value="1">' . PHP_EOL;
  switch($button)
  {
    case 'update':
      $string .= '      <button type="submit" class="btn"><i class="icon-refresh"></i> Update</button>' . PHP_EOL;
      break;
    default:
      $string .= '      <button type="submit" class="btn"><i class="icon-search"></i> Search</button>' . PHP_EOL;
  }
  $string .= '    </li></ul>' . PHP_EOL;
  $string .= '</div></div></div></form>' . PHP_EOL;
  $string .= '<!-- END search form -->' . PHP_EOL . PHP_EOL;

  // Print search form
  echo($string);
}

/**
 * Display IPv4/IPv6 addresses.
 *
 * Display pages with IP addresses from device Interfaces.
 *
 * @param array $vars
 * @return none
 *
 */

function print_addresses($vars)
{
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  switch($vars['search'])
  {
    case '6':
    case 'ipv6':
    case 'v6':
      $address_type = 'ipv6';
      break;
    default:
      $address_type = 'ipv4';
  }

  $param = array();
  $where = ' WHERE 1 ';
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        case 'device':
        case 'device_id':
          $where .= ' AND I.device_id = ?';
          $param[] = $value;
          break;
        case 'interface':
          $where .= ' AND I.ifDescr LIKE ?';
          $param[] = $value;
          break;
        case 'network':
          $where .= ' AND N.ip_network_id = ?';
          $param[] = $value;
          break;
        case 'address':
          list($addr, $mask) = explode('/', $value);
          if (is_numeric(stripos($addr, ':abcdef'))) { $address_type = 'ipv6'; }
          switch ($address_type)
          {
            case 'ipv6':
              $ip_valid = Net_IPv6::checkIPv6($addr);
              break;
            case 'ipv4':
              $ip_valid = Net_IPv4::validateIP($addr);
              break;
          }
          if ($ip_valid)
          {
            // If address valid -> seek occurrence in network
            if (!$mask) { $mask = ($address_type === 'ipv4') ? '32' : '128'; }
          } else {
            // If address not valid -> seek LIKE
            $where .= ' AND A.ip_address LIKE ?';
            $param[] = '%'.$addr.'%';
          }
          break;
      }
    }
  }

  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  $query = 'FROM `ip_addresses` AS A ';
  $query .= 'LEFT JOIN `ports`   AS I ON I.port_id   = A.port_id ';
  $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
  $query .= 'LEFT JOIN `ip_networks` AS N ON N.ip_network_id = A.ip_network_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(ip_address_id) ' . $query;
  $query =  'SELECT * ' . $query;
  $query .= ' ORDER BY A.ip_address';
  if ($ip_valid)
  {
    $pagination = FALSE;
  } else {
    $query .= " LIMIT $start,$pagesize";
  }
  // Override by address type
  $query = str_replace(array('ip_address', 'ip_network'), array($address_type.'_address', $address_type.'_network'), $query);
  $query_count = str_replace(array('ip_address', 'ip_network'), array($address_type.'_address', $address_type.'_network'), $query_count);

  // Query addresses
  $entries = dbFetchRows($query, $param);
  // Query address count
  if ($pagination) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'search') { $list['device'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    $string .= '      <th>Interface</th>' . PHP_EOL;
    $string .= '      <th>Address</th>' . PHP_EOL;
    $string .= '      <th>Description</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    $address_show = TRUE;
    if ($ip_valid)
    {
      // If address not in specified network, don't show entry.
      if ($address_type === 'ipv4')
      {
        $address_show = Net_IPv4::ipInNetwork($entry[$address_type.'_address'], $addr . '/' . $mask);
      } else {
        $address_show = Net_IPv6::isInNetmask($entry[$address_type.'_address'], $addr, $mask);
      }
    }

    if ($address_show)
    {
      list($prefix, $length) = explode('/', $entry[$address_type.'_network']);

      if (port_permitted($entry['port_id']))
      {
        humanize_port ($entry);
        if ($entry['ifInErrors_delta'] > 0 || $entry['ifOutErrors_delta'] > 0)
        {
          $port_error = generate_port_link($entry, '<span class="label label-important">Errors</span>', 'port_errors');
        }

        $string .= '  <tr>' . PHP_EOL;
        if ($list['device'])
        {
          $string .= '    <td class="entity" nowrap>' . generate_device_link($entry) . '</td>' . PHP_EOL;
        }
        $string .= '    <td class="entity">' . generate_port_link($entry, makeshortif($entry['label'])) . ' ' . $port_error . '</td>' . PHP_EOL;
        if ($address_type === 'ipv6') { $entry[$address_type.'_address'] = Net_IPv6::compress($entry[$address_type.'_address']); }
        $string .= '    <td>' . $entry[$address_type.'_address'] . '/' . $length . '</td>' . PHP_EOL;
        $string .= '    <td>' . $entry['ifAlias'] . '</td>' . PHP_EOL;
        $string .= '  </tr>' . PHP_EOL;
      }
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination) { echo pagination($vars, $count); }

  // Print addresses
  echo $string;
}

/**
 * Display Interface MACs addresses.
 *
 * Display pages with MAC addresses from device Interfaces.
 *
 * @param array $vars
 * @return none
 *
 */

function print_mac_addresses($vars)
{
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  $param = array();
  $where = ' WHERE 1 ';
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        case 'device':
        case 'device_id':
          $where .= ' AND I.device_id = ?';
          $param[] = $value;
          break;
        case 'interface':
          $where .= ' AND I.ifDescr LIKE ?';
          $param[] = $value;
          break;
        case 'address':
          $where .= ' AND I.ifPhysAddress LIKE ?';
          $param[] = '%'.str_replace(array(':', ' ', '-', '.', '0x'),'',mres($value)).'%';
          break;
      }
    }
  }
  $where .= ' AND I.ifPhysAddress IS NOT NULL'; //Exclude empty MACs

  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  $query = 'FROM `ports` AS I ';
  $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(*) ' . $query;
  $query =  'SELECT * ' . $query;
  $query .= ' ORDER BY I.ifPhysAddress';
  $query .= " LIMIT $start,$pagesize";

  // Query addresses
  $entries = dbFetchRows($query, $param);
  // Query address count
  if ($pagination) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'search') { $list['device'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    $string .= '      <th>Interface</th>' . PHP_EOL;
    $string .= '      <th>MAC Address</th>' . PHP_EOL;
    $string .= '      <th>Description</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    if (port_permitted($entry['port_id']))
    {
      humanize_port ($entry);
      
      $string .= '  <tr>' . PHP_EOL;
      if ($list['device'])
      {
        $string .= '    <td class="entity" nowrap>' . generate_device_link($entry) . '</td>' . PHP_EOL;
      }
      if ($entry['ifInErrors_delta'] > 0 || $entry['ifOutErrors_delta'] > 0)
      {
        $port_error = generate_port_link($entry, '<span class="label label-important">Errors</span>', 'port_errors');
      }
      $string .= '    <td class="entity">' . generate_port_link($entry, makeshortif($entry['label'])) . ' ' . $port_error . '</td>' . PHP_EOL;
      $string .= '    <td width="160">' . formatMac($entry['ifPhysAddress']) . '</td>' . PHP_EOL;
      $string .= '    <td>' . $entry['ifAlias'] . '</td>' . PHP_EOL;
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination) { echo pagination($vars, $count); }

  // Print MAC addresses
  echo $string;
}

/**
 * Display ARP/NDP table addresses.
 *
 * Display pages with ARP/NDP tables addresses from devices.
 *
 * @param array $vars
 * @return none
 *
 */

function print_arptable($vars)
{
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  $param = array();
  $where = ' WHERE 1 ';
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        case 'device':
        case 'device_id':
          $where .= ' AND D.device_id = ?';
          $param[] = $value;
          break;
        case 'port':
        case 'port_id':
          $where .= ' AND I.port_id = ?';
          $param[] = $value;
          break;
        case 'ip_version':
          $where .= ' AND ip_version = ?';
          $param[] = $value;
          break;
        case 'address':
          if (isset($vars['searchby']) && $vars['searchby'] == 'ip')
          {
            $where .= ' AND `ip_address` LIKE ?';
            $value = trim($value);
            ///FIXME. Need another conversion ("2001:b08:b08" -> "2001:0b08:0b08") -- mike
            if (Net_IPv6::checkIPv6($value)) { $value = Net_IPv6::uncompress($value, true); }
            $param[] = '%'.$value.'%';
          } else {
            $where .= ' AND `mac_address` LIKE ?';
            $param[] = '%'.str_replace(array(':', ' ', '-', '.', '0x'),'',mres($value)).'%';
          }
          break;
      }
    }
  }

  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  $query = 'FROM `ip_mac` AS M ';
  $query .= 'LEFT JOIN `ports`   AS I ON I.port_id   = M.port_id ';
  $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(mac_id) ' . $query;
  $query =  'SELECT * ' . $query;
  $query .= ' ORDER BY M.mac_address';
  $query .= " LIMIT $start,$pagesize";

  // Query ARP/NDP table addresses
  $entries = dbFetchRows($query, $param);
  // Query ARP/NDP table address count
  if ($pagination) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE, 'port' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'search') { $list['device'] = TRUE; }
  if (!isset($vars['port']) || empty($vars['port']) || $vars['page'] == 'search') { $list['port'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    $string .= '      <th>MAC Address</th>' . PHP_EOL;
    $string .= '      <th>IP Address</th>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    if ($list['port']) { $string .= '      <th>Interface</th>' . PHP_EOL; }
    $string .= '      <th>Remote Device</th>' . PHP_EOL;
    $string .= '      <th>Remote Interface</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    if (port_permitted($entry['port_id']))
    {
      humanize_port ($entry);
      $ip_version = $entry['ip_version'];
      $ip_address = ($ip_version == 6) ? Net_IPv6::compress($entry['ip_address']) : $entry['ip_address'];
      $arp_host = dbFetchRow('SELECT * FROM ipv'.$ip_version.'_addresses AS A
                             LEFT JOIN ports AS I ON A.port_id = I.port_id
                             LEFT JOIN devices AS D ON D.device_id = I.device_id
                             WHERE A.ipv'.$ip_version.'_address = ?', array($ip_address));
      $arp_name = ($arp_host) ? generate_device_link($arp_host) : '';
      $arp_if = ($arp_host) ? generate_port_link($arp_host) : '';
      if ($arp_host['device_id'] == $entry['device_id']) { $arp_name = 'Self Device'; }
      if ($arp_host['port_id'] == $entry['port_id']) { $arp_if = 'Self Port'; }

      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td width="160">' . formatMac($entry['mac_address']) . '</td>' . PHP_EOL;
      $string .= '    <td width="140">' . $ip_address . '</td>' . PHP_EOL;
      if ($list['device'])
      {
        $string .= '    <td class="entity" nowrap>' . generate_device_link($entry) . '</td>' . PHP_EOL;
      }
      if ($list['port'])
      {
        if ($entry['ifInErrors_delta'] > 0 || $entry['ifOutErrors_delta'] > 0)
        {
          $port_error = generate_port_link($entry, '<span class="label label-important">Errors</span>', 'port_errors');
        }
        $string .= '    <td class="entity">' . generate_port_link($entry, makeshortif($entry['label'])) . ' ' . $port_error . '</td>' . PHP_EOL;
      }
      $string .= '    <td class="entity" width="200">' . $arp_name . '</td>' . PHP_EOL;
      $string .= '    <td class="entity">' . $arp_if . '</td>' . PHP_EOL;
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination) { echo pagination($vars, $count); }

  // Print ARP/NDP table
  echo $string;
}

/**
 * Display events.
 *
 * Display pages with device/port/system events on some formats.
 * Examples:
 * print_events() - display last 10 events from all devices
 * print_events(array('pagesize' => 99)) - display last 99 events from all device
 * print_events(array('pagesize' => 10, 'pageno' => 3, 'pagination' => TRUE)) - display 10 events from page 3 with pagination header
 * print_events(array('pagesize' => 10, 'device' = 4)) - display last 10 events for device_id 4
 * print_events(array('short' => TRUE)) - show small block with last events
 *
 * @param array $vars
 * @return none
 *
 */
function print_events($vars)
{
  // Short events? (no pagination, small out)
  $short = (isset($vars['short']) && $vars['short']);
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  $param = array();
  $where = ' WHERE 1 ';
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        case 'device':
        case 'device_id':
          $where .= ' AND E.device_id = ?';
          $param[] = $value;
          break;
        case 'port':
          $where .= ' AND E.reference = ?';
          $param[] = $value;
          break;
        case 'type':
          if (!is_array($value)) { $value = array($value); }
          $where .= ' AND (';
          foreach ($value as $v)
          {
            $where .= "E.type = ? OR ";
            $param[] = $v;
          }
          $where = substr($where, 0, -4) . ')';
          break;
        case 'message':
          foreach (explode(',', $value) as $val)
          {
            $param[] = '%'.$val.'%';
            $cond[] = "`$var` LIKE ?";
          }
          $where .= 'AND (';
          $where .= implode(' OR ', $cond);
          $where .= ')';
          break;
        case 'timestamp_from':
          $where .= ' AND `timestamp` > ?';
          $param[] = $value;
          break;
        case 'timestamp_to':
          $where .= ' AND `timestamp` < ?';
          $param[] = $value;
          break;
      }
    }
  }

  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
#  $query_device = ' AND D.ignore = 0 ';
#  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  $query = 'FROM `eventlog` AS E ';
  $query .= $query_perms;
  $query .= $where . $query_device . $query_user;
  $query_count = 'SELECT COUNT(event_id) '.$query;

  /// FIXME Mike: bad table column `type` they intersect with table `devices`
  $query = 'SELECT STRAIGHT_JOIN E.device_id, E.timestamp, E.message, E.type, E.reference '.$query;
  $query .= ' ORDER BY `timestamp` DESC ';
  $query .= "LIMIT $start,$pagesize";

  // Query events
  $entries = dbFetchRows($query, $param);

  // Query events count
  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE, 'port' => FALSE);
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'eventlog') { $list['device'] = TRUE; }
  if ($short || !isset($vars['port']) || empty($vars['port'])) { $list['port'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed-more">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    $string .= '      <th>Date</th>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    if ($list['port'])   { $string .= '      <th>Entity</th>' . PHP_EOL; }
    $string .= '      <th>Message</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string   .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    $icon = geteventicon($entry['message']);
    if ($icon) { $icon = '<img src="images/16/' . $icon . '" />'; }

    $string .= '  <tr>' . PHP_EOL;
    if ($short)
    {
      $string .= '    <td width="105" class="syslog">';
    } else {
      $string .= '    <td width="160">';
    }
    $string .= format_timestamp($entry['timestamp']) . '</td>' . PHP_EOL;
    if ($list['device'])
    {
      $dev = device_by_id_cache($entry['device_id']);
      $string .= '    <td class="entity">' . generate_device_link($dev, shorthost($dev['hostname'])) . '</td>' . PHP_EOL;
    }
    if ($list['port'])
    {
      if ($entry['type'] == 'interface')
      {
        $this_if = getifbyid($entry['reference']);
        humanize_port($this_if);
        $entry['link'] = '<span class="entity">' . generate_port_link($this_if, makeshortif($this_if['label'])) . '</span>';
      } else {
        $entry['link'] = 'System';
      }
      if (!$short) { $string .= '    <td>' . $entry['link'] . '</td>' . PHP_EOL; }
    }
    if ($short)
    {
      $string .= '    <td class="syslog">' . $entry['link'] . ' ';
    } else {
      $string .= '    <td>';
    }
    $string .= htmlspecialchars($entry['message']) . '</td>' . PHP_EOL;
    $string .= '  </tr>' . PHP_EOL;
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Print pagination header
  if ($pagination && !$short) { echo pagination($vars, $count); }

  // Print events
  echo $string;
}

/**
 * Display short events.
 *
 * This is use function:
 * print_events(array('short' => TRUE))
 *
 * @param array $vars
 * @return none
 *
 */
function print_events_short($var)
{
  $var['short'] = TRUE;
  print_events($var);
}

/**
 * Display syslog messages.
 *
 * Display pages with device syslog messages.
 * Examples:
 * print_events() - display last 10 syslog messages from all devices
 * print_events(array('pagesize' => 99)) - display last 99 syslog messages from all device
 * print_events(array('pagesize' => 10, 'pageno' => 3, 'pagination' => TRUE)) - display 10 syslog messages from page 3 with pagination header
 * print_events(array('pagesize' => 10, 'device' = 4)) - display last 10 syslog messages for device_id 4
 * print_events(array('short' => TRUE)) - show small block with last syslog messages
 *
 * @param array $vars
 * @return none
 *
 */
function print_syslogs($vars)
{
  // Short events? (no pagination, small out)
  $short = (isset($vars['short']) && $vars['short']);
  // With pagination? (display page numbers in header)
  $pagination = (isset($vars['pagination']) && $vars['pagination']);
  $pageno = (isset($vars['pageno']) && !empty($vars['pageno'])) ? $vars['pageno'] : 1;
  $pagesize = (isset($vars['pagesize']) && !empty($vars['pagesize'])) ? $vars['pagesize'] : 10;
  $start = $pagesize * $pageno - $pagesize;

  $priorities = syslog_priorities();

  $param = array();
  $where = ' WHERE 1 ';
  foreach ($vars as $var => $value)
  {
    if ($value != '')
    {
      switch ($var)
      {
        case 'device':
        case 'device_id':
          $where .= ' AND D.device_id = ?';
          $param[] = $value;
          break;
        case 'priority':
        case 'program':
          if (!is_array($value)) { $value = array($value); }
          $where .= ' AND (';
          foreach ($value as $v)
          {
            if ($v === '[[EMPTY]]') { $v = ''; }
            $where .= "`$var` = ? OR ";
            $param[] = $v;
          }
          $where = substr($where, 0, -4) . ')';
          break;
        case 'message':
          foreach (explode(',', $value) as $val)
          {
            $param[] = '%'.$val.'%';
            $cond[] = '`msg` LIKE ?';
          }
          $where .= 'AND (';
          $where .= implode(' OR ', $cond);
          $where .= ')';
          break;
        case 'timestamp_from':
          $where .= ' AND `timestamp` > ?';
          $param[] = $value;
          break;
        case 'timestamp_to':
          $where .= ' AND `timestamp` < ?';
          $param[] = $value;
          break;
      }
    }
  }

  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  $query = 'FROM `syslog` AS S ';
  $query .= 'LEFT JOIN `devices` AS D ON S.device_id = D.device_id ';
  $query .= $query_perms;
  $query .= $where . $query_user . $query_device;
  $query_count = 'SELECT COUNT(seq) ' . $query;
  $query = 'SELECT STRAIGHT_JOIN * ' . $query;
  $query .= ' ORDER BY `timestamp` DESC ';
  $query .= "LIMIT $start,$pagesize";

  // Query syslog messages
  $entries = dbFetchRows($query, $param);
  // Query syslog count
  if ($pagination && !$short) { $count = dbFetchCell($query_count, $param); }

  $list = array('device' => FALSE, 'priority' => TRUE); // For now (temporarily) priority always displayed
  if (!isset($vars['device']) || empty($vars['device']) || $vars['page'] == 'syslog') { $list['device'] = TRUE; }
  if ($short || !isset($vars['priority']) || empty($vars['priority'])) { $list['priority'] = TRUE; }

  $string = '<table class="table table-bordered table-striped table-hover table-condensed">' . PHP_EOL;
  if (!$short)
  {
    $string .= '  <thead>' . PHP_EOL;
    $string .= '    <tr>' . PHP_EOL;
    $string .= '      <th>Date</th>' . PHP_EOL;
    if ($list['device']) { $string .= '      <th>Device</th>' . PHP_EOL; }
    if ($list['priority']) { $string .= '      <th>Priority</th>' . PHP_EOL; }
    $string .= '      <th>Message</th>' . PHP_EOL;
    $string .= '    </tr>' . PHP_EOL;
    $string .= '  </thead>' . PHP_EOL;
  }
  $string .= '  <tbody>' . PHP_EOL;

  foreach ($entries as $entry)
  {
    $string .= '  <tr>';
    if ($short)
    {
      $string .= '    <td width="160" class="syslog">';
    } else {
      $string .= '    <td width="160">';
    }
    $string .= format_timestamp($entry['timestamp']) . '</td>' . PHP_EOL;
    if ($list['device'])
    {
      $dev = device_by_id_cache($entry['device_id']);
      $string .= '    <td class="entity">' . generate_device_link($dev, shorthost($dev['hostname'])) . '</td>' . PHP_EOL;
    }
    if ($list['priority'])
    {
      if (!$short) { $string .= '    <td style="color: ' . $priorities[$entry['priority']]['color'] . ';">(' . $entry['priority'] . ')&nbsp;' . $priorities[$entry['priority']]['name'] . '</td>' . PHP_EOL; }
    }
    if ($short)
    {
      $string .= '    <td class="syslog">';
    } else {
      $string .= '    <td>';
    }
    $entry['program'] = ($entry['program'] === '') ? '[[EMPTY]]' : $entry['program'];
    $string .= '<strong>' . $entry['program'] . ' :</strong> ' . htmlspecialchars($entry['msg']) . '</td>' . PHP_EOL;
    $string .= '  </tr>' . PHP_EOL;
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>' . PHP_EOL;

  // Print pagination header
  if ($pagination && !$short) { echo pagination($vars, $count); }

  // Print syslog
  echo($string);
}

/**
 * Display status alerts.
 *
 * Display pages with alerts about device troubles.
 * Examples:
 * print_status(array('devices' => TRUE)) - display for devices down
 *
 * Another statuses:
 * devices, uptime, ports, errors, services, bgp
 *
 * @param array $status
 * @return none
 *
 */
function print_status($status)
{
  // Mike: I know that there are duplicated variables, but later will remove global
  global $config;

  $string  = '<table class="table table-bordered table-striped table-hover table-condensed">' . PHP_EOL;
  $string .= '  <thead>' . PHP_EOL;
  $string .= '  <tr>' . PHP_EOL;
  $string .= '    <th>Device</th>' . PHP_EOL;
  $string .= '    <th>Type</th>' . PHP_EOL;
  $string .= '    <th>Status</th>' . PHP_EOL;
  $string .= '    <th>Entity</th>' . PHP_EOL;
  $string .= '    <th>Location</th>' . PHP_EOL;
  $string .= '    <th>Time Since / Information</th>' . PHP_EOL;
  $string .= '  </tr>' . PHP_EOL;
  $string .= '  </thead>' . PHP_EOL;
  $string .= '  <tbody>' . PHP_EOL;

  $param = array();
  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  // Show Device Status
  if ($status['devices'])
  {
    $query = 'SELECT * FROM `devices` AS D ';
    $query .= $query_perms;
    $query .= 'WHERE D.status = 0' . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $device)
    {
      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td class="entity">' . generate_device_link($device, shorthost($device['hostname'])) . '</td>' . PHP_EOL;
      $string .= '    <td><span class="badge badge-inverse">Device</span></td>' . PHP_EOL;
      $string .= '    <td><span class="label label-important">Device Down</span></td>' . PHP_EOL;
      $string .= '    <td>-</td>' . PHP_EOL;
      $string .= '    <td nowrap>' . substr($device['location'], 0, 30) . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>' . deviceUptime($device, 'short') . '</td>' . PHP_EOL;
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  // Uptime
  if ($status['uptime'])
  {
    if (filter_var($config['uptime_warning'], FILTER_VALIDATE_FLOAT) !== FALSE && $config['uptime_warning'] > 0)
    {
      $query = 'SELECT * FROM `devices` AS D ';
      $query .= $query_perms;
      $query .= 'WHERE D.status = 1 AND D.uptime > 0 AND D.uptime < ' . $config['uptime_warning'] . $query_device . $query_user;
      $query .= 'ORDER BY D.hostname ASC';
      $entries = dbFetchRows($query, $param);
      foreach ($entries as $device)
      {
        $string .= '  <tr>' . PHP_EOL;
        $string .= '    <td class="entity">' . generate_device_link($device, shorthost($device['hostname'])) . '</td>' . PHP_EOL;
        $string .= '    <td><span class="badge badge-inverse">Device</span></td>' . PHP_EOL;
        $string .= '    <td><span class="label label-success">Device Rebooted</span></td>' . PHP_EOL;
        $string .= '    <td>-</td>' . PHP_EOL;
        $string .= '    <td nowrap>' . substr($device['location'], 0, 30) . '</td>' . PHP_EOL;
        $string .= '    <td nowrap>Uptime ' . formatUptime($device['uptime'], 'short') . '</td>' . PHP_EOL;
        $string .= '  </tr>' . PHP_EOL;
      }
    }
  }

  // Ports Down
  if ($status['ports'] || $status['links'])
  {
    // warning about deprecated option: $config['warn']['ifdown']
    if (isset($config['warn']['ifdown']) && !$config['warn']['ifdown'])
    {
      echo('
  <div class="alert">
     <button type="button" class="close" data-dismiss="alert">&times;</button>
     <p><i class="oicon-bell"></i> <strong>Config option obsolete</strong></p>
     <p>Please note that config option <strong>$config[\'warn\'][\'ifdown\']</strong> is now obsolete.<br />Use options: <strong>$config[\'frontpage\'][\'device_status\'][\'ports\']</strong> and <strong>$config[\'frontpage\'][\'device_status\'][\'errors\']</strong></p>
     <p>To remove this message, delete <strong>$config[\'warn\'][\'ifdown\']</strong> from configuration file.</p>
  </div>');
    }

    $query = 'SELECT * FROM `ports` AS I ';
    if ($status['links'] && !$status['ports']) { $query .= 'INNER JOIN links as L ON I.port_id = L.local_port_id '; }
    $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
    $query .= $query_perms;
    $query .= "WHERE I.ifOperStatus = 'down' AND I.ifAdminStatus = 'up' AND I.ignore = 0 AND I.deleted = 0 ";
    if ($status['links'] && !$status['ports']) { $query .= ' AND L.active = 1 '; }
    $query .= $query_device . $query_user;
    $query .= ' AND I.ifLastChange >= DATE_SUB(NOW(), INTERVAL 24 HOUR) ';
    $query .= 'ORDER BY I.ifLastChange DESC, D.hostname ASC, I.ifDescr * 1 ASC ';
    $entries = dbFetchRows($query, $param);
    //$count = count($entries);
    $i = 1;
    foreach ($entries as $port)
    {
      if ($i > 200)
      {
        // Limit to 200 ports on overview page
        $string .= '  <tr><td></td><td><span class="badge badge-info">Port</span></td>';
        $string .= '<td><span class="label label-important">Port Down</span></td>';
        $string .= '<td colspan=3>Too many ports down. See <strong><a href="'.generate_url(array('page'=>'ports'), array('state'=>'down')).'">All DOWN ports</a></strong>.</td></tr>' . PHP_EOL;
        break;
      }
      humanize_port($port);
      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td class="entity">' . generate_device_link($port, shorthost($port['hostname'])) . '</td>' . PHP_EOL;
      $string .= '    <td><span class="badge badge-info">Port</span></td>' . PHP_EOL;
      $string .= '    <td><span class="label label-important">Port Down</span></td>' . PHP_EOL;
      $string .= '    <td class="entity">' . generate_port_link($port, makeshortif($port['label'])) . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>' . substr($port['location'], 0, 30) . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>Down for ' . formatUptime($config['time']['now'] - strtotime($port['ifLastChange']), 'short'); // This is like deviceUptime()
      if ($status['links'] && !$status['ports']) { $string .= ' ('.strtoupper($port['protocol']).': ' .$port['remote_hostname'].' / ' .$port['remote_port'] .')'; }
      $string .= '</td>' . PHP_EOL;
      $string .= '  </tr>' . PHP_EOL;
      $i++;
    }
  }

  // Ports Errors (only deltas)
  if ($status['errors'])
  {
    $query = 'SELECT * FROM `ports` AS I ';
    $query .= 'LEFT JOIN `ports-state` AS E ON I.port_id = E.port_id ';
    $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
    $query .= $query_perms;
    $query .= "WHERE I.ifOperStatus = 'up' AND I.ignore = 0 AND I.deleted = 0 AND (E.ifInErrors_delta > 0 OR E.ifOutErrors_delta > 0)" . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC, I.ifDescr * 1 ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $port)
    {
      humanize_port($port);
      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td class="entity">' . generate_device_link($port, shorthost($port['hostname'])) . '</td>' . PHP_EOL;
      $string .= '    <td><span class="badge badge-info">Port</span></td>' . PHP_EOL;
      $string .= '    <td><span class="label label-important">Port Errors</span></td>' . PHP_EOL;
      $string .= '    <td class="entity">'.generate_port_link($port, makeshortif($port['label']), 'port_errors') . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>' . substr($port['location'], 0, 30) . '</td>' . PHP_EOL;
      $string .= '    <td>Errors ';
      if ($port['ifInErrors_delta']) { $string .= 'In: ' . $port['ifInErrors_delta']; }
      if ($port['ifInErrors_delta'] && $port['ifOutErrors_delta']) { $string .= ', '; }
      if ($port['ifOutErrors_delta']) { $string .= 'Out: ' . $port['ifOutErrors_delta']; }
      $string .= '</td>' . PHP_EOL;
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  // Services
  if ($status['services'])
  {
    $query = 'SELECT * FROM `services` AS S ';
    $query .= 'LEFT JOIN `devices` AS D ON S.device_id = D.device_id ';
    $query .= $query_perms;
    $query .= "WHERE S.service_status = 'down' AND S.service_ignore = 0" . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $service)
    {
      $string .= '  <tr>' . PHP_EOL;
      $string .= '    <td class="entity">' . generate_device_link($service, shorthost($service['hostname'])) . '</td>' . PHP_EOL;
      $string .= '    <td><span class="badge">Service</span></td>' . PHP_EOL;
      $string .= '    <td><span class="label label-important">Service Down</span></td>' . PHP_EOL;
      $string .= '    <td>' . $service['service_type'] . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>' . substr($service['location'], 0, 30) . '</td>' . PHP_EOL;
      $string .= '    <td nowrap>Down for ' . formatUptime($config['time']['now'] - strtotime($service['service_changed']), 'short') . '</td>' . PHP_EOL; // This is like deviceUptime()
      $string .= '  </tr>' . PHP_EOL;
    }
  }

  // BGP
  if ($status['bgp'])
  {
    if (isset($config['enable_bgp']) && $config['enable_bgp'])
    {
      // Description for BGP states
      $bgpstates = 'IDLE - Router is searching routing table to see whether a route exists to reach the neighbor. &#xA;';
      $bgpstates .= 'CONNECT - Router found a route to the neighbor and has completed the three-way TCP handshake. &#xA;';
      $bgpstates .= 'OPEN SENT - Open message sent, with parameters for the BGP session. &#xA;';
      $bgpstates .= 'OPEN CONFIRM - Router received agreement on the parameters for establishing session. &#xA;';
      $bgpstates .= 'ACTIVE - Router did not receive agreement on parameters of establishment. &#xA;';
      //$bgpstates .= 'ESTABLISHED - Peering is established; routing begins.';

      $query = 'SELECT * FROM `devices` AS D ';
      $query .= 'LEFT JOIN bgpPeers AS B ON B.device_id = D.device_id ';
      $query .= $query_perms;
      $query .= "WHERE (bgpPeerAdminStatus = 'start' OR bgpPeerAdminStatus = 'running') AND bgpPeerState != 'established' " . $query_device . $query_user;
      $query .= 'ORDER BY D.hostname ASC';
      $entries = dbFetchRows($query, $param);
      foreach ($entries as $peer)
      {
        $peer_ip = (strstr($peer['bgpPeerRemoteAddr'], ':')) ? Net_IPv6::compress($peer['bgpPeerRemoteAddr']) : $peer['bgpPeerRemoteAddr'];
        $string .= '  <tr>' . PHP_EOL;
        $string .= '    <td class="entity">' . generate_device_link($peer, shorthost($peer['hostname']), array('tab' => 'routing', 'proto' => 'bgp')) . '</td>' . PHP_EOL;
        $string .= '    <td><span class="badge badge-warning">BGP</span></td>' . PHP_EOL;
        $string .= '    <td><span class="label label-important" title="' . $bgpstates . '">BGP ' . strtoupper($peer['bgpPeerState']) . '</span></td>' . PHP_EOL;
        $string .= '    <td nowrap>' . $peer_ip . '</td>' . PHP_EOL;
        $string .= '    <td nowrap>' . substr($peer['location'], 0, 30) . '</td>' . PHP_EOL;
        $string .= '    <td><strong>AS' . $peer['bgpPeerRemoteAs'] . ' :</strong> ' . $peer['astext'] . '</td>' . PHP_EOL;
        $string .= '  </tr>' . PHP_EOL;
      }
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Final print all statuses
  echo($string);
}

/**
 * Display status alerts.
 *
 * Display pages with alerts about device troubles.
 * Examples:
 * print_status(array('devices' => TRUE)) - display for devices down
 *
 * Another statuses:
 * devices, uptime, ports, errors, services, bgp
 *
 * @param array $status
 * @return none
 *
 */

function print_status_boxes($status)
{

  $status_array = get_status_array($status);

  $status_array = array_sort($status_array, 'sev', SORT_DESC);

  foreach($status_array AS $entry)
  {

    if($entry['sev'] > 51) { $class="alert-danger"; } elseif ($entry['sev'] > 20) { $class="alert-warn"; } else { $class="alert-info"; }
    if($entry['wide']) { $class .= ' statusbox-wide'; }

    echo('<div class="alert statusbox '.$class.'">');
    echo('<h4>'.$entry['device_link'].'</h4>');
    echo('<p>');
    echo($entry['class'] .' '.$entry['event'].'<br />');
    echo('<span class="entity">'.$entry['entity_link'].'</span><br />');
    echo('<small>'.$entry['time'].'</small><br />');
    echo('</p>');
    echo('</div>');

  }

#  echo("<pre>");
#  print_r($status_array);
#  echo("</pre>");

}


function get_status_array($status)
{
  // Mike: I know that there are duplicated variables, but later will remove global
  global $config;
  global $cache;

  $param = array();
  if ($_SESSION['userlevel'] >= 5)
  {
    $query_perms = '';
    $query_user = '';
  } else {
    $query_perms = 'LEFT JOIN devices_perms AS P ON D.device_id = P.device_id ';
    $query_user = ' AND P.user_id = ? ';
    $param[] = $_SESSION['user_id'];
  }

  // Don't show ignored and disabled devices
  $query_device = ' AND D.ignore = 0 ';
  if (!$config['web_show_disabled']) { $query_device .= 'AND D.disabled = 0 '; }

  // Show Device Status
  if ($status['devices'])
  {
    $query = 'SELECT * FROM `devices` AS D ';
    $query .= $query_perms;
    $query .= 'WHERE D.status = 0' . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $device)
    {
      $boxes[] = array('sev' => 100, 'class' => 'Device', 'event' => 'Down', 'device_link' => generate_device_link($device, shorthost($device['hostname'])),
                       'time' => deviceUptime($device, 'short-3'));
    }
  }

  // Uptime
  if ($status['uptime'])
  {
    if (filter_var($config['uptime_warning'], FILTER_VALIDATE_FLOAT) !== FALSE && $config['uptime_warning'] > 0)
    {
      $query = 'SELECT * FROM `devices` AS D ';
      $query .= $query_perms;
      $query .= 'WHERE D.status = 1 AND D.uptime > 0 AND D.uptime < ' . $config['uptime_warning'] . $query_device . $query_user;
      $query .= 'ORDER BY D.hostname ASC';
      $entries = dbFetchRows($query, $param);
      foreach ($entries as $device)
      {
        $boxes[] = array('sev' => 10, 'class' => 'Device', 'event' => 'Rebooted', 'device_link' => generate_device_link($device, shorthost($device['hostname'])),
                         'time' => deviceUptime($device, 'short-3'), 'location' => $device['location']);
      }
    }
  }

  // Ports Down
  if ($status['ports'] || $status['links'])
  {
    // warning about deprecated option: $config['warn']['ifdown']
    if (isset($config['warn']['ifdown']) && !$config['warn']['ifdown'])
    {
      echo('
  <div class="alert">
     <button type="button" class="close" data-dismiss="alert">&times;</button>
     <p><i class="oicon-bell"></i> <strong>Config option obsolete</strong></p>
     <p>Please note that config option <strong>$config[\'warn\'][\'ifdown\']</strong> is now obsolete.<br />Use options: <strong>$config[\'frontpage\'][\'device_status\'][\'ports\']</strong> and <strong>$config[\'frontpage\'][\'device_status\'][\'errors\']</strong></p>
     <p>To remove this message, delete <strong>$config[\'warn\'][\'ifdown\']</strong> from configuration file.</p>
  </div>');
    }

    $query = 'SELECT * FROM `ports` AS I ';
    if ($status['links'] && !$status['ports']) { $query .= 'INNER JOIN links as L ON I.port_id = L.local_port_id '; }
    $query .= 'LEFT JOIN `devices` AS D ON I.device_id = D.device_id ';
    $query .= $query_perms;
    $query .= "WHERE I.ifOperStatus = 'down' AND I.ifAdminStatus = 'up' AND I.ignore = 0 AND I.deleted = 0 ";
    if ($status['links'] && !$status['ports']) { $query .= ' AND L.active = 1 '; }
    $query .= $query_device . $query_user;
    $query .= ' AND I.ifLastChange >= DATE_SUB(NOW(), INTERVAL 24 HOUR) ';
    $query .= 'ORDER BY I.ifLastChange DESC, D.hostname ASC, I.ifDescr * 1 ASC ';
    $entries = dbFetchRows($query, $param);
    //$count = count($entries);
    $i = 1;
    foreach ($entries as $port)
    {
      if ($i > 200)
      {
        // Limit to 200 ports on overview page
        $string .= '  <tr><td></td><td><span class="badge badge-info">Port</span></td>';
        $string .= '<td><span class="label label-important">Port Down</span></td>';
        $string .= '<td colspan=3>Too many ports down. See <strong><a href="'.generate_url(array('page'=>'ports'), array('state'=>'down')).'">All DOWN ports</a></strong>.</td></tr>' . PHP_EOL;
        break;
      }
      humanize_port($port);
      $boxes[] = array('sev' => 50, 'class' => 'Port', 'event' => 'Down', 'device_link' => generate_device_link($port, shorthost($port['hostname'])),
                       'entity_link' => generate_port_link($port, truncate(makeshortif($port['label']),13,'')),
                       'time' => formatUptime($config['time']['now'] - strtotime($port['ifLastChange'])), 'location' => $device['location']);

      // We don't do anything with this here at the moment. There is no comment on it, what is it for?
      // if ($status['links'] && !$status['ports']) { $string .= ' ('.strtoupper($port['protocol']).': ' .$port['remote_hostname'].' / ' .$port['remote_port'] .')'; }

    }
  }

  // Ports Errors (only deltas)
  if ($status['errors'])
  {

    foreach ($cache['ports_errored'] as $port_id)
    {
      $port = get_port_by_id($port_id);
      if(port_permitted($port))
      {
        $device = device_by_id_cache($port['device_id']);
        humanize_port($port);

        if ($port['ifInErrors_delta']) { $port['string'] .= 'Rx: ' . $port['ifInErrors_delta']; }
        if ($port['ifInErrors_delta'] && $port['ifOutErrors_delta']) { $port['string'] .= ', '; }
        if ($port['ifOutErrors_delta']) { $port['string'] .= 'Tx: ' . $port['ifOutErrors_delta']; }

        $boxes[] = array('sev' => 75, 'class' => 'Port', 'event' => 'Errors', 'device_link' => generate_device_link($device, shorthost($device['hostname'])),
                       'entity_link' => generate_port_link($port, truncate(makeshortif($port['label']),13,'')),
                       'time' => $port['string'], 'location' => $device['location']);

      }
    }
  }

  // Services
  if ($status['services'])
  {
    $query = 'SELECT * FROM `services` AS S ';
    $query .= 'LEFT JOIN `devices` AS D ON S.device_id = D.device_id ';
    $query .= $query_perms;
    $query .= "WHERE S.service_status = 'down' AND S.service_ignore = 0" . $query_device . $query_user;
    $query .= 'ORDER BY D.hostname ASC';
    $entries = dbFetchRows($query, $param);
    foreach ($entries as $service)
    {
      $boxes[] = array('sev' => 50, 'class' => 'Service', 'event' => 'Down', 'device_link' => generate_device_link($service, shorthost($service['hostname'])),
                       'entity_link' => $service['service_type'],
                       'time' => formatUptime($config['time']['now'] - strtotime($service['service_changed']), 'short'), 'location' => $device['location']);
    }
  }

  // BGP
  if ($status['bgp'])
  {
    if (isset($config['enable_bgp']) && $config['enable_bgp'])
    {
      // Description for BGP states
      $bgpstates = 'IDLE - Router is searching routing table to see whether a route exists to reach the neighbor. &#xA;';
      $bgpstates .= 'CONNECT - Router found a route to the neighbor and has completed the three-way TCP handshake. &#xA;';
      $bgpstates .= 'OPEN SENT - Open message sent, with parameters for the BGP session. &#xA;';
      $bgpstates .= 'OPEN CONFIRM - Router received agreement on the parameters for establishing session. &#xA;';
      $bgpstates .= 'ACTIVE - Router did not receive agreement on parameters of establishment. &#xA;';
      //$bgpstates .= 'ESTABLISHED - Peering is established; routing begins.';

      $query = 'SELECT * FROM `devices` AS D ';
      $query .= 'LEFT JOIN bgpPeers AS B ON B.device_id = D.device_id ';
      $query .= $query_perms;
      $query .= "WHERE (bgpPeerAdminStatus = 'start' OR bgpPeerAdminStatus = 'running') AND bgpPeerState != 'established' " . $query_device . $query_user;
      $query .= 'ORDER BY D.hostname ASC';
      $entries = dbFetchRows($query, $param);
      foreach ($entries as $peer)
      {
        $peer_ip = (strstr($peer['bgpPeerRemoteAddr'], ':')) ? Net_IPv6::compress($peer['bgpPeerRemoteAddr']) : $peer['bgpPeerRemoteAddr'];
        if (strstr($peer['bgpPeerRemoteAddr'], ':')) { $peer['wide'] = TRUE;  }

        $boxes[] = array('sev' => 75, 'class' => 'BGP Peer', 'event' => 'Down', 'device_link' => generate_device_link($peer, shorthost($peer['hostname'])),
                         'entity_link' => $peer_ip, 'wide' => $peer['wide'],
                         'time' => formatUptime($peer['bgpPeerFsmEstablishedTime'], 'shorter'), 'location' => $device['location']);

      }
    }
  }

  $string .= '  </tbody>' . PHP_EOL;
  $string .= '</table>';

  // Final print all statuses
  return $boxes;
}


?>
