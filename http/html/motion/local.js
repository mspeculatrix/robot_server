/*  *************************************************************************
	*****   local.js  ||  for motion app                                  ***
    ************************************************************************* 

This web app uses websockets to communicate with a remote comms node on
the Sheldon robot. Currently, this is an ESP8266 board, but it could be
anything - a Raspberry Pi, for instance.

Resources for websockets:
https://www.tutorialspoint.com/html5/html5_websocket.htm
*/

//---NEW STUFF __ IN PROGRESS ---------------------------------
/*
#define HDG_REVERSE 0	// these first two values need to be 0 and 1
#define HDG_FORWARD 1	// for logic reasons. The rest are arbitrary
#define HDG_BRAKE 2
#define HDG_STOPPED 3
#define HDG_UNKNOWN -1

// ---- MESSAGE CODES ------------------
#define MSG_FORWARD		'F'
#define MSG_REVERSE		'B'
#define MSG_LEFT 		'L'
#define MSG_RIGHT 		'R'

#define MT_DIR_RIGHT 0b00000001
#define MT_DIR_LEFT  0b00000010

#define MT_TURN_ROTATE 	0b00000100 // 4
#define MT_TURN_SWING	0b00001000 // 8
#define MT_TURN_CURVE	0b00001100 // 12

#define MT_TURN_ROTATE_RIGHT 	5	// derived from adding above values
#define MT_TURN_ROTATE_LEFT		6
#define MT_TURN_SWING_RIGHT		9
#define MT_TURN_SWING_LEFT		10
#define MT_TURN_CURVE_RIGHT		13
#define MT_TURN_CURVE_LEFT		14
*/
const msgCat = {MOTOR:'M', SENSOR:'S'};
//const motorCmd = {START:'S', STOP:'X', TURN:'T', MOVE:'M', HEADING:'H', SPEED:'V'};
const motorHdg = {REVERSE:0, FORWARD:1};
const motorDir = {RIGHT:1, LEFT:2};
const motorTurnType = {ROTATE:4, SWING: 8, CURVE:12}

const motorSpeedIdx = {CRAWL:0, SLOW:1, NORMAL:2, FAST:3};
const motorSpeedStates = ['CRAWL', 'SLOW', 'NORMAL', 'FAST'];
const motorSpeedVals = [40, 64, 110, 192];

const motionState = {STOP:0, START:1};
const motionStateCmd = ['X', 'S'];

const motionStateLabel = ['STOP', 'START'];
const headingStates = ['REVERSE', 'FORWARD'];
const hStateBtnCls = ['btn-primary', 'btn-success'];

//---END OF NEW STUFF------------------------------------------
//const motionStateCodes = ['X', 'S'];
const mStateBtnCls = ['btn-danger', 'btn-warning'];

//const headingMsgCodes = ['B', 'F'];

const speedStateLabel = ['STOP', 'CRAWL', 'SLOW', 'NORMAL', 'FAST'];


//const turnTypeCodes = ['ROTL', 'VERL', 'ROTR', 'VERR'];
//const turns = {ROTATE_L:0, VEER_L:1, ROTATE_R:2, VEER_R:3};

const ipPattern = /^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/;

let socket = null;		// for websocket
let ip = '10.0.40.17';	// 35 is ada dev machine
let port = '8181';		// because why not?
let connected = false;
let mState = motionState.STOP;
let heading = motorHdg.FORWARD;
let speed = motorSpeedIdx.NORMAL;
let recvdMsg;
let pingSent = false;
let pongRecvd = false;
let connectTimerID = 0;

//let flashConnectBtnIntervalID = 0;

/* ----- FUNCTIONS ---------------------------------------------------------- */

function ping() {
	pingSent = true;
	pongRecvd = false;	// reset
	recvdMsg = "";
	let pongTimerID = setTimeout(pongTimeout, 2000);
	socketSend('PING');
}

function pongTimeout() {
	// assumes a ping has been sent
	if(pongRecvd == true) {	// everything's okay
		pongRecvd = false;
		$('#pingBtn').html('ping');	
	} else {
		reset();
	}
}

