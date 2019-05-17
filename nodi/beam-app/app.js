var createError = require('http-errors');
var express = require('express');
var path = require('path');
var cookieParser = require('cookie-parser');
var logger = require('morgan');
var compression = require('compression')
var bodyParser = require('body-parser');

var indexRouter = require('./routes/index');
var scootersRouter = require('./routes/scooters');

var helpers = require('./helpers');

var app = express();

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'ejs');

//ssetting base dir
app.set('baseDir', __dirname);

app.use(logger('dev'));
// app.use(express.json());
// app.use(express.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

/** 
* bodyParser.urlencoded(options)
* Parses the text as URL encoded data (which is how browsers tend to send form data from regular forms set to POST)
* and exposes the resulting object (containing the keys and values) on req.body
*/
app.use(bodyParser.urlencoded({
    limit: '50mb',
    extended: true,
    parameterLimit: 1000000
}));

/**
 * bodyParser.json(options)
 * Parses the text as JSON and exposes the resulting object on req.body.
 */
app.use(bodyParser.json({limit: '50mb'}));

/**
 * parse application/vnd.api+json as json
 */
app.use(bodyParser.json({ type: 'application/vnd.api+json' }));

app.use(helpers.cors());

app.use(function (req, res, next) {
	res.locals = process.env;
	res.locals.aditional = true; 
	next();
});

// compress all responses
app.use(compression());

app.use('/', indexRouter);
app.use('/scooters', scootersRouter);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
    next(createError(404));
});

// error handler
app.use(function(err, req, res, next) {
    // set locals, only providing error in development
    res.locals.message = err.message;
    res.locals.error = req.app.get('env') === 'development' ? err : {};

    // render the error page
    res.status(err.status || 500).send(err.message);
    //res.render('error');
});

module.exports = app;