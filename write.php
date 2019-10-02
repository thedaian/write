<?php
require_once 'inc/Database.php';

$ip = 0;

if(isset($_GET['i'])) 
{
	$ip = intval($_GET['i']);
	$account['showWordCount']	= true;
	$account['autoHide']		= false;
} else {
	$sql = "SELECT `ip`, `showWordCount`, `autoHide` FROM `accounts` WHERE `ip`=?";
	$account = $db->get_one_row($sql, array('ip' => ip2long($_SERVER['REMOTE_ADDR'])) );

	if(empty($account['ip']))
	{
		$account['showWordCount']	= false;
		$account['autoHide']		= false;
		$sql = "INSERT INTO `accounts` (`ip`, `showWordCount`, `autoHide`) VALUES(".ip2long($_SERVER['REMOTE_ADDR']).", false, false)";
		$db->query($sql, array());
	}
}

$w = (isset($_GET['w'])) ? intval($_GET['w']) : 1;

$sql = "SELECT `id`, `paragraphs`, `words`, `characters`, `all` FROM `writings` WHERE `ip`=? AND `id`=?";
$writing = $db->get_one_row($sql, array('ip' => $ip, 'id' => $w) );

if(empty($writing['id']))
{
	$writing['id']			= $w;
	$writing['paragraphs']	= 0;
	$writing['words']		= 0;
	$writing['characters']	= 0;
	$writing['all']			= 0;
	$writing['title']		= 'Untitled';
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Tetris Writer</title>
<script>
var options = {
	'id': <?php echo $writing['id']; ?>,
	'wordCount': <?php echo ($account['showWordCount']) ? 'true' : 'false'; ?>,
	'hide': <?php echo ($account['autoHide']) ? 'true' : 'false'; ?>,
	'title': '<?php echo $writing['title']; ?>',
	'count': {
		'paragraphs': <?php echo $writing['paragraphs']; ?>,
		'words': <?php echo $writing['words']; ?>,
		'characters': <?php echo $writing['characters']; ?>,
		'all': <?php echo $writing['all']; ?>
	}
};
</script>
<script src="js/counter.min.js" type="text/javascript"></script>
<script src="js/ajax.js" type="text/javascript"></script>
<script src="js/write.js" type="text/javascript"></script>
<link rel="stylesheet" href="inc/write.css">
</head>
<body onLoad="onLoad()">
<div id="text">
	<textarea onkeyup="checkKey(this, event)" id="writing_area"></textarea>
</div>

<div id="info">
	<a id="icon" onMouseOver="autoShow(true)" onMouseOut="autoShow(false)" onClick="autoHide(false)">?</a>
	<div id="controls">
		<label for="title">Title:</label> <input id="title" type="textbox" value="<?php echo $writing['title']; ?>" size="50" type="text">
		<input type="checkbox" onChange="wordCount(this.checked)" id="wordcount"><label for="wordcount">Word Count</label> <input type="checkbox" onChange="autoHide(this.checked)" id="autohide"><label for="autohide">Autohide Status</label>
	</div>
	<div id="count">Words: <span id="count_words">0</span> Characters: <span id="count_characters">0</span> All characters: <span id="count_all">0</span> Paragraphs: <span id="count_paragraphs">0</span></div>
	<div id="saved">Saved...</div>
</div>
</body>