// Simple API server using HTTP & GET requests

// Packages
const path = require('path');
const http = require('http');
const url = require('url');

// Port - default to environment variable if available
const PORT = process.env.PORT || 5000;

const ERR_BAD_URL = 480;
const ERR_BAD_API_CALL = 481;
const ERR_BAD_REQUEST = 482;
const ERR_BAD_PAYLOAD = 485;

const server = http.createServer((req,res) => {var filePath = path.join(__dirname, 'static', req.url === '/' ? 'index.html' : req.url);
	let params = url.parse(req.url, true);
	let response = '';
	let respCode = 200;
	let contentType = 'text/plain';
	if(params.pathname === '/') {
		// Calls to the root should be API calls. The query should
		// contain the 'fn' variable. If not, that's an error.
		if(params.query.fn) { 
			switch(params.query.fn) {
				case 'msg':
					if(params.query.text) {
						response = params.query.fn + ': ' + params.query.text + ' - OK';
					} else {
						respCode = ERR_BAD_PAYLOAD;	// payload problem
						response = 'ERR:no_message';
					}
					break;
				default:
					respCode = ERR_BAD_REQUEST;
					response = 'ERR:unknown_request_type';
					break;
			}
		} else {
			response = 'ERR:not_an_API_call';
			respCode = ERR_BAD_API_CALL;
		}
	} else {
		respCode = ERR_BAD_URL;
		response = 'ERR:incorrect_path';
	}
	res.writeHead(respCode, { 'Content-Type': contentType } );
	res.end(response, 'utf8');
});

server.listen(PORT, () => console.log(`HTTP server running on port ${PORT}`));
