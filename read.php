<?php
require_once 'inc/Database.php';

$ip = (isset($_GET['i'])) ? intval($_GET['i']) : ip2long($_SERVER['REMOTE_ADDR']);
$w = (isset($_GET['w'])) ? intval($_GET['w']) : 1;

$sql = "SELECT `id`, `title`, `content` FROM `writings` WHERE `ip`=? AND `id`=?";
$writing = $db->get_one_row($sql, array('ip' => $ip, 'id' => $w) );
?>
<!DOCTYPE html>
<html>
<head>
<title>Tetris Writer - Read <?php echo $writing['title']; ?></title>
<link rel="stylesheet" href="inc/index.css">
<style>
textarea {
	width: 95%;
	height: 800px;
}
</style>
</head>
<body>
<h1><?php echo $writing['title']; ?></h1>

<textarea readonly><?php echo $writing['content']; ?></textarea>
<hr>
<a href="write.php?w=<?php echo $writing['id']; ?>">Write More</a> - <a href="index.php">Back to listing</a>
</body>