## React/Laravel API Starter Application

This is a boilerplate application that uses React for the front end and Laravel to build a Rest API backend. Sign up/Sign in is built in already and demonstrated.

This repo uses the following immensely useful packages

- [tymondesigns/jwt-auth](https://laravel.com/docs/routing).
- [Dingo API](https://laravel.com/docs/container).

It's a good idea to check out the wiki's for each of these packages to get a good understanding of how they work.

## Installation

- Clone this repo
- from within the root directory:
    - npm install
    - composer install
    - php artisan key:generate
    - php artisan jwt:generate
- Make sure your database is configured in the .env file and then
	- php artisan migrate
- N.B the jwt-auth key is stored in the config\jwt.php file - I recommend you store it as JWT_SECRET in your .env file

## React Front end

The front end utilises some nifty (but quite standard) react stuff.

 - React Router v4
 - React-Redux
 - Prop types validation
 - Localised, modularised CSS with [react-css-modules](https://github.com/gajus/react-css-modules)
 
And demonstrates a couple of 'Reacty' ways of doing things like

 - A re-usable modal component
 - Protecting routes that require authentication


## Feedback/Contribution
I made this mostly for personal use to have a mildly configured base application, but any comments or contributions are welcome. 


### TODO

- Implement proper error checking on sign up/sign in
