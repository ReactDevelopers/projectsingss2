const { mix } = require('laravel-mix');
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.react('resources/assets/js/index.jsx', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .webpackConfig({
	    module: {
		    rules: [
				{
					test: /\.scss$/,
					include: [
						path.resolve(__dirname, "resources/assets/js/"),
					],
					loaders: [
						'style-loader',
				        'css-loader?sourceMap&modules=1&importLoaders=1&localIdentName=nlc_[hash:base64:5]',
				        'resolve-url-loader',
				        'sass-loader'
					]
				}
		    ]
		},
		resolve: {
			extensions: ['.scss'],
		},
   })
   .browserSync({
	   proxy: 'http://127.0.0.1:8000',
   		files: ['public/**/*'],
		   notify: false
	});
