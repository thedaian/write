<?php
$page_start = microtime(TRUE);

require 'sessionHandling.php';

function load_page($file, $title)
{
	require 'Page.php';
	global $page, $tags;
	
	$tags['title'] = $title;
	$tags['page'] = $title;

	$page = new Page($file);
}

if(isset($_POST['action']) && ($_POST['action']=='register')) {
	require_once 'Database.php';
	
	$db->query("INSERT INTO `accounts` (`ip`, `showWordCount`, `autoHide`, `user_account_id`, `identity`) VALUES(?, false, false, ?, ?)",
			array("ip" => $_SERVER['REMOTE_ADDR'], "user_account_id" => $last_id, "identity" => $_POST['identity']));
	$db->query("INSERT INTO `open_ids` () VALUES(?, ?)",
			array();
	
	session_name('write');
	session_start();
	
	$_SESSION['logged'] = true;
	$_SESSION['userid'] = $db->last();
	
	if(SERVER)
	{
		header('Location: http://barchok.com/p/write/');
	} else {
		header('Location: http://localhost/write/');
	}
}

require 'lightopenid/openid.php';
require 'config.php';

# Change 'localhost' to your domain name.
$domain = 'localhost';
if(SERVER)
{
	$domain = 'barchok.com';
}
$openid = new LightOpenID($domain);
if(!$openid->mode)
{
	if(isset($_POST['openid_identifier']))
	{
		switch($_POST['openid_identifier'])
		{
			case "google":
				$openid->identity = 'https://www.google.com/accounts/o8/id';
				break;
			case "yahoo":
				$openid->identity = 'https://open.login.yahooapis.com/openid/op/auth';
				break;
			default:
				die("ERROR! NO OPEN ID SPECIFIED");
				break;
		}
		header('Location: ' . $openid->authUrl());
	}

	load_page('Login', 'login');
	
	$tags['message'] = 'Please login';
} else {
	if($openid->mode == 'cancel')
	{
		load_page('Login', 'login');
		$tags['message'] = 'Login canceled!';
	} else {
		if($openid->validate())
		{
			require_once 'Database.php';
			
			$account = $db->get_one_row("SELECT `user_account_id` FROM `open_ids` WHERE `identity`=?", array('identity' => $openid->identity));
			
			if(empty($account['user_account_id']))
			{
				load_page('Register', 'register');
				
				$tags['message'] = 'Success!  Please create a user account';
				$tags['identity'] = $openid->identity;
			} else {
				session_name("validmon");
				session_start();
				$_SESSION['logged'] = true;
				$_SESSION['userid'] = $account['user_account_id'];
				
				header('Location: http://localhost/Validmon/game.php');
			}
		} else {
			load_page('Login', 'login');
			$tags['message'] = 'Login failed.';
		}
	}
}
$page->output($tags);
?>