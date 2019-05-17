
var mysql      = require('mysql');

var connection = mysql.createConnection({

    host     : process.env.DATABASE_HOST,
    user     : process.env.DATABASE_USER,
    password : process.env.DATABASE_PASS,
    database : process.env.DATABASE_NAME
});

connection.connect();

connection.config.queryFormat = function (query, values) {

  if (!values) return query;
  var i =-1;
  return query.replace(/\:(\w+)|\?/g, function (txt, key) {
    if(txt =='?'){
      i++;
      return this.escape(values[i]);
    }
    else if (values.hasOwnProperty(key)) {
      return this.escape(values[key]);
    }
    return txt;
  }.bind(this));
};

module.exports = connection;