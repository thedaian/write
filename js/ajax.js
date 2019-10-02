function createRequestObject()
{
	var req;
	try
	{
		req = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			req = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (oc) {
		req = null;
		}
	}
	if(!req && typeof XMLHttpRequest != "undefined")
	{
		req = new XMLHttpRequest();
	}
	if (!req)
	{
		alert("Could not create connection object.");
	}
	return req;
}

function post(url, opt, callback)
{
	var http = createRequestObject();
	if(!http||!url) return;
	
	var params = objToString(opt);
	
	params += "uid=" + new Date().getTime();

	http.open("POST", url, true);
	
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	http.onreadystatechange = function ()
	{//Call a function when the state changes.
		if (http.readyState == 4)
		{//Ready State will be 4 when the document is loaded.
			if(http.status == 200)
			{
				var result = "";
				if(http.responseText)
				{
					result = JSON.parse(http.responseText);
				}
				
				//Give the data to the callback function.
				if(callback)
				{
					callback(result);
				}
			} else {
				alert("Error saving to server..." + http.status);
			}
		}
	}
	http.send(params);
}

function objToString(obj) {
    var str = '';
    for(var p in obj) {
        if(obj.hasOwnProperty(p)) {
			if(typeof obj[p] === 'object') {
				str += objToString(obj[p]);
			} else {
				str += p + '=' + obj[p] + '&';
			}
        }
    }
    return str;
}