
exports.database = function database(connection) {
    
    /** creating database if not exists */
    connection.query(`CREATE DATABASE IF NOT EXISTS ${process.env.DATABASE_NAME}`, function (err, results, fields) {
        console.log(results);
        if (err) {
            console.log('errorroror');
            console.log('error: ' + err.message);
            return 'error: ' + err.message;
        } else {
            if(results.warningCount==1){
                console.log(process.env.DATABASE_NAME+' created successfully.');
            }
        }
    });
    console.log('1111111');

    /** using the database */
    connection.query(`USE ${process.env.DATABASE_NAME}`, function (err, results, fields) {
        if (err) {
            return console.log('error: ' + err.message);
        } else {
            console.log(`Selected ${process.env.DATABASE_NAME}`)
        }
    });    
}