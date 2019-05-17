const path = require('path');
const webpack = require('webpack');
const DashboardPlugin = require('webpack-dashboard/plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const dotenv = require('dotenv');
const ExtractTextWebpackPlugin = require('extract-text-webpack-plugin');

//const extractCSS = new ExtractTextWebpackPlugin('[name].css');

const PATHS = {
  root: path.resolve(__dirname, '..'),
  nodeModules: path.resolve(__dirname, '../node_modules'),
  src: path.resolve(__dirname, '../src'),
  dist: path.resolve(__dirname, '../../production'),
};

const DEV_SERVER = {
  hot: true,
  hotOnly: true,
  historyApiFallback: true,
  overlay: true,
  // stats: 'verbose',
  // proxy: {
  //   '/api': 'http://localhost:3000'
  // },
};

module.exports = (env = {}) => {
  
  const globalDotEnv = dotenv.config({
    path:  path.resolve(__dirname, "../.env."+ env.filename),
    silent: true
  });
  

  const isBuild = !!globalDotEnv.parsed.build;
  const isDev = !globalDotEnv.parsed.build;
  const isSourceMap = !!globalDotEnv.parsed.sourceMap || isDev;

  const DOMAIN = globalDotEnv.parsed.DOMAIN;
  const DOMAIN_PATH = globalDotEnv.parsed.DOMAIN_PATH;
  const API_URL = globalDotEnv.parsed.API_URL;
  const publicPath = DOMAIN+DOMAIN_PATH;

  return {
    cache: true,
    devtool: isDev ? 'eval-source-map' : 'source-map',
    devServer: DEV_SERVER,

    context: PATHS.root,

    entry: {
      app: [
        'babel-polyfill',
        'isomorphic-fetch',
        //'ckeditor',
        './src/scss/assets/images/loader.gif',
        'react-hot-loader/patch',
        './src/index.tsx',
      ],
    },
    output: {
      path: PATHS.dist,
      filename: isDev ? '[name].js' : '[name].[hash].js',
      publicPath: publicPath,
      // chunkFilename: '[id].chunk.js',
    },

    resolve: {
      alias: {
        Components: path.resolve(__dirname, '../src/components'),
        //Features: path.resolve(__dirname, '../src/features')
      },
      extensions: ['.ts', '.tsx', '.js', '.jsx', '.json'],
      modules: ['src', 'node_modules'],
    },

    module: {
      rules: [
        // typescript
        {
          test: /\.tsx?$/,
          include: PATHS.src,
          use: (env.awesome ?
            [
              { loader: 'react-hot-loader/webpack' },
              {
                loader: 'awesome-typescript-loader',
                options: {
                  transpileOnly: true,
                  useTranspileModule: false,
                  sourceMap: isSourceMap,
                },
              },
            ] : [
              { loader: 'react-hot-loader/webpack' },
              {
                loader: 'ts-loader',
                options: {
                  transpileOnly: true,
                  compilerOptions: {
                    'sourceMap': isSourceMap,
                    'target': isDev ? 'es2015' : 'es5',
                    'isolatedModules': true,
                    'noEmitOnError': false,
                  },
                },
              },
            ]
          ),
        },
        // json
        {
          test: /\.json$/,
          include: [PATHS.src],
          use: { loader: 'json-loader' },
        },
        // {
        //   test: /\.s?css$/,
        //   use: ExtractTextWebpackPlugin.extract({
        //     fallback: 'style-loader',
        //     use: [
        //       {
        //           loader: 'css-loader',
        //           options: {
        //               // If you are having trouble with urls not resolving add this setting.
        //               // See https://github.com/webpack-contrib/css-loader#url
        //               url: false,
        //               minimize: true,
        //               sourceMap: true
        //           }
        //       }, 
        //       {
        //           loader: 'sass-loader',
        //           options: {
        //               sourceMap: true
        //           }
        //       }
        //     ]
        //     // options: {
        //     //   plugins: () => [require('autoprefixer')]
        //     // }
        //   })
        // },
        // // css
        {
          test: /\.s?css$/,
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
          test: /\.(jpe?g|gif|png|ico)$/,
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
      ],
    },

    plugins: [
     // require('autoprefixer'),
    //  new ExtractTextWebpackPlugin({
    //   filename: 'styles.css',
    //   allChunks: true
    // }),
      new DashboardPlugin(),
      new webpack.DefinePlugin({
        'process.env': {
          NODE_ENV: JSON.stringify(isDev ? 'development' : 'production'),
          DOMAIN: JSON.stringify(DOMAIN),
          DOMAIN_PATH: JSON.stringify(DOMAIN_PATH),
          API_URL: JSON.stringify(API_URL),
        },
      }),
      new webpack.optimize.CommonsChunkPlugin({
        name: 'vendor',
        minChunks: (module) => module.context && module.context.indexOf('node_modules') !== -1,
      }),
      new webpack.optimize.CommonsChunkPlugin({
        name: 'manifest',
      }),
      new HtmlWebpackPlugin({
        template: './index.html',
        loaderUrl: publicPath+'/images/loader.gif',
        favicon: 'src/scss/assets/images/favicon.ico',
        // chunks:{
        //   "head": {
        //     "css": [ "styles.css" ]
        //   }
        // }

      }),
      ...(isDev ? [
        new webpack.HotModuleReplacementPlugin({
          // multiStep: true, // better performance with many files
        }),
        new webpack.NamedModulesPlugin(),
      ] : []),
      ...(isBuild ? [
        new webpack.LoaderOptionsPlugin({
          minimize: true,
          debug: false
        }),
        new webpack.optimize.UglifyJsPlugin({
          beautify: false,
          compress: {
            screw_ie8: true
          },
          comments: false,
          sourceMap: isSourceMap,
        }),
      ] : []),

      new webpack.ProvidePlugin({
        $: "jquery",
        jQuery: "jquery"
      })
    ]
  };

};
