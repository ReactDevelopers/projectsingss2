var NodeEnviRonment = '.env.'+process.env.NODE_ENV;

require('dotenv').config({path: NodeEnviRonment});

process.env.NODE_TLS_REJECT_UNAUTHORIZED = 0;
process.env.TZ = process.env.SERVER_TIMEZONE;

var fs                      = require('fs');
var $socket                 = require('socket.io');
var request                 = require('request');

var $served = require('./config/express');

if (!global.$app){
    global.$app = $served.app;
}

if (!global.$server){
    global.$server = $served.server;
}

if (!global.connection){
    global.connection = require(__dirname+'/config/mysql');
}


// var $app = $served.app;
// var $server = $served.server;

$app.use($served.express.static(__dirname + '/public')); 

/**
 * set ejs as a view engine 
 */
$app.set('view engine', 'ejs')

/**
 * setting base directory 
 */
$app.set('baseDir', __dirname);

var $debug                  = (process.env.NODE_DEBUG == 'true') ? true : false;

var $io                     = $socket.listen($server);

Object.prototype.isEmpty = function() {
    for(var key in this) {
        if(this.hasOwnProperty(key)){
            return false;
        }
    }
    return true;
}
if (!global.print){
    global.print = function print($data,$heading){
        if(!$heading){
            $heading = "RECENT DATA";
        }
        if(!$data){
            $data = "NO DATA TO PRINT";
        }

        if($debug){
            console.log('\n\n\n-----------------------'+$heading+'-----------------------'+'\n\n\n'+ JSON.stringify($data,null,2)+'\n\n-----------------------'+'DEVELOPED BY: Manish Mahant-----------------------\n\n\n\n');
        }
    }
}


/**
 * All routes included
 */
require('./routes')($app);

$app.use(function(req,res){
    var data = {
        map_key:process.env.MAP_KEY
    }
    res.render($app.get('baseDir')+'/views/home.ejs',data);
});

exports = module.exports    = $app; 