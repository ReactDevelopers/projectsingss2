// index.js
    const fs = require("fs");
    let shell = require("shelljs");
    const express = require("express");
    const cron = require("node-cron");

    app = express();

    // To backup a database
    cron.schedule("* * * * *", function() {
      console.log("---------------------");
      console.log("Running Cron Job");
      if (shell.exec("mysql -u root -p root my_db > users.sql").code !== 0) {
        shell.exit(1);
      }
      else{
        shell.echo("Database backup complete");
      }
    });
    app.listen("3128");