exports.tablecreator = function tablecreator(connection,callback) {
    
    connection.query(`CREATE TABLE IF NOT EXISTS ${process.env.DATABASE_NAME}.users (
            id int(10) UNSIGNED NOT NULL,
            user_id int(10) UNSIGNED DEFAULT NULL,
            name varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT NULL,
            updated_at datetime DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;`,function(err,result) {
        if (err) {
            console.log('error ffff'+result);
            callback({status:false,error:err.message});
            //return helper.print('error: ' + err.message);
        } else {
            console.log('done'+result.warningCount);
            if(result.warningCount) {
                connection.query(`ALTER TABLE ${process.env.DATABASE_NAME}.users ADD PRIMARY KEY (id);`,function(err,result) {
                    if (err) {
                        callback({status:false,error:err.message});
                    }
                });
                connection.query(`ALTER TABLE ${process.env.DATABASE_NAME}.users MODIFY id int(10) UNSIGNED NOT NULL AUTO_INCREMENT;`,function(err,result) {
                    if (err) {
                        callback({status:false,error:err.message});
                    } else {
                        console.log(`${process.env.DATABASE_NAME}.users created successfully.`);
                        callback({status:true});
                    }
                });
                console.log('PPPPPPPP');
            } else {
                console.log('LLLLL');
                callback({status:true});
            }
        }
    });
}