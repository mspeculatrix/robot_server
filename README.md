# robot_server

Blog post: https://mansfield-devine.com/speculatrix/2019/01/sheldon-robot-the-web-app-on-docker/

This is a Dockerised server meant to provide control and monitoring of my robot project.

The idea is that these containers can be spun up on any computer residing on the same local network as the robot. For example, I've been developing them on my desktop machine, but I can as easily run the containers on my laptop or even the LattePanda Alpha that is ultimately intended as the robot controller.

It consists of the following servers:

## http
This is an Apache/PHP/JavaScript server. It has pages that use JavaScript to communicate with the robot via HTTP GET requests and websockets. It also has a PHP-based Ajax server script to deal with communication from the robot.

## api
This is a Node.js server that deals with HTTP GET requests. I wrote this simply to learn a bit of Node and it's not currently serving any useful function. This will probably replace the PHP-based one on the server above. However, I may ultimately replace the Apache/PHP server with the Node.js one, perhaps using Express to serve pages. We'll see.
