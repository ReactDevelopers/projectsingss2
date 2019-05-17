const webpack = require('webpack');
const Dotenv = require('dotenv-webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const fs = require('fs');
const dotenv = require('dotenv');
const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
//const prodWebConfig = require('./webpack-production.config');

//console.log(prodWebConfig);

/**
 * Setup environment File and Veriables
 */
// const envConfig = dotenv.parse(fs.readFileSync('.env.'+process.env.ENV_FILE));
// for (var k in envConfig) {
//   process.env[k] = (envConfig[k] === 'true' ) ? true : ( envConfig[k] === 'false' ? false : envConfig[k] );
// }

const port = process.env.PORT || 3000;
const publicURl = process.env.DOMAIN + process.env.DOMAIN_PATH;
const isBuild = process.env.BUILD === 'true' ? true : false;
const isDev = process.env.BUILD === 'true'? false : true;
const isSourceMap = isDev;
const dist = path.resolve(__dirname, process.env.DIST);
const src = path.resolve(__dirname, './src');

module.exports = {
  mode: 'development',  
  entry: { app: [process.env.MAIN_JS]},
  output: {
    path: dist,
    filename: 'bundle.[hash].js',
    publicPath: publicURl,
  },
  optimization: {
    splitChunks: {
      cacheGroups: {
        vendor: {
          chunks: 'initial',
          test: 'vendor',
          name: 'vendor',
          enforce: true
        }
      }
    }
  },
  devtool: isDev ? 'eval-source-map' : 'source-map',
  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        //include: src,
        exclude: /node_modules/,        
        use: ['babel-loader']
      },
      {
          test: /\.s?css$/,
          //include: src,
          use: [{
              loader: "style-loader" // creates style nodes from JS strings
            }, {
                loader: "css-loader" // translates CSS into CommonJS
            }, {
                loader: "sass-loader" // compiles Sass to CSS
            }
          ]
      },
      {
          test: /\.(jpe?g|gif|png|pdf|ico)$/,
          exclude: /node_modules/,
          loader:'url-loader?limit=1024&name=images/[name].[ext]'
      },
      
      { 
          test: /\.((woff2?|svg)(\?v=[0-9]\.[0-9]\.[0-9]))|(woff2?|svg)$/, 
          loader: 'url-loader?limit=1024&name=fonts/[name].[ext]'
      },
      { 
        test: /\.((ttf|eot)(\?v=[0-9]\.[0-9]\.[0-9]))|(ttf|eot)$/, 
        loader: 'url-loader?limit=1024&name=fonts/[name].[ext]'
      }
      // {
      //   test: /\.s?css$/,
      //   use: [
      //     {
      //       loader: 'style-loader'
      //     },
      //     {
      //       loader: 'sass-loader'
      //     },
      //     {
      //       loader: 'css-loader',
      //       options: {
      //         modules: true,
      //         camelCase: true,
      //         sourceMap: isSourceMap
      //       }
      //     }
      //   ]
        
      // }
    ]
  },
  resolve: {
    extensions: ['png', '.js', '.jsx','jpeg','jpg','css','scss'],
  },
  plugins: [

    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery'
    }),

    new Dotenv({
      path: __dirname +'/.env.'+process.env.ENV_FILE, // Path to .env file (this is the default) 
      safe: true, // load .env.example (defaults to "false" which does not use dotenv-safe) 
      systemvars: true,
    }),

    new webpack.HotModuleReplacementPlugin(),
    new HtmlWebpackPlugin({
      template: 'public/index.html',
      favicon: './favicon.ico'
    }), 
  ],
  devServer: {
    host: 'localhost',
    port: port,
    historyApiFallback: true,
    open: true,
    hot: true,
  }
};