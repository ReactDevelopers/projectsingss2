var fs                      = require('fs');
var express                 = require('express');
var http                    = require('http');
var bodyParser              = require('body-parser');
var $app                    = express();

/** 
* bodyParser.urlencoded(options)
* Parses the text as URL encoded data (which is how browsers tend to send form data from regular forms set to POST)
* and exposes the resulting object (containing the keys and values) on req.body
*/
$app.use(bodyParser.urlencoded({
    extended: true
}));

/**
 * bodyParser.json(options)
 * Parses the text as JSON and exposes the resulting object on req.body.
 */
$app.use(bodyParser.json());

/**
 * parse application/vnd.api+json as json
 */
$app.use(bodyParser.json({ type: 'application/vnd.api+json' }));

var allowCrossDomain = function(req, res, next) {
    res.header('Access-Control-Allow-Origin', req.get('Origin') || '*');
    res.header('Access-Control-Allow-Credentials', 'true');
    res.header('Access-Control-Allow-Methods', 'GET,HEAD,PUT,PATCH,POST,DELETE');
    res.header('Access-Control-Expose-Headers', 'Content-Length');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Content-Length, X-Requested-With');
    if (req.method === 'OPTIONS') {
        return res.send(200);
    } else {
        return next();
    }

};

$app.use(allowCrossDomain);


var $server                 = http.createServer($app);

var $port                   = process.env.NODE_PORT || 8484;


$server.listen($port, function () {console.log('\n\nListening on port %d \n\nEnvironment Used is %s \n\nFull URL Used is %s:%d',$port,process.env.NODE_ENV,process.env.NODE_IP,$port);});


module.exports    = {express:express,app:$app,server:$server};