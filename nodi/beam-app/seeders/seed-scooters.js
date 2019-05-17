exports.seedData = function seedData(connection) {
    var ne = require('node-each');
    var helper = require('../helpers');
    var table = require('../tables/scooters');
    var Scooter = require('../models/Scooter');

    table.scooters(connection,function(output){
        if(output.status) {
            var model = new Scooter();

            /** checking if data already present in table */
            model.scooterCount(function(resp){
                if(resp.count > 0) {
                    /** no action if data present in table */
                    noAction();
                } else {
                    /** if no data in table the seed data */
                    runSeeder();
                }
            });
        }
    });


    function noAction () {
        helper.print('Seeder can run only once.','Exiting');
        process.exit(0);
    }

    /** Async function to perform data entry in scooters table after fake data is returned */
    async function runSeeder () {

        helper.print('Seeding 10000 scooters.','Seed Started');
        var dbval = [];
        var latlongs = generateLatLong(10000);
        
        latlongs.then(function(data){

            var options = {
                debug: true
            };
            ne.each(data, function(el, i){
                var dbData = Object.assign({},new Scooter(el));
                dbData = Object.values(dbData);
                dbval.push(dbData);
            }, options).then(function(debug) {
                
                dbKeys = new Scooter();

                /** getting table fields from model */
                db_field = Object.keys(dbKeys).join(',');

                /** query to batch insert data */
                var sql =  'INSERT INTO scooters ('+db_field+') values ?';

                var query = connection.query(sql, [dbval], function(err,result) {
                    if (err) {
                        helper.print(query.sql,'QUERY')
                        helper.print(err,'ERROR');
                    };
                    helper.print('Seeder ran successfully.','Seeding Complete');
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
                var latlng = Object.assign({},helper.randomLatLng(process.env.SINGAPORE_LAT,process.env.SINGAPORE_LNG,100));
                latLongs[i] = [];
                latLongs[i]['user_id'] = makeid(2,'123456789');
                latLongs[i]['type'] = makeid(2,'123456789');
                latLongs[i]['name'] = makeid(5,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
                latLongs[i]['serialCode'] = makeid(4,'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
                latLongs[i]['battery'] = makeid(2,'0123456789');
                latLongs[i]['latitude'] = getRandomInRange(1,3,6);
                latLongs[i]['longitude'] = getRandomInRange(100,130,6);
            }
            resolve(latLongs)
        });
    }

    /** for generating random numbers in a range */
    function getRandomInRange(from, to, fixed) {
        // .toFixed() returns string, so ' * 1' is a trick to convert to number
        return (Math.random() * (to - from) + from).toFixed(fixed) * 1;
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