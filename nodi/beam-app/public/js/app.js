function setQueryString(priority) {
    if(priority == 'form') {
        queryString = queryString ? queryString : $.query.toString();
    } else {
        queryString = $.query.toString() ? $.query.toString() : queryString;
    }
}

function setLatLong(m) {
    if(typeof m === 'object' && typeof $.query === 'object') {
        if(typeof m.getCenter() !== 'undefined') {
            if(m.getCenter().lat()){
                $.query.SET('latitude',m.getCenter().lat());
            }
            if(m.getCenter().lng()){
                $.query.SET('longitude',m.getCenter().lng());
            }
        }
        window.history.replaceState(null,null, $.query.toString());
        processing = false;
    }
}

function setRange(m) {
    if(typeof m === 'object') {
        var b = map.getBounds();
        var ne = b.getNorthEast();
        var sw = b.getSouthWest();

        var clat = $.query.GET('latitude');
        var clng = $.query.GET('longitude');
        var bnelat = ne.lat();
        var bnelng = ne.lng();

        var range = getDistanceFromLatLonInKm(clat,clng,bnelat,bnelng);
        range = parseInt(range)*parseInt(m.getZoom());
        if(range){
            $.query.SET('range',range)
        }
    }
}

function callScooterApi() {
    var check = true;
    if(typeof $.query === 'object'){
        check = $.query.toString() !== queryString;
    }

    return check;
}

function getProcessing() {
    return  processing == true;
}

function getDistanceFromLatLonInKm(lat1,lon1,lat2,lon2) {
    var R = 6371; // Radius of the earth in km
    var dLat = deg2rad(lat2-lat1);  // deg2rad below
    var dLon = deg2rad(lon2-lon1); 
    var a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
    Math.sin(dLon/2) * Math.sin(dLon/2)
    ; 
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
    var d = R * c; // Distance in km
    return d;
}

function deg2rad(deg) {
    return deg * (Math.PI/180)
}

setInterval(function(){
    var c = callScooterApi();
    var p = getProcessing();
    if(c && !p){
        fetchScooters();
    }
},3000);

function queryStringValue (key) {  
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));  
}  

$(document).on('keypress','form input',function(e){
    $('form input[type="checkbox"]').prop('checked',false);
});

$(document).on('click','#fetch-all',function(e){
    if($(this).is(':checked')){
        $('form input').not(this).val(null);
    } else {
        $.query.REMOVE('fetchAll');
        setQueryString();
        $('form input[name="count"]').val(queryStringValue('count'));
        $('form input[name="range"]').val(queryStringValue('range'));
        $('form input[name="latitude"]').val(queryStringValue('latitude'));
        $('form input[name="longitude"]').val(queryStringValue('longitude'));
    }
});

$(document).on('click','.clear-form',function(e){
    $('form checkbox').prop('checked',false);
    $('form input:not([type="checkbox"])').val(null);
});

$('.number').keypress(function(event) {
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});

// Sets the map on all markers in the array.
function setMapOnAll(map) {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
    setMapOnAll(null);
}

// Shows any markers currently in the array.
function showMarkers() {
    setMapOnAll(map);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
    clearMarkers();
    drawnMarkers = [];
    markers = [];
}