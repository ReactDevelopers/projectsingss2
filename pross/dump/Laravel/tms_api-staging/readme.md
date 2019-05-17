# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://poser.pugx.org/laravel/lumen-framework/d/total.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/lumen-framework/v/stable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/lumen-framework/v/unstable.svg)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://poser.pugx.org/laravel/lumen-framework/license.svg)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](http://lumen.laravel.com/docs).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Project Setup Steps:
1. Clone project
2. Copy the .env.example as .env and change the database credential and SMTP/Mail Driver credentails.
3. Open terminal/cmd prompt and go to the project root location
4. execute command: composer update (To download the dependancy)
5. execute command: php artisan migrate (To create the database Schema)
6. excute command:  php artisan passport:install (To confirm the API Token)
7. excute command:  php artisan db:seed (To insert the required data)
8. Need to replace the line 768 from if(!empty($columns)){ in file  vendor/phpoffice/phpexcel/Classes/PHPExcel/Writer/Excel2007/Worksheet.php (It is due to PHP 7+ version) 
9. Add  php artisan user:cached_clear command in scheduler , It should be execute daily after 50 mins from HRDU command.