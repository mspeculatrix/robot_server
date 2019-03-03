/*  *************************************************************************
	*****   local.js  ||  for remconfig app                               ***
    *************************************************************************/ 

/* Info here about Jquery get method:
	https://www.w3schools.com/jquery/ajax_get.asp
	*/

let robotIP = '10.0.0.35';
let robotPort = '3000';

let remSvrWebIP = '10.0.0.20';	// 35 is ada dev machine
let remSvrWebPort = '8081';
let remSvrWSPort = '8181';
let remSvrAPIPort = '5000';
const ipPattern = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/;

/* ----- FUNCTIONS ---------------------------------------------------------- */


/* ----- DOCUMENT READY ----------------------------------------------------- */
$(document).ready(function() {
	window.onerror = function(msg, url, line, col, error) {
		var extra = !col ? '' : '\ncolumn: ' + col;
		extra += !error ? '' : '\nerror: ' + error;
		alert("Error: " + msg + "\nurl: " + url + "\nline: " + line + extra);
	}
	$('#robotIPField').attr('value', robotIP);
	$('#robotPortField').attr('value', robotPort);
	$('#remWebSvrIPField').attr('value', remSvrWebIP);
	$('#remWebSvrPortField').attr('value', remSvrWebPort);
	$('#remWebSvrWSPortField').attr('value', remSvrWSPort);
	$('#remWebSvrAPIPortField').attr('value', remSvrAPIPort);
	$('#robotAddr').html(robotIP + ":" + robotPort);

	$('#setRobotCfg').on('click', () => {
		robotIP = $('#robotIPField').val();
		robotPort = $('#robotPortField').val();
		$('#robotAddr').html(robotIP + ":" + robotPort);
	});

	$('#submitCfg').on('click', function() {
		$('#respRecvd').html("")
		remSvrWebIP = $('#remWebSvrIPField').val();
		remSvrWebPort = $('#remWebSvrPortField').val();
		remSvrWSPort = $('#remWebSvrWSPortField').val();
		remSvrAPIPort = $('#remWebSvrAPIPortField').val();
		robotIP = $('#robotIPField').val();
		robotPort = $('#robotPortField').val();
		if(ipPattern.test(remSvrWebIP)) {
			let sheldonURL = 'http://' + robotIP + ':' + robotPort + '/remcfg'
			$.get(sheldonURL, {
					remSvrWebIP:remSvrWebIP, 
					remSvrWebPort:remSvrWebPort,
					remSvrWSPort:remSvrWSPort,
					remSvrAPIPort:remSvrAPIPort
				}, (data, _status, xhr) => {
				$('#respStatus').html(xhr.status);
				$('#respRecvd').html(data);		
			});
		} else {
			remSvrWebIP = '';
			alert('Not a valid IP address!');
		}
	});
});
