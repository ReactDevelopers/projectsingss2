var helper     = require('../helpers');
var mysql      = require('mysql');
var database   = require('./database');

var connection = mysql.createConnection({

    port     : process.env.DATABASE_PORT,
    host     : process.env.DATABASE_HOST,
    user     : process.env.DATABASE_USER,
    password : process.env.DATABASE_PASS,
    //database : process.env.DATABASE_NAME,
    timezone : process.env.TZ,
    dateStrings : 'datetime',
});

connection.connect(function(err) {
    if (err) {
        return helper.print('error: ' + err.message);
    }

    helper.print('Connected to the MySQL server.');
});

connection.config.queryFormat = function (query, values) {

    if (!values) return query;
    var i =-1;
    return query.replace(/\:(\w+)|\?/g, function (txt, key) {
        if(txt =='?'){
            i++;
            return this.escape(values[i]);
        } else if (values.hasOwnProperty(key)) {
            return this.escape(values[key]);
        }
        return txt;
    }.bind(this));
};

/**
 * creating and selecting database if not exists
 */
database.database(connection);

module.exports = connection;