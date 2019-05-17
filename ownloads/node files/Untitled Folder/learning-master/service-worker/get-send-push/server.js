const webPush = require('web-push');

process.env.VAPID_PUBLIC_KEY = 'BAPTuqCufTJOh3XnaEqhCQM-3Hj8u55k5ThMXtZRZcrsMXRdU0mMfvnlMpIjTsKAyuHuom-Fg6bh8a1NkguKDUo';
process.env.VAPID_PRIVATE_KEY = 'q9hXVCQiNVCerPQYXAQJk0zVxoKR7-UL_uxEC3hT1Vc';

if (!process.env.VAPID_PUBLIC_KEY || !process.env.VAPID_PRIVATE_KEY) {
    console.log("You must set the VAPID_PUBLIC_KEY and VAPID_PRIVATE_KEY "+
    "environment variables. You can use the following ones:");
    console.log(webPush.generateVAPIDKeys());
    return;
}

webPush.setVapidDetails(
    'http://localhost/manish-rnd/service-worker/',
    process.env.VAPID_PUBLIC_KEY,
    process.env.VAPID_PRIVATE_KEY
);

const payloads = {};



module.exports = function(app, route) {
    app.get(route, function(req, res) {
        res.sendFile(__dirname+'/index.html');
    });

    app.get(route + 'vapidPublicKey', function(req, res) {
        res.send(process.env.VAPID_PUBLIC_KEY);
    });

    app.post(route + 'register', function(req, res) {
        res.sendStatus(201);
    });

    app.post(route + 'sendNotification', function(req, res) {
        const subscription = req.body.subscription;
        const payload = req.body.payload;
        const options = {
            TTL: req.body.ttl
        };

        setTimeout(function() {
            payloads[req.body.subscription.endpoint] = payload;
            webPush.sendNotification(subscription, null, options)
            .then(function() {
                res.sendStatus(201);
            })
            .catch(function(error) {
                res.sendStatus(500);
                console.log(error);
            });
        }, req.body.delay * 1000);
    });

    app.get(route + 'getPayload', function(req, res) {
        res.send(payloads[req.query.endpoint]);
    });
};
