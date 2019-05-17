var helper = require('../helpers');
var moment = require('moment-timezone');
var connection = require('../config/mysql');
var Scooter = function(data) {
    data = Object.assign({},data);
    if(data.id !== undefined){
        this.id = data.id;
    }
    
    this.user_id = data.user_id !== undefined ? data.user_id : '';
    this.type = data.type !== undefined ? data.type : '';
    this.name = data.name !== undefined ? data.name : '';
    this.serialCode = data.serialCode !== undefined ? data.serialCode : '';
    this.battery = data.battery !== undefined ? data.battery : '';
    this.latitude = data.latitude !== undefined ? data.latitude : 0.000000;
    this.longitude = data.longitude !== undefined ? data.longitude : 0.000000;
    this.created_at = data.created_at !== undefined ? data.created_at : this.nowTime();
    this.updated_at = data.updated_at !== undefined ? data.updated_at : this.nowTime();
};


Scooter.prototype.changeToServerTimeZone = function(date) {
	
	moment.tz.setDefault(process.env.SERVER_TIMEZONE);

	if(date instanceof Date) { 
		var a = moment(date);
	} else {
		var a = moment(new Date());
	}

	return a.format('YYYY-MM-DD HH:mm:ss');
};


Scooter.prototype.isInt =  function (value) {
  return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value));
}

Scooter.prototype.scooterCount = function(callback) {
	connection.query('SELECT COUNT(*) as count FROM scooters', function (error, list, fields) {
		if(!error && typeof callback != "undefined") {
            callback(list[0]);
        }
    });
}

Scooter.prototype.getAllScooterList = function(req,callback) {
    var condi = [];
    var data = req.query;
    data.notin = req.body.notin ? req.body.notin : [];

    data.range = parseInt(data.range);
    data.count = parseInt(data.count);

    data.range = (data.range !== undefined && data.range && !isNaN(data.range)) ? data.range : parseInt(process.env.MAP_RADIUS);
    data.limit = (data.count !== undefined && data.count && !isNaN(data.count)) ? data.count : parseInt(process.env.DEFAULT_LIMIT);
    
    var sql = 'SELECT * FROM scooters ';

    if((data.latitude !== undefined && data.latitude) && (data.longitude !== undefined && data.longitude)) {
        //var index = (condi.length > 0) ? Math.abs(parseInt(condi.length) - parseInt(1)) : 0;
        condi.push('( 3959 * acos( cos( radians(:latitude) ) * cos( radians( latitude ) ) * cos( radians( `longitude` ) - radians(:longitude) ) + sin( radians(:latitude) ) * sin( radians( latitude ) ) ) )*1.60934 <= :range'); 
    }

    if(data.notin.length > 0) {
        condi.push('scooters.id not in ('+data.notin.join(',')+')');
    }

    if(condi.length > 0) {
        sql += ' where '+condi.join(' and ');
    }

    sql += ' LIMIT 0,:limit';

    var query = connection.query(sql, data, function (error, list, fields) {
        //helper.print(query.sql)
        if(!error && typeof callback != "undefined"){
            callback(list);
        } else if(typeof callback != "undefined" && error){
            callback(error)
        }
    });
}

Scooter.prototype.nowTime = function(){

	moment.tz.setDefault(process.env.SERVER_TIMEZONE);
	return moment(new Date()).format('YYYY-MM-DD HH:mm:ss');
}


module.exports = Scooter;