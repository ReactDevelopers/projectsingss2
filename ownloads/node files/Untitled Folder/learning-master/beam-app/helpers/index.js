exports.cors = function cors() {
  	var cors = require('./cors');
  	return cors;
};

exports.print = function print($data,$heading){
    $debug = true;
    if(!$heading){
        $heading = "RECENT DATA";
    }
    if(!$data){
        $data = "NO DATA TO PRINT";
    }

    if($debug){
        console.log('\n\n\n-----------------------'+$heading+'-----------------------'+'\n\n\n'+ JSON.stringify($data,null,2)+'\n\n-----------------------'+'DEVELOPED BY: Manish Mahant-----------------------\n\n\n\n');
    }
}


/** Function for generating lat/long in a provided range of a given lat/long */
exports.randomLatLng = function (latitude, longitude, radiusInMeters) {

    var getRandomCoordinates = function (radius, uniform) {
        // Generate two random numbers
        var a = Math.random(),
            b = Math.random();

        // Flip for more uniformity.
        if (uniform) {
            if (b < a) {
                var c = b;
                b = a;
                a = c;
            }
        }

        // It's all triangles.
        return [
            b * radius * Math.cos(2 * Math.PI * a / b),
            b * radius * Math.sin(2 * Math.PI * a / b)
        ];
    };

    var randomCoordinates = getRandomCoordinates(radiusInMeters, true);

    // Earths radius in meters via WGS 84 model.
    var earth = 6378137;

    // Offsets in meters.
    var northOffset = randomCoordinates[0],
        eastOffset = randomCoordinates[1];

    // Offset coordinates in radians.
    var offsetLatitude = northOffset / earth,
        offsetLongitude = eastOffset / (earth * Math.cos(Math.PI * (latitude / 180)));

    // Offset position in decimal degrees.
    return {
        latitude: latitude + (offsetLatitude * (180 / Math.PI)),
        longitude: longitude + (offsetLongitude * (180 / Math.PI))
    }
};