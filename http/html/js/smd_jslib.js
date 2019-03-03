/** ************************************************************************************************
	*****   smd_jslib.js  ||  Basic JavaScript Library                                           ***
    ************************************************************************************************ **/


function changeClass(el, currentCls, newCls) {
	/*  Swaps one class in an element for another. */
	var element = document.getElementById(el);
	element.classList.remove(currentCls);
	if (newCls && newCls != '') element.classList.add(newCls);
}

/** ************************************************************************************************
	*****   AJAX FUNCTIONS                                                                       ***
    ************************************************************************************************ **/

function ajaxError() {
	/*  This is meant to be a default error function if you don't want to implement one
		in a specific app */
	console.log('Ajax error encountered');
}

function getAjax(url, responseHandlerFunction, errorFunction) {
	/*  errorFunction	If there's no specific error function in the app, just pass ajaxError */
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onload = function() {
        if (xhr.status == 200 || xhr.status == 304) {
        	responseHandlerFunction(xhr.responseText);
        } else if (xhr.status >= 400) {
        	ajaxError('JASON file not found');
        }
    }
    xhr.onerror = errorFunction;
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.send();
    return xhr;
}

function postAjax(url, data, responseHandlerFunction, errorFunction) {
	/*  url 			url of the remote app
		data			data as an associative array or a URIencoded string already formatted with
						'&' delimeters between items and with items in the form key=data
		responseHandlerFunction	the function in the app code that will accept the xhr.responseText
						and do stuff with it.           
    	errorFunction	If there's no specific error function in the app, just pass 
                        the placeholder ajaxError function above */
    var params = typeof data == 'string' ? data : Object.keys(data).map(
            function(k){
                return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) 
            }).join('&');
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url);
	xhr.onload = function() {
         if (xhr.status == 200 || xhr.status == 304) {
        	responseHandlerFunction(xhr.responseText);
        } else if (xhr.status >= 400) {
        	ajaxError('JASON file not found');
        }
    }
    xhr.onerror = errorFunction;
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(params);
    return xhr;
}
