exports = module.exports = function(app){
    app.get('/',function(req,res){    
        var data = {
            map_key:process.env.MAP_KEY
        }
        //res.sendFile(__dirname+'/views/index.html',data);
        res.render($app.get('baseDir')+'/views/home.ejs',data);
    });
}