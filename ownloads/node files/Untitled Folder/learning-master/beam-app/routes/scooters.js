var express = require('express');
var router = express.Router();

/* GET scooters listing. */
router.post('/', function(req, res, next) {
	require('../controllers/home/scooters')(req,res);
});

module.exports = router;
