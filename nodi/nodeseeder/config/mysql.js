var mysql      = require('mysql');
var database   = require('./database');
var connection = mysql.createConnection({

    port     : process.env.DATABASE_PORT,
    host     : process.env.DATABASE_HOST,
    user     : process.env.DATABASE_USER,
    password : process.env.DATABASE_PASS,
    timezone : process.env.TZ,
    dateStrings : 'datetime',
});

connection.connect(function(err) {
    if (err) {
        console.log('error from mysql: ' + err.message);
        return 'error from mysql: ' + err.message;
    }
    console.log('Connected to the MySQL server.');
    
});

/**
 * creating and selecting database if not exists
 */
database.database(connection);

module.exports = connection;