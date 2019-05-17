exports = module.exports = function(app){
    require('./test')(app);
    require('./home')(app);
    require('./seeder')(app);
    require('./scooters')(app);


    /**
     * Managing not found
     */
    $app.get('*', function(req, res){
        res.contentType('application/json');
        res.status(404).end(JSON.stringify({
            data:{},
            message:'Endpoint not found.'
        }));
    });
}