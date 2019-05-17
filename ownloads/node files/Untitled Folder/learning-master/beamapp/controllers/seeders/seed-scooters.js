exports = module.exports = function(req,res){
    var ne = require('node-each');
    var Scooter = require($app.get('baseDir')+'/models/Scooter');

    // print(req.params,'PARAMS');
    // print(req.body,'BODY');
    // print(req.query,'QUERY');
    
    var model = new Scooter();

    model.scooterCount(function(resp){
        if(resp.count > 0) {
            noAction(res,resp);
        } else {
            runSeeder(res,resp);
        }
    });

    function noAction (res,resp) {
        res.contentType('application/json');
        res.status(417).end(JSON.stringify({message:'Seeder can be run only one time.',data:resp}));
    }

    async function runSeeder (res,resp) {

        var dbval = [];
        var dbvalJson = [];
        var latlongs = generateLatLong(100000);
        latlongs.then(function(data){

            var options = {
                debug: true
            };
            ne.each(data, function(el, i){
                var dbData = Object.assign({},new Scooter(el));
                dbvalJson[i] = dbData;
                dbData = Object.values(dbData);
                //dbData.splice(0, 1);
                //dbval[i] = '('+dbData.join(',')+')';
                dbval.push(dbData);
                //console.log(dbval)
            }, options).then(function(debug) {
                dbKeys = new Scooter();
                //print(':'+Object.keys(dbKeys).join(',:'),'db_field_replace');
                db_field = Object.keys(dbKeys).join(',');
                var sql =  'INSERT INTO scooters ('+db_field+') values ?';
            
                var query = connection.query(sql, [dbval], function(err,result) {
                    if (err) {
                        console.log(query.sql)
                        console.log(err);
    
                    };
                    //connection.end();
                });
                
                res.contentType('application/json');
                res.status(200).end(JSON.stringify({message:'Seeder ran successfully.',data:dbvalJson}));
            });
        });

    }

    function getRandomInRange(from, to, fixed) {
        return (Math.random() * (to - from) + from).toFixed(fixed) * 1;
        // .toFixed() returns string, so ' * 1' is a trick to convert to number
    }
    
    
    function generateLatLong(n){
        var latLongs = [];
        return new Promise(function(resolve,reject){

            for(var i=0;i<n;i++) {
                latLongs[i] = [];
                latLongs[i]['user_id'] = makeid(2,'123456789');
                latLongs[i]['type'] = makeid(2,'123456789');
                latLongs[i]['name'] = makeid(5,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
                latLongs[i]['serialCode'] = makeid(4,'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789');
                latLongs[i]['battery'] = makeid(2,'0123456789');
                latLongs[i]['latitude'] = getRandomInRange(1.416910,1.290270,6);
                latLongs[i]['longitude'] = getRandomInRange(103.851959,103.795677,6);
            }
            resolve(latLongs)
        });
    }

    function makeid(n,c) {
        var text = "";
        var possible = c;
      
        for (var i = 0; i < n; i++)
          text += possible.charAt(Math.floor(Math.random() * possible.length));
      
        return text;
    }

}