exports = module.exports = function(req,res){
    var Scooter = require($app.get('baseDir')+'/models/Scooter');
    
    var model = new Scooter();
    
    model.getAllScooterList(req.query,function(resp){
        res.contentType('application/json');
        res.status(200).end(JSON.stringify({message:'Listing scooters.',data:resp}));
    });

}