function reset() {
	// take things back to the default state
	mState = motionState.STOP;
	connected = false;
	$('#msg').html('');
	$('#connectBtn').html('connect');
	$('#connectBtn').removeClass('btn-success').addClass('btn-warning');
	$('#pingBtn').html('ping');
	$('#pingBtn').hide();
	setStartStopButton(mState)
}

function connectTimerTimeout() {
	if(socket.readyState == 1) {
		$('#msg').html('connected');
	} else {
		alert('Not connected');
		reset();	
	}
}

function receiveMsg(event) {
	recvdMsg = event.data;
	if(recvdMsg == "OK" && pingSent) {
		pongRecvd = true; pingSent = false;
		$('#pingBtn').html('ping ok');	 
	}
	$('#msg').html(recvdMsg);
	if(recvdMsg.substr(0,1) == 'W' && recvdMsg.length >= 4) {
		//let msgCat = recvdMsg.substr(1,1); // not currently used
		let msgType = recvdMsg.substr(2,1);
		let msgContent = recvdMsg.substr(3);
		if(msgType == 'E') {		// ERRORS
			if(msgContent == 'S') {
				$('#warningMsg').html('stalled');
				setState(motionState.STOP);
			}
			$('#warning').show();
		}
	}
}

function socketConnect() {
	if(ip != '' && !connected) {
		let url = 'ws://' + ip + ':' + port;
		socket = new WebSocket(url);
		//socket.binaryType = "arraybuffer";
		socket.onopen = function() {
			//
		};
		socket.onmessage = receiveMsg;
		// socket.onmessage = function(event) {
		// 	recvdMsg = event.data;
		// 	if(recvdMsg == "PONG") { pongRecvd = true; }
		// 	$('#msg').html(recvdMsg);
		// };
		socket.onclose = function() {
			reset();
			//alert('WebSocket closed!');
		};
		socket.onerror = function() {
			//
		};
		connected = true;
		$('#connectBtn').html('disconnect');
		$('#pingBtn').show();
		$('#connectBtn').removeClass('btn-warning').addClass('btn-success');
		connectTimerID = setTimeout(connectTimerTimeout, 2000);
		//clearInterval(flashConnectBtnIntervalID);
	}
}

function socketDisconnect() {
	//if(socket.readyState == 1) socket.close();
	socket.close();
	reset();
}

function sendMsg(msg) {
	// Get length of message. This does not include the msg_fmt prefix
	// ('B' or 'S'), the msg_len byte itself or the null terminator.
	// It is the length of the msg_cat + msg_typ + msg_dat, and so is
	// always at least 2.
	let len = msg.length;
	smsg = "B" + String.fromCharCode(len) + msg + String.fromCharCode(0);
	socketSend(smsg);
	$('#msgsent').html("B" + len + msg)
	//$('#msgsent').html(smsg)
}

function socketSend(msg) {
	switch (socket.readyState) {
		case 0:
			alert('Connecting - try again in a moment');
			break;
		case 1:
			socket.send(msg);
			$('#msg').html("");
			break;
		case 2:
			alert('Connection closing');
		case 3:
			reset();
			break;
	}
}

function setStartStopButton(state) {
	//$('#startStopVal').html(motionStateLabel[state]);
	$('#startStopBtn').html(motionStateLabel[1 - state]);
	$('#startStopBtn').removeClass(mStateBtnCls[state]).addClass(mStateBtnCls[1-state]);
}

function setState(state) {
	if(connected) {
		mState = state;
		setStartStopButton(state)
	} 
}

function startStop(state) {
	if(connected) {
		sendMsg("M" + motionStateCmd[state]);
		setState(state);
	} else {
		alert('Connect to robot first!');
	}
}

function setHeading(hdg) {
	if(connected) {
		sendMsg("MH" + String.fromCharCode(hdg));
		//$('#headingVal').html(headingStates[hdg]);
		$('#fwdBtn').removeClass(hStateBtnCls[1 - hdg]).addClass(hStateBtnCls[hdg]);
		$('#revBtn').removeClass(hStateBtnCls[hdg]).addClass(hStateBtnCls[1 - hdg]);
	} else {
		alert('Connect to robot first!');
	}
}

