/**
 * Module dependencies.
 */
var NodeEnviRonment = '.env.'+process.env.NODE_ENV;

require('dotenv').config({path: NodeEnviRonment});

var connection = require('../config/mysql');

var scooters = require ('./seed-scooters');

scooters.seedData(connection);