var moment = require('moment-timezone');
var connection = require('../config/mysql');
var modelling = function(data) {
    data = Object.assign({},data);
    if(data.id !== undefined){
        this.id = data.id;
    }
    
    this.user_id = data.user_id !== undefined ? data.user_id : '';
    this.name = data.name !== undefined ? data.name : '';
    this.created_at = data.created_at !== undefined ? data.created_at : this.nowTime();
    this.updated_at = data.updated_at !== undefined ? data.updated_at : this.nowTime();
};


modelling.prototype.changeToServerTimeZone = function(date) {
	
	moment.tz.setDefault(process.env.SERVER_TIMEZONE);

	if(date instanceof Date) { 
		var a = moment(date);
	} else {
		var a = moment(new Date());
	}

	return a.format('YYYY-MM-DD HH:mm:ss');
};


modelling.prototype.isInt =  function (value) {
  return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value));
}

modelling.prototype.tableCount = function(callback) {
	connection.query('SELECT COUNT(*) as count FROM users', function (error, list, fields) {
		if(!error && typeof callback != "undefined") {
            callback(list[0]);
        }
    });
}

modelling.prototype.nowTime = function(){

	moment.tz.setDefault(process.env.SERVER_TIMEZONE);
	return moment(new Date()).format('YYYY-MM-DD HH:mm:ss');
}


module.exports = modelling;