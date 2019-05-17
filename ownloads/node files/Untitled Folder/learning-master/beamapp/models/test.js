var events = require('events');
var moment = require('moment-timezone');
var ne = require('node-each');
var events = require('events').EventEmitter;
var _ = require('lodash');

var TEST = function() {
    this.server_time_zone = 'UTC';
};

TEST.prototype = new events.EventEmitter;

TEST.prototype.changeToServerTimeZone = function(date)
{

	/*if(date instanceof Date){

		var a = moment(date);
		return a.format('YYYY-MM-DD HH:mm:ss');
	}
	else{
		var a = moment.tz(date,'UTC');
		return a.tz(this.server_time_zone).format('YYYY-MM-DD HH:mm:ss');
	}*/

	/*moment.tz.setDefault('UTC');
	aa = moment(new Date());
	console.log(aa.format('YYYY-MM-DD HH:mm:ss'))
	bb = aa.tz('Asia/Kolkata').format('YYYY-MM-DD HH:mm:ss');
	console.log(bb)*/
	
	moment.tz.setDefault(this.server_time_zone);

	if(date instanceof Date) { 
		var a = moment(date);
	} else {
		var a = moment(new Date());
	}
	//this.print('SERVER TIME',a.format('YYYY-MM-DD HH:mm:ss'));

	return a.format('YYYY-MM-DD HH:mm:ss');
};


TEST.prototype.isInt =  function (value) {
  return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value));
}


TEST.prototype.getTestList = function(data,callbackFnc){
	
	var self = this;
	// this.connection.query('SHOW COLUMNS FROM test', function(err, rows, fields){ 
	// 	console.log('sdfg');
	// 	console.log(rows[0].Field);
	// });

	//this.connection.query('SELECT * FROM test where id= :id', {id:data.id}, function (error, list, fields) {
	connection.query('SELECT * FROM test', function (error, list, fields) {
		if(!error && typeof callbackFnc != "undefined"){
            self.emit('success','successfully_retrieved_data');
            callbackFnc(list);
        }
    });
}

TEST.prototype.nowTime = function(){

	moment.tz.setDefault(this.server_time_zone);
	return moment(new Date()).format('YYYY-MM-DD HH:mm:ss');
}


module.exports = TEST;