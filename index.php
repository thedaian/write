<?php
require_once 'inc/Database.php';

$ip = 0;

if(isset($_GET['i'])) 
{
	$ip = intval($_GET['i']);
} else {
	$ip = ip2long($_SERVER['REMOTE_ADDR']);
	
	$sql = "SELECT `ip`, `showWordCount`, `autoHide` FROM `accounts` WHERE `ip`=?";
	$account = $db->get_one_row($sql, array('ip' => $ip) );

	if(empty($account['ip']))
	{
		$sql = "INSERT INTO `accounts` (`ip`, `showWordCount`, `autoHide`) VALUES(".$ip.", false, false)";
		$db->query($sql, array());
	}
}

$sql = "SELECT `id`, `title`, `paragraphs`, `words`, `characters`, `all`, `published` FROM `writings` WHERE `ip`=?";
$db->query($sql, array('ip' => $ip) );
?>
<!DOCTYPE html>
<html>
<head>
<title>Tetris Writer - All Writings</title>
<link rel="stylesheet" href="inc/index.css">
</head>
<body>
<h1>Welcome to Tetris Writer</h1>
<p>Click on a name to edit your writing, or click on "New Writing" to start something new.</p>
<table>
	<tr>
		<th>Title</th><th>Read</th><th>Paragraphs</th><th>Words</th><th>Characters</th><th>All Characters</th>
	</tr>
	
	<?php
	$print		= '<tr><td><a href="write.php?w=%d">%s</a></td><td>%s</td><td>%d</td><td>%d</td><td>%d</td><td>%d</td></tr>';
	$readBase	= '<a href="read.php?w=%d">Read</a>';
	
	$writing = array();
	$db->bind($writing);
	
	while($db->fetch_row())
	{
		if($writing['published']) {
			$read = sprintf($readBase, $writing['id']);
		} else {
			$read = 'Not Yet';
		}

		printf($print, $writing['id'], $writing['title'], $read, $writing['paragraphs'], $writing['words'], $writing['characters'], $writing['all']);
	}
	$next = intval($writing['id']) + 1;
	?>
</table>
<br>
<a class="new" href="write.php?w=<?php echo $next; ?>">New Writing</a>
<h2>About Tetris Writer</h2>
<p>Tertris Writer is the most distraction free way to write. Everytime you hit 'Enter', what you've written gets saved to the server, and then disappears, keeping the screen blank. Included is a simple word count tracker. <a href="write.php?w=<?php echo $next; ?>">Try it yourself</a></p>
<hr>
<p>Note: Tetris Writer currently uses IP addresses to store your information. Do not put anything important here.
</body>