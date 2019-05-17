node v8.11.3

/*
* NODE_ENV=manish PORT=3212 pm2 start fpchat.js --name="FpChat"
*/

Part 1: (coding) Fetching X Scooters within a radius (Y).

Your goal is to code a system that can fetch the closest x scooters for a given lat,lon within y meters and plot it on a map. This exercise will include front-end, backend and data storage, and you can choose any technology you’d like for each.

Steps:
a.) Create the data storage for the scooters and populate data for scooters placed randomly throughout Singapore.
b.) Write a backend service that can provide data to the front end for plotting the closest x scooters within y meters of a given lat,lon
c.) Write front end that allows you to set:
x scooters
y search radius
lat, and long
It should fetch the data based on these params from the backend service, and it should plot on a map.
Documentation: Be sure to include sample values for x, y, lat, long for testing and any notes needed to test your exercise.
Submission: You can submit to a private repo in Bitbucket or send us a zip file. Please include any instructions needed to quickly test.
Tip: We care a lot about code quality and understandability.

Part 2: (technical design) Let’s say that we have plot all the scooters in Singapore on a map (hundreds of thousands) for our operations team to understand where all the scooters currently are. Please describe how you would do that, focusing on usability, performance and scalability. You can focus more on the front end, the backend or discuss both equally in your answer. (~1 page should be fine, but feel free to use more space if needed.)

function fun1() {
return new Promise(function (resolve, reject) {
setTimeout(() => {
reject('abey chuchae 1 resolved');
}, 2000);
});
}

async function fun2() {
var result = await fun1();
console.log(result);

return new Promise(function (resolve, reject) {
setTimeout(() => {
reject('abey chuchae 2 rejected');
}, 2000);

});
}

async function fun3() {
var result = fun2();
result.then(function (response) {
console.log(response);
}).catch(function(error){ 
console.log(error);
});
}
fun3();



Latitude Longitude
1.416910,103.795677,13z

1.290270,103.851959


function getRandomInRange(from, to, fixed) {
    return (Math.random() * (to - from) + from).toFixed(fixed) * 1;
    // .toFixed() returns string, so ' * 1' is a trick to convert to number
}


function generateLatLong(){
    var latLongs = [];
    for(var i=0;i<10000;i++) {
        latLongs[i] = [];
        latLongs[i]['latitude'] = getRandomInRange(1.416910,1.290270,6)
        latLongs[i]['longitude'] = getRandomInRange(103.851959,103.795677,6)
    }

    return latLongs;
}

generateLatLong()


http://192.168.4.66:8484/seed-scooters?name=manish&pass=manish


https://www.sitepoint.com/using-node-mysql-javascript-client/
https://www.npmjs.com/package/prototypes
http://www.java2s.com/Tutorials/Javascript/Node.js_Tutorial/1820__Node.js_HTTP_Files.htm
https://expressjs.com/en/guide/routing.html
https://expressjs.com/en/guide/writing-middleware.html

https://gis.stackexchange.com/questions/33238/getting-coordinates-from-click-or-drag-event-in-google-maps-api

var map = new google.maps.Map(document.getElementById('map'), {
     zoom: 12,
     center: new google.maps.LatLng('<%=center.lat%>', '<%=center.lon%>'),
     mapTypeId: google.maps.MapTypeId.ROADMAP
   });

   var infowindow = new google.maps.InfoWindow();

   var marker, i;
   <% scooters.forEach(function(sct){ %>
     marker = new google.maps.Marker({
       position: new google.maps.LatLng('<%= sct.latitude %>', '<%= sct.longitude %>'),
       // icon: 'http://localhost/node/demo/gahna/images/scooter.jpeg',
       map: map
     });

     google.maps.event.addListener(marker, 'click', (function(marker, i) {
       return function() {
         infowindow.setContent('<%= sct.name %>');
         infowindow.open(map, marker);
       }
     })(marker, i));
   <% })%>

        $ DEBUG=beam-app:* npm start

https://www.codementor.io/iykyvic/writing-your-nodejs-apps-using-es6-6dh0edw2o

pm2 start  app.js -i 0
