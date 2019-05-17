exports.scooters = function scooters(connection,callback) {

    var helper = require('../helpers');
    
    connection.query(`CREATE TABLE IF NOT EXISTS ${process.env.DATABASE_NAME}.scooters (
            id int(10) UNSIGNED NOT NULL,
            user_id int(10) UNSIGNED DEFAULT NULL,
            type int(10) UNSIGNED DEFAULT NULL,
            name varchar(255) DEFAULT NULL,
            serialCode varchar(255) DEFAULT NULL,
            battery int(10) UNSIGNED DEFAULT '0',
            latitude double(10,6) DEFAULT NULL,
            longitude double(10,6) DEFAULT NULL,
            created_at datetime DEFAULT NULL,
            updated_at datetime DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;`,function(err,result) {
        if (err) {
            callback({status:false,error:err.message});
            //return helper.print('error: ' + err.message);
        } else {
            if(!result.warningCount) {
                connection.query(`ALTER TABLE ${process.env.DATABASE_NAME}.scooters ADD PRIMARY KEY (id);`,function(err,result) {
                    if (err) {
                        callback({status:false,error:err.message});
                    }
                });
                connection.query(`ALTER TABLE ${process.env.DATABASE_NAME}.scooters MODIFY id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;`,function(err,result) {
                    if (err) {
                        callback({status:false,error:err.message});
                    } else {
                        helper.print(`${process.env.DATABASE_NAME}.scooters created successfully.`);
                        callback({status:true});
                    }
                });
            } else {
                callback({status:true});
            }
        }
    });
}