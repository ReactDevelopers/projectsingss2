exports.seedDt = function seedDt(connection) {
    var ne = require('node-each');
    var table = require('../tables/table');
    var modelling = require('../models/modelling');

    table.tablecreator(connection,function(output){
        console.log('exxxxxx'+output.status);
        if(output.status) {
            var model = new modelling();

            // /** checking if data already present in table */
            model.tableCount(function(resp){
                console.log('yup'+resp);
                if(resp.count > 0) {
                    /** no action if data present in table */
                    console.log('Seeder can run only once.','Exiting');
                    process.exit(0);
                } else {
                    /** if no data in table the seed data */
                    console.log('insert data after this');
                }
            });
        }
    });
    
}