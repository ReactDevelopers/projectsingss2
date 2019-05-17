exports = module.exports = function(app){
    
    app.get('/seed-scooters', function(req, res) {
        if(seedAuth(req, res)){
            require(app.get('baseDir')+'/controllers/seeders/seed-scooters')(req,res);   
        }       
    });

    function seedAuth(req, res) {
        var canAccess = (req.query.name == process.env.ADMIN_PASS && req.query.pass == process.env.ADMIN_NAME);
        if(!canAccess) {
            res.contentType('application/json');
            res.status(403).end(JSON.stringify({message:'You cannot access this url.',data:{}}));
        }
        return canAccess;
    }
}