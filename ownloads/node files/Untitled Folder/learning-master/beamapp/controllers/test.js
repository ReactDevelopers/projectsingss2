exports = module.exports = function(res){

    // function user_verification_function($io,userdata)
    // {
    //     print(userdata,'USER NOT VERIFIED');
    //     $io.emit('on.user.verification.check.'+userdata.user, userdata);
    // }

    var TEST = require($app.get('baseDir')+'/models/test');
    
    var testmodel = new TEST();

    // testmodel.on('success',function(message){
    //     print(message);
    // });
    

    testmodel.getTestList({id:1},function(resp){
        /**
         * Can return data to the route
         * return resp;
         * 
         * OR can directly send the response
         */
        
        res.contentType('application/json');
        res.end(JSON.stringify(resp));
    });

}