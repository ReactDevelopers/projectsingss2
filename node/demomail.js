var http = require('http');
var dt = require('./myfirstmail');

http.createServer(function (req, res) {
    res.writeHead(200, {'Content-Type': 'text/html'});
    res.write("MAil Sent");
    res.end();
}).listen(8000);