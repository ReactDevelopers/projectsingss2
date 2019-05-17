
exports.database = function database(connection) {
    
    /** importing helpers */
    var helper = require('../helpers');
    
    /** creating database if not exists */
    connection.query(`CREATE DATABASE IF NOT EXISTS ${process.env.DATABASE_NAME}`, function (err, results, fields) {
        if (err) {
            return helper.print('error: ' + err.message);
        } else {
            if(!results.warningCount){
                helper.print(process.env.DATABASE_NAME+' created successfully.');
            }
        }
    });

    /** using the database */
    connection.query(`USE ${process.env.DATABASE_NAME}`, function (err, results, fields) {
        if (err) {
            return helper.print('error: ' + err.message);
        } else {
            helper.print(`Selected ${process.env.DATABASE_NAME}`)
        }
    });
}