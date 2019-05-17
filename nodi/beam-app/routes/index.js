var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
	var data = {
		query: req.query,
        map_key:process.env.MAP_KEY,
    	title: 'Beam App'
    }
  	res.render('home', data);
});


module.exports = router;
