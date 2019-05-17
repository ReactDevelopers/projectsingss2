const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const fs = require('fs');
const dotenv = require('dotenv');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
//console.log(prodWebConfig);

/**
 * Setup environment File and Veriables
 */
const envConfig = dotenv.parse(fs.readFileSync('.env.'+process.env.ENV_FILE));

for (var k in envConfig) {
	console.log(k);
	console.log(envConfig[k]);

	process.env[k] = envConfig[k];
}

const prodWebConfig = require('./webpack-production.config');
const devWebConfig = require('./webpack-dev.config');

const isBuild = process.env.BUILD;
// console.log("isBuild");
// console.log(process.env );

const config = isBuild === 'true' ? prodWebConfig : devWebConfig;

//console.log(config);
module.exports = config;
