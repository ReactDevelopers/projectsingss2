module.exports = function(req,res){
    
    /** Including Scooter model*/
    var Scooter = require('../../models/Scooter');
    
    var model = new Scooter();
    
    /** Fetching data for the home page to set markers */
    model.getAllScooterList(req,function(resp){
        res.contentType('application/json');
        res.status(200).end(JSON.stringify({message:'Listing scooters.',data:resp}));
    });

}