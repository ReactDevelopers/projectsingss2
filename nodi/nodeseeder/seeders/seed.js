exports.seedDt = function seedDt(connection) {
    var ne = require('node-each');
    var table = require('../tables/table');
    var modelling = require('../models/modelling');

    table.tablecreator(connection,function(output){
        if(output.status) {
            var model = new modelling();

            // /** checking if data already present in table */
            model.tableCount(function(resp){
                if(resp.count > 0) {
                    /** no action if data present in table */
                    console.log('Seeder can run only once.','Exiting');
                    process.exit(0);
                } else {
                    /** if no data in table the seed data */
                    console.log('insert data after this');
                    runSeeder();
                }
            });
        }
    });


    /** Async function to perform data entry  */
    async function runSeeder () {

        console.log('Seeding 10000 scooters.','Seed Started');
        var dbval = [];
        var latlongs = generateLatLong(10000);
        
        latlongs.then(function(data){

            var options = {
                debug: true
            };
            ne.each(data, function(el, i){
                var dbData = Object.assign({},new modelling(el));
                dbData = Object.values(dbData);
                dbval.push(dbData);
            }, options).then(function(debug) {
                
                dbKeys = new modelling();

                /** getting table fields from model */
                db_field = Object.keys(dbKeys).join(',');

                /** query to batch insert data */
                var sql =  'INSERT INTO users ('+db_field+') values ?';

                var query = connection.query(sql, [dbval], function(err,result) {
                    if (err) {
                        console.log(query.sql,'QUERY')
                        console.log(err,'ERROR');
                    };
                    console.log('Seeder ran successfully.','Seeding Complete');
                    process.exit(0);
                });
                
            });
        });

    }

    /** Faking data to be seeded in scooters table */
    function generateLatLong(n){
        var latLongs = [];
        return new Promise(function(resolve,reject){
            for(var i=0;i<n;i++) {
                // var latlng = Object.assign({},helper.randomLatLng(process.env.SINGAPORE_LAT,process.env.SINGAPORE_LNG,100));
                latLongs[i] = [];
                latLongs[i]['user_id'] = makeid(2,'123456789');
                latLongs[i]['name'] = makeid(5,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
            }
            resolve(latLongs)
        });
    }

    /** making fake ids */
    function makeid(n,c) {
        var text = "";
        var possible = c;
      
        for (var i = 0; i < n; i++)
          text += possible.charAt(Math.floor(Math.random() * possible.length));
      
        return text;
    }
    
}