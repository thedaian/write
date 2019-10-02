<?php
if(!isset($_POST['id']) || !isset($_POST['paragraphs']) || !isset($_POST['words']) || !isset($_POST['characters']) || !isset($_POST['all']) || !isset($_POST['title']) || !isset($_POST['content']))
{
	if(isset($_POST['wordCount']) && isset($_POST['hide']))
	{
		require_once 'Database.php';
		
		$vals = array(($_POST['wordCount'] == 'true') ? 1 : 0, ($_POST['hide'] == 'true') ? 1 : 0);
		
		$sql = "UPDATE `accounts` SET `showWordCount` = ?, `autoHide` = ? WHERE `ip` = " . ip2long($_SERVER['REMOTE_ADDR']);
		$db->query($sql, $vals);
		
		die('{ "status": true }');
	} else {
		die('{ "status": false }');
	}
}

require_once 'Database.php';

$sql = "SELECT `id`, `content` FROM `writings` WHERE `ip`=".ip2long($_SERVER['REMOTE_ADDR'])." AND `id`=?";
$writing = $db->get_one_row($sql, array('id' => intval($_POST['id'])));

$content = $_POST['content'];

if(empty($writing['id']))
{
	$sql = "INSERT INTO `writings` (`ip`, `paragraphs`, `words`, `characters`, `all`, `title`, `content`, `id`) VALUES(".ip2long($_SERVER['REMOTE_ADDR']).", ?, ?, ?, ?, ?, ?, ?)";
} else {
	$content = $writing['content'] . $_POST['content'];
	$sql = "UPDATE `writings` SET `paragraphs` = ?, `words` = ?, `characters` = ?, `all` = ?, `title` = ?, `content` = ? WHERE `ip` = ".ip2long($_SERVER['REMOTE_ADDR'])." AND `id` = ?";
}

$db->query($sql, array(intval($_POST['paragraphs']), intval($_POST['words']), intval($_POST['characters']), intval($_POST['all']), $_POST['title'], $content, intval($_POST['id'])) );

echo '{ "status": true }';
?>