
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Bootstrap
 |--------------------------------------------------------------------------
 |
 | First, we will load all of the "core" dependencies for Spark which are
 | libraries such as Vue and jQuery. This also loads the Spark helpers
 | for things such as HTTP calls, forms, and form validation errors.
 |
 | Next, we'll create the root Vue application for Spark. This will start
 | the entire application and attach it to the DOM. Of course, you may
 | customize this script as you desire and load your own components.
 |
 */

require('spark-bootstrap');

require('./components/bootstrap');

if (Spark.env == 'production') {

    const Rollbar = require('vue-rollbar');

    Vue.use(Rollbar, {
        accessToken: '288d9d97fc1448fb9c2167906c13744f',
        captureUncaught: true,
        captureUnhandledRejections: true,
        enabled: true,
        source_map_enabled: true,
        environment: Spark.env,
        payload: {
           client: {
                javascript: {
                   code_version: '1.0'
                }
           }
        },
        // transform: function (obj) {
        //    obj.sessionURL = LogRocket.sessionURL;
        //    return obj;
        // },
    });
}

var app = new Vue({
    mixins: [require('spark')]
});

require('./sw-register');
