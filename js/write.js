function checkKey(element, event)
{
	var charCode = (typeof event.which === 'number') ? event.which : event.keyCode;
	if(charCode === 13)
	{
		saveToServer(element);
	}
}

function saveToServer(element)
{
	document.getElementById('saved').className = '';
	options.title = document.getElementById('title').value;
	
	Countable.once(element, function (counter) {
		options.count.paragraphs += counter.paragraphs;
		options.count.words += counter.words;
		options.count.characters += counter.characters;
		options.count.all += counter.all;
		
		post('inc/save.php', { 'options': options, 'content': encodeURI(element.value) }, function(result) {
			if(result.status)
			{
				var saved = document.getElementById('saved');
				saved.offsetWidth = saved.offsetWidth;
				saved.className = 'anim';
				
				element.value = '';
			}
		});
	});
	
}

function onLoad()
{
	var text = document.getElementById('text');
	text.style.height = (window.innerHeight - 6) + 'px';
	
	if(options.wordCount)
	{
		updateWordCountDisplay(options.count.paragraphs, options.count.words, options.count.characters, options.count.all);
		wordCount(true);
		document.getElementById('wordcount').checked = true;
	} else {
		document.getElementById('count').style.display = 'none';
	}
	
	if(options.hide)
	{
		autoHide(true);
		document.getElementById('autohide').checked = true;
	}
	
	document.getElementById('saved').className = 'anim';
	document.getElementById('writing_area').focus();
}

function autoHide(is)
{
	document.getElementById('saved').className = '';
	
	if(is)
	{
		options.hide = true;
		autoShow(false);
		document.getElementById('icon').style.display = 'inline';
	} else {
		options.hide = false;
		document.getElementById('icon').style.display = 'none';
		document.getElementById('autohide').checked = false;
	}
	
	post('inc/save.php', { 'wordCount': options.wordCount, 'hide': options.hide }, function(result) {
		if(result.status)
		{
			var saved = document.getElementById('saved');
			saved.offsetWidth = saved.offsetWidth;
			saved.className = 'anim';
		}
	});
}

function autoShow(value)
{
	if(!options.hide)
	{
		return;
	}
	var count = document.getElementById('count');
	var controls = document.getElementById('controls');
	if(value)
	{
		if(options.wordCount)
		{
			count.style.display = 'inline';
		}
		
		controls.style.display = 'inline';
	} else {
		count.style.display = 'none';
		controls.style.display = 'none';
	}
}

function wordCount(is)
{
	var area = document.getElementById('writing_area');
	document.getElementById('saved').className = '';

	var count = document.getElementById('count');
	if(is)
	{
		options.wordCount = true;
		Countable.live(area, function (counter) {
			updateWordCountDisplay(options.count.paragraphs + counter.paragraphs, options.count.words + counter.words, options.count.characters + counter.characters, options.count.all + counter.all);
		});
		
		if(!options.hide)
		{
			count.style.display = 'inline';
		}
	} else {
		options.wordCount = false;
		Countable.die(area);
		
		count.style.display = 'none';
	}
	
	post('inc/save.php', { 'wordCount': options.wordCount, 'hide': options.hide }, function(result) {
		if(result.status)
		{
			var saved = document.getElementById('saved');
			saved.offsetWidth = saved.offsetWidth;
			saved.className = 'anim';
		}
	});
}

function updateWordCountDisplay(p, w, c, a)
{
	document.getElementById('count_paragraphs').innerHTML = p;
	document.getElementById('count_words').innerHTML = w;
	document.getElementById('count_characters').innerHTML = c;
	document.getElementById('count_all').innerHTML = a;
}