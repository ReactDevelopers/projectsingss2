exports = module.exports = function(app){
    
    app.get('/scooters', function(req, res) {
        require(app.get('baseDir')+'/controllers/home/scooters')(req,res);      
    }).post(function(req, res) {
        require(app.get('baseDir')+'/controllers/home/scooters')(req,res);     
    });
}