function setSpeed(idx) {
	if(connected) {
		sendMsg("MV" + String.fromCharCode(motorSpeedVals[idx]));
		//$('#speedVal').html(motorSpeedStates[idx]);
		for(let i=0; i < 4; i++) {
			let btnID = '#' + motorSpeedStates[i].toLowerCase() + 'Btn';
			if(i != idx) {
				$(btnID).removeClass('btn-success').addClass('btn-secondary');
			} else {
				$(btnID).removeClass('btn-secondary').addClass('btn-success');			
			}
		}
	}
}

function turn(dir, angle, btn) {
	if(connected) {
		msg = "MT";
		msg += String.fromCharCode(dir);
		ang16 = Math.floor(angle) & 0xFFFF;	// force to 16-bit
		msg += String.fromCharCode(ang16 >> 8);		// high byte
		msg += String.fromCharCode(ang16 & 0x00FF);	// low byte
		sendMsg(msg);
		$(btn).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
	}
}

/* ----- DOCUMENT READY ----------------------------------------------------- */
$(document).ready(function() {
	window.onerror = function(msg, url, line, col, error) {
		let extra = !col ? '' : '\ncolumn: ' + col;
		extra += !error ? '' : '\nerror: ' + error;
		alert("Error: " + msg + "\nurl: " + url + "\nline: " + line + extra);
	}
	//flashConnectBtnIntervalID = setInterval(flashConnectBtn, 3000);
	$('#ipData').hide();
	$('#startStopVal').html(motionStateLabel[mState]);
	$('#headingVal').html(headingStates[heading]);
	$('#speedVal').html(motorSpeedStates[speed]);
	$('#' + motorSpeedStates[speed].toLowerCase() + 'Btn').removeClass('btn-secondary').addClass('btn-success');
	$('#startStopBtn').html(motionStateLabel[1 - mState]);
	$('#sheldonIP').html(ip);
	$('#pingBtn').hide();
	$('#warning').hide();

	/* ----- IP ------------------------------------------------------------- */
	$('#changeIPBtn').on('click', function() {
		$('#ipData').show();
		$('#changeIPBtn').hide();
		$('#ipField').val("");
	});
	$('#submitIPBtn').on('click', function() {
		ip = $('#ipField').val();
		if(ipPattern.test(ip)) {
			$('#sheldonIP').html(ip);
			$('#ipData').hide();
			$('#changeIPBtn').show();
		} else {
			ip = '';
			alert('Not a valid IP address!');
		}
	});
	$('#cancelIPBtn').on('click', function() {
		$('#ipData').hide();
		$('#changeIPBtn').show();
	});


	/* ----- CONNECTION ----------------------------------------------------- */
	$('#connectBtn').on('click', function() {
		if(connected) {
			socketDisconnect();
		} else {
			socketConnect();
		}
	});
	$('#pingBtn').on('click', function() { ping(); });

	/* ----- MOTION CONTROL BUTTONS ----------------------------------------- */
	$('#startStopBtn').on('click', function() { startStop(1 - mState); });
	$('#fwdBtn').on('click', function() { setHeading(motorHdg.FORWARD); });
	$('#revBtn').on('click', function() { setHeading(motorHdg.REVERSE); });
	$('#rotateLeftBtn').on('click', function() { turn(motorDir.LEFT, 90, $(this)); });
	$('#rotateRightBtn').on('click', function() { turn(motorDir.RIGHT, 90, $(this)); });
	$('#veerLeftBtn').on('click', function() { turn(motorDir.LEFT, 20, $(this)); });
	$('#veerRightBtn').on('click', function() { turn(motorDir.RIGHT, 20, $(this)); });

	/* ----- SPEED BUTTONS -------------------------------------------------- */
	$('#crawlBtn').on('click', function() { setSpeed(motorSpeedIdx.CRAWL); });
	$('#slowBtn').on('click', function() { setSpeed(motorSpeedIdx.SLOW); });
	$('#normalBtn').on('click', function() { setSpeed(motorSpeedIdx.NORMAL); });
	$('#fastBtn').on('click', function() { setSpeed(motorSpeedIdx.FAST); });

	/* ----- OTHER BUTTONS -------------------------------------------------- */

	$('#clrWarningBtn').on('click', function() { 
		$('#warningMsg').html('');
		$('#warning').hide();
	});


});
