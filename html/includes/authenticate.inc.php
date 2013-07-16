<?php

@ini_set("session.gc_maxlifetime","0");

session_start();

# Fallback to MySQL auth as default
if (!isset($config['auth_mechanism']))
{
  $config['auth_mechanism'] = "mysql";
}

if (file_exists('includes/authentication/' . $config['auth_mechanism'] . '.inc.php'))
{
  include('includes/authentication/' . $config['auth_mechanism'] . '.inc.php');
}
else
{
  print_error('ERROR: no valid auth_mechanism defined!');
  exit();
}

if ($vars['page'] == "logout" && $_SESSION['authenticated'])
{
  if (auth_can_logout())
  {
    dbInsert(array('user' => $_SESSION['username'], 'address' => $_SERVER["REMOTE_ADDR"], 'result' => 'Logged Out'), 'authlog');
    unset($_SESSION);
    session_destroy();
    setcookie ("username", "", time() - 60*60*24*100, "/");
    setcookie ("password", "", time() - 60*60*24*100, "/");
    $auth_message = "Logged Out";
  }
  header('Location: /');
}

if (isset($_GET['username']) && isset($_GET['password']))
{
  $_SESSION['username'] = mres($_GET['username']);
  $_SESSION['password'] = $_GET['password'];
} elseif (isset($_POST['username']) && isset($_POST['password'])) {
  $_SESSION['username'] = mres($_POST['username']);
  $_SESSION['password'] = $_POST['password'];
} elseif (isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
  $_SESSION['username'] = mres($_COOKIE['username']);
  $_SESSION['password'] = $_COOKIE['password'];
}

$auth_success = 0;

if (isset($_SESSION['username']))
{
  if (authenticate($_SESSION['username'],$_SESSION['password']) || auth_user_level($_SESSION['origusername']) >= 10)
  {
    $_SESSION['userlevel'] = auth_user_level($_SESSION['username']);
    $_SESSION['user_id'] = auth_user_id($_SESSION['username']);
    if (!$_SESSION['authenticated'])
    {
      $_SESSION['authenticated'] = true;
      dbInsert(array('user' => $_SESSION['username'], 'address' => $_SERVER["REMOTE_ADDR"], 'result' => 'Logged In'), 'authlog');
      header("Location: ".$_SERVER['REQUEST_URI']);
    }
    if (isset($_POST['remember']))
    {
      setcookie("username", $_SESSION['username'], time()+60*60*24*100, "/");
      setcookie("password", $_SESSION['password'], time()+60*60*24*100, "/");
    }
    $permissions = permissions_cache($_SESSION['user_id']);
  }
  elseif (isset($_SESSION['username']))
  {
    $auth_message = "Authentication Failed";
    unset ($_SESSION['authenticated']);
    dbInsert(array('user' => $_SESSION['username'], 'address' => $_SERVER["REMOTE_ADDR"], 'result' => 'Authentication Failure'), 'authlog');
  }
}

?>
