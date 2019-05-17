exports = module.exports = function(app){
    app.get('/test', function(req, res) {
        /**
         * Take the response from the module
         * if return is used in module
         * var test = require('../modules/test')(app, connection, print);
         * print(test);
         * res.contentType('application/json');
         * res.end(JSON.stringify(resp));
         * 
         * OR can directly send the response from module
         */

        require($app.get('baseDir')+'/controllers/test')(res);
        
    });
}