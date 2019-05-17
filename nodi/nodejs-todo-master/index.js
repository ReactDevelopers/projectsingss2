//dependencies required for the app
var express = require("express");
var bodyParser = require("body-parser");
var app = express();
var validation    =     require("validator");

app.use(bodyParser.urlencoded({ extended: true }));
app.set("view engine", "ejs");
//render css files
app.use(express.static("public"));

//placeholders for added task
var task = ["buy socks", "practise with nodejs"];
//placeholders for removed task
var complete = [""];
var error = [""];

//post route for adding new task 
app.post("/addtask", function(req, res) {
    var newTask = req.body.newtask;
    // console.log(req.body.newtask.length);
    if(req.body.newtask.length==0) {
        error.pop();
        error.push('Required');
        res.redirect("/");
    }else if(!validation.isUppercase(req.body.newtask)) {
        error.pop();
        error.push('UpperCase');
        res.redirect("/");
    }else{
        //add the new task from the post route
        task.push(newTask);
        res.redirect("/");
    }
    
});

app.post("/removetask", function(req, res) {
    var completeTask = req.body.check;
    //check for the "typeof" the different completed task, then add into the complete task
    if (typeof completeTask === "string") {
        complete.push(completeTask);
        //check if the completed task already exits in the task when checked, then remove it
        task.splice(task.indexOf(completeTask), 1);
    } else if (typeof completeTask === "object") {
        for (var i = 0; i < completeTask.length; i++) {
            complete.push(completeTask[i]);
            task.splice(task.indexOf(completeTask[i]), 1);
        }
    }
    res.redirect("/");
});

//render the ejs and display added task, completed task
app.get("/", function(req, res) {
    res.render("index", { task: task, complete: complete, error: error });
});

//set app to listen on port 3000
app.listen(3000, function() {
    console.log("server is running on port 3000");
});