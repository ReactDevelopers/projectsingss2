var ggmarkers = new Array();
var cntmmk = 0;
var blockload = 1;
var show_res_box = 0;
var profile_sign;
var lastOpenInfoWin = null;
var cur_time = '';
var table;
var pinType;
var infowindow = null;
var oms = null;
var map = null;
var markerGroups = {
    "student": [],
    "tutor": [],
    "matchRed": [],
    "matchPink": []
};
var profile_type = "";
var searchAddress = "";
var qualification = "";
var subject = "";
var level = "";
var tutor_experience = "";
var student_experience = "";
var pricepersession = "";
var pricepermonth = "";
var urlParam = "";
var is_profile_id = "";
var is_asset_id = "";
var asset_image = "";
var asset_name = "";
var search_name = "";
var nLat = '';
var nLang = '';
var searchClick = 'nclicked';
var resetClick = 'nclicked';
var countryChange = 'nChange';
var addChange = 'nChange';

var pinClk = false;
var fNowClk = false;
var markers = [];
var isMapDragged = false;
var zoomChange = false;
var prevZoomLvl = 0;
var currentMarker;
var ajaxRequest;
var dropdownajaxRequest;
var subjectRange;
var that = {};
var is_user_loggedin = JSON.parse(is_user_loggedin);
var isFindClicked = false;

//var cur_selected = "<?php echo $_COOKIE['country_code']; ?>";
var cur_selected = readCookie('country_code');
var cur_name = readCookie('country_name');

var pageNumber = 1;

String.prototype.filename = function(extension) {
    var s = this.replace(/\\/g, '/');
    s = s.substring(s.lastIndexOf('/') + 1);
    // return extension ? s.replace(/[?#].+$/, '') : s.split('.')[0];
    return extension ? s.split('.')[1] : s.split('.')[0];
}

var userCurrLat = 0;
var userCurrLng = 0;


window.sgmap = {
    init: function(curLat, curLong, currency_symbol) {
        that = this;
        if (that.readCookie('country_name')) {

            geocoder.geocode({ 'address': that.readCookie('country_name') }, function(results, status) {
                console.log("result" + results + "status" + status);
                if (status == google.maps.GeocoderStatus.OK) {
                    curLat = results[0].geometry.location.lat();
                    curLong = results[0].geometry.location.lng();
                }
            });

        }

        userCurrLat = curLat;
        userCurrLng = curLong;

        //   lat = that.geoplugin_latitude();
        //   lng = that.geoplugin_longitude();
        lat = curLat;
        lng = curLong;
        document.getElementById('UseraddressLattitude').value = curLat;
        document.getElementById('UseraddressLongitude').value = curLong;
        isFindClicked = true;
        var address2 = '';
        if (address2 && address2 != '') {

            that.getlatlong(address2, 'add');
            that.find_latlong(address2, 'add');
        } else {
            if (cur_selected) {
                /*   
              that.showLoder();
              countryChange = "change";
            
              var meVal = cur_selected;
              $('.result-list').html('');
              $('.listing > h4').html('Searching... ');

              var meText = cur_name;
            
              $('#CountryAddress2').val(meText);
              $('#CountryAddress').val(meText);
              that.writeCookie('country_name', meText, 1);
              that.writeCookie('country_code', meVal, 1);
              that._getFindDropdown(meVal, function() {
              that.getlatlong(meText, 'add');              
             }); */
                countryChange = "change";
                document.getElementById('UseraddressLattitude').value = userCurrLat;
                document.getElementById('UseraddressLongitude').value = userCurrLng;
            } else {
                document.getElementById('UseraddressLattitude').value = that.geoplugin_latitude();
                document.getElementById('UseraddressLongitude').value = that.geoplugin_longitude();
            }
            if (navigator.geolocation) {

                navigator.geolocation.getCurrentPosition(function(position) {

                    document.getElementById('UseraddressLattitude').value = position.coords.latitude;
                    document.getElementById('UseraddressLongitude').value = position.coords.longitude;
                    // var zoomval = that.setzoom();
                    // that.initialize(zoomval);
                }, function(failure) {
                    if (failure.message.indexOf("Only secure origins are allowed") == 0) {

                        document.getElementById('UseraddressLattitude').value = that.geoplugin_latitude();
                        document.getElementById('UseraddressLongitude').value = that.geoplugin_longitude();
                    }
                    // var zoomval = that.setzoom();
                    // that.initialize(zoomval);
                });
            }

        }

        that._getFindDropdown($("#country").val(), function() {

            var zoomval = that.setzoom();
            that.initialize(zoomval);
        });
        $('.result-list').html('');
        //  $('.listing > h4').html('Searching... ');
        jQuery('#UseraddressAddress, #CountryAddress').keyup(function(event) {
            addChange = 'change';
        });

        jQuery('select[name="data_country"]').on('change', function(e) {
            that.showLoder();
            countryChange = "change";

            var meVal = $(this).val();
            $('.result-list').html('');
            //      $('.listing > h4').html('Searching... ');

            var meText = $(this).children("option").filter(":selected").text();
            $('#CountryAddress2').val(meText);
            $('#CountryAddress').val(meText);
            that.writeCookie('country_name', meText, 1);
            that.writeCookie('country_code', meVal, 1);
            that._getFindDropdown(meVal, function() {
                that.getlatlong(meText, 'add');
            });


        });

        jQuery('#UseraddressAddress').on('change blur', function() {
            var searchedAddress = $(this).val();
            if (searchedAddress.trim() != '') {
                $('#CountryAddress').val($(this).val());
                that.getlatlong($(this).val(), 'add');
            }
        });
        jQuery(document).on('click', '.jump_pin', function() {
            blockload = 2;
            var prev_lat = $('#UseraddressLattitude').val();
            var prev_lng = $('#UseraddressLongitude').val();
            $('#UseraddressLattitude').val($(this).attr('data-lat'));
            $('#UseraddressLongitude').val($(this).attr('data-lng'));
            $('#UseraddressLattitude').val(prev_lat);
            $('#UseraddressLongitude').val(prev_lng);
        });


        $(".success").hide(8000);

        jQuery('#search,#search2').on('click', function(e) {
            that.showLoder();


            //      $('.listing > h4').html('Searching... ');
            $('.result-list').html('');
            e.preventDefault();
            resetClick = '';
            searchClick = 'clicked';
            $(this).val('Please Wait...');
            $(this).attr('disabled', 'disabled');

            $('#CountrySearchForm').submit();
            return false;

            first_lat = 0;
            first_lng = 0;
            var address;
            if ($("#CountryAddress").val() != '') {
                address = $("#CountryAddress").val();
            } else if ($("#UseraddressAddress").val() != '') {
                address = $("#UseraddressAddress").val();
            } else if ($("#UseraddressAddress").val() == '') {
                address = $("#UseraddressAddress").attr('placeholder');
            }

            that.getlatlong(address, 'add');
            that.find_latlong(address, 'add');
            show_res_box = 1;

            console.log("FilterCountryAdd:" + address);


            var url = window.location.href;

            url = new URL(url);
            var param_level = url.searchParams.get("level");
            var param_qualification = url.searchParams.get("qualification");

            if (param_level) {
                var url_object = window.location.href;
                url_object = updateURLParameter(url_object, 'qualification', $('#CountryQualification').val());
                url_object = updateURLParameter(url_object, 'experience', $('#CountrySExperience').val());
                url_object = updateURLParameter(url_object, 'profile_type', $('#profile_type').val());
                url_object = updateURLParameter(url_object, 'CountrySubject', $('#CountrySubject').val());
                url_object = updateURLParameter(url_object, 'search_name', $('#CountrySearchName').val());
                window.location.href = updateURLParameter(url_object, 'level', $('#CountryLevel').val());
            } else {
                url += '?subject=' + subject + '&profile_type=' + $('#profile_type').val() + '&level=' + $('#CountryLevel').val() + '&qualification=' + $('#CountryQualification').val() + '&experience=' + $('#CountrySExperience').val() + '&search_name=' + $('#CountrySearchName').val()
                window.location.href = url;
            }


        });


        jQuery('#reset, #reset2').on('click', function() {
            that.showLoder();
            $('.result-list').html('');
            //      $('.listing > h4').html('Searching... ');
            resetClick = 'clicked';
            isFindClicked = false;
            searchClick = '';
            first_lat = 0;
            first_lng = 0;

            $('#CountrySubject option:selected').removeAttr('selected');
            $('#CountrySubject').trigger('chosen:updated');
            $('#CountryLevel option:selected').removeAttr('selected');
            $('#CountryLevel').trigger('chosen:updated');
            $('#CountrySExperience option:selected').removeAttr('selected');
            $('#CountrySExperience').trigger('chosen:updated');
            $('#CountryQualification option:selected').removeAttr('selected');
            $('#CountryQualification').trigger('chosen:updated');
            $("#CountryPricepersession").val('');
            $("#CountryPricepermonth").val('');
            $("#CountryAddress").val('');
            $("#profile_type").val('A');

            $('#CountrySubject2 option:selected').removeAttr('selected');
            $('#CountrySubject2').trigger('chosen:updated');
            $('#CountryLevel2 option:selected').removeAttr('selected');
            $('#CountryLevel2').trigger('chosen:updated');
            $('#CountryTExperience option:selected').removeAttr('selected');
            $('#CountryTExperience').trigger('chosen:updated');
            $('#CountryQualification2 option:selected').removeAttr('selected');
            $('#CountryQualification2').trigger('chosen:updated');
            $("#CountryPricepersession2").val('');
            $("#CountryPricepermonth2").val('');
            $("#CountryAddress2").val('');
            $("#CountrySearchName").val('');
            $("#CountryPricepersessionMin").val('');
            $("#CountryPricepersessionMax").val('');
            $("#CountryPricepermonthMin").val('');
            $("#CountryPricepermonthMax").val('');
            // $("#CountryRadius").val('');
            var address;
            if ($("#CountryAddress").val() != '' && $("#profile_type").val() == 'S') {
                address = $("#CountryAddress").val();
            } else if ($("#CountryAddress2").val() != '' && $("#profile_type").val() == 'T') {
                address = $("#CountryAddress2").val();
            } else if ($("#UseraddressAddress").val() != '') {
                address = $("#UseraddressAddress").val();
            } else if ($("#UseraddressAddress").val() == '') {
                address = $("#UseraddressAddress").attr('placeholder');
            }
            // $('#CountrySearchForm').submit();
            zoomval = that.setzoom();
            that.initialize(zoomval);
            show_res_box = 0;


            var url = window.sgmap.base_url() + 'search';
            url += '?subject=clearall'
            window.location.href = url;

        });



        var windowHeight = eval(jQuery(window).height()) - eval(eval($('.navbar').height()));
        $('#map_canvas').css('height', (windowHeight - 2));

        jQuery(window).resize(function() {
            windowHeight = eval(jQuery(window).height()) - eval(eval($('.navbar').height()) + eval(22));
            $('#map_canvas').css('height', windowHeight - 2);
            $('#map_canvas').css('width', jQuery(window).width());
            var zoomval = that.setzoom();
            that.initialize(zoomval);
        });
    },
    base_url: function() {
        var host = baseURL = window.location.hostname;
        if (host == 'localhost') {
            baseURL = 'http://' + host + '/tueetor-new/';
        } else if (host == '103.15.232.35') {
            baseURL = 'http://' + host + '/singsys-stg3/tueetor-new/';
        } else if (host == '13.229.67.232') {
            baseURL = 'http://' + host + '/tueetor-new/';
        } else {
            baseURL = 'https://' + host + '/';
        }

        return baseURL;
    },
    writeCookie: function(name, value, days) {
        var date, expires;
        if (days) {
            date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        } else {
            expires = "";
        }
        document.cookie = name + "=" + value + expires + "; path=/";
    },
    readCookie: function(name) {
        var i, c, ca, nameEQ = name + "=";
        ca = document.cookie.split(';');
        for (i = 0; i < ca.length; i++) {
            c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) == 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return '';
    },
    showLoder: function() {
        $('.loader').show();
    },
    hideLoder: function() {
        $('.loader').hide();
    },


    geoplugin_latitude: function() {
        //  return (userCurrLat) ? userCurrLat : '1.352083';
        return (userCurrLat != 0) ? userCurrLat : '15.8700';
    },
    getAwsImageUrl: function(image) {
        var imageUrl = 'https://tueetor.s3.amazonaws.com/uploads/' + image;
        return imageUrl;
    },
    geoplugin_longitude: function() {
        //   return (userCurrLng) ? userCurrLng : '103.81983600000001';
        return (userCurrLng != 0) ? userCurrLng : '100.9925';
    },
    _getFindDropdown: function(countryCode, callback) {
        if (typeof countryCode == 'undefined' || countryCode == '') {
            countryCode = $('input[name="data_country"]').val();
        }
        if (dropdownajaxRequest) {
            dropdownajaxRequest.abort();
        }

        var url = new URL(window.location.href);
        var param_level = url.searchParams.get("level");
        var param_qualification = url.searchParams.get("qualification");

        dropdownajaxRequest = $.ajax({
            type: 'post',
            url: 'pages/getFindnowDropDownData',
            data: { countryCode: countryCode },
            dataType: "json",
            async: true,
            success: function(response) {

                $("#CountryLevel, #CountryLevel2, #CountryQualification2, #CountryQualification, #CountrySubject, #CountrySubject2").html('');

                $('#CountrySubject, #CountrySubject2').each(function() {
                    var placeholderTxt = $(this).attr('data-placeholder');
                    console.log(placeholderTxt + ' ==== ');
                    if (placeholderTxt != '') {
                        $(this).html('<option value=""></option>');
                    }
                });


                $.each(response.subject, function(key, subject) {

                    $('#CountrySubject, #CountrySubject2').append('<option value="' + subject.key + '">' + subject.value + '</option>');

                });
                // console.log($('#CountrySubject').html());

                $.each(response.level, function(key, level) {
                    if (level.key == param_level) {
                        $("#CountryLevel, #CountryLevel2").append('<option value="' + level.key + '" selected="selected">' + level.value + '</option>');
                    } else {
                        $("#CountryLevel, #CountryLevel2").append('<option value="' + level.key + '">' + level.value + '</option>');
                    }

                });
                $.each(response.qualification, function(key, qualification) {
                    if (qualification.key == param_qualification) {
                        $("#CountryQualification2, #CountryQualification").append('<option value="' + qualification.key + '" selected="selected">' + qualification.value + '</option>');
                    } else {
                        $("#CountryQualification2, #CountryQualification").append('<option value="' + qualification.key + '">' + qualification.value + '</option>');
                    }


                });
                $('#CountrySubject').val(searchedSubject);
                $('#CountrySubject, #CountrySubject2, #CountryLevel, #CountryLevel2, #CountryQualification2, #CountryQualification ').trigger("chosen:updated");
                callback(countryCode);
            }
        });

    },
    getlatlong: function(address, type) {
        var geocoder = new google.maps.Geocoder();
        console.log("getaddr" + address);
        geocoder.geocode({ 'address': address }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var latitude = results[0].geometry.location.lat();
                var longitude = results[0].geometry.location.lng();
                console.log("getlat" + latitude);
                document.getElementById('UseraddressLattitude').value = latitude;
                document.getElementById('UseraddressLongitude').value = longitude;
                if (type == 'cou') {
                    $('#UseraddressAddress').val('');
                } else {
                    for (var i = 0; i < results[0].address_components.length; i++) {
                        for (var b = 0; b < results[0].address_components[i].types.length; b++) {
                            if (results[0].address_components[i].types[b] == "country") {
                                //this is the object you are looking for
                                country = results[0].address_components[i];
                                break;
                            }
                        }
                    }

                    $("#country").val(country.short_name);
                    $('#country').trigger("chosen:updated");
                }
                zoomval = that.setzoom();
                that.initialize(zoomval);
            }
        });

    },
    find_latlong: function(address, type) {
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 'address': address }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var latitude = results[0].geometry.location.lat();
                var longitude = results[0].geometry.location.lng();
                console.log("findlat" + latitude);
                document.getElementById('UseraddressLattitude').value = latitude;
                document.getElementById('UseraddressLongitude').value = longitude;
                that.getAddress(latitude, longitude);
            }
        });

    },

    getAddress: function(lat, lng) {
        //  var conutrycode = "SG";

        var conutrycode = cur_selected;
        geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(lat, lng);
        geocoder.geocode({ 'latLng': latlng }, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    if (conutrycode == '') {
                        $('#UseraddressAddress').attr('placeholder', 'Enter Address as "' + results[1].formatted_address + '"');
                        $("#CountryAddress2").val(results[1].formatted_address);
                        $("#CountryAddress").val(results[1].formatted_address);
                    } else {

                        $('#UseraddressAddress').attr('placeholder', (results[0].formatted_address) ? results[0].formatted_address : 'Enter Address as  491, Main Street Po Box 1022, Seattle, Washington 98104');

                        $("#CountryAddress2").val(results[0].formatted_address);
                        $("#CountryAddress").val(results[0].formatted_address);

                    }
                    for (var i = 0; i < results[0].address_components.length; i++) {
                        for (var b = 0; b < results[0].address_components[i].types.length; b++) {
                            if (results[0].address_components[i].types[b] == "country") {
                                //this is the object you are looking for
                                country = results[0].address_components[i];

                                if ($("#CountryAddress2").val() == "") {
                                    $("#CountryAddress2").val(results[0].formatted_address);
                                }
                                if ($("#CountryAddress").val() == "") {
                                    $("#CountryAddress").val(results[0].formatted_address);
                                }

                                break;
                            }
                        }
                    }

                    $("#country").val(country.short_name);
                    $('#country').trigger("chosen:updated");
                    $("#country").val(country.short_name);
                    $('#country').trigger("chosen:updated");
                    that.writeCookie('country_name', country.long_name, 1);
                    that.writeCookie('country_code', country.short_name, 1);
                } else {}
            } else {}
        });
    },
    initialize: function(zoomval) {

        var lat = $('#UseraddressLattitude').val();
        var lng = $('#UseraddressLongitude').val();

        //   var lat = userCurrLat;
        //   var lng = userCurrLng;

        var geocoder = new google.maps.Geocoder();

        if (that.readCookie('country_name')) {
            console.log(that.readCookie('country_name'));
            var cookie_country = that.readCookie('country_name');

            var temp_latlong = that.getlatlong(cookie_country, 'add');
            console.log("temp_latlong" + temp_latlong);
            geocoder.geocode({ 'address': cookie_country }, function(results, status) {
                console.log("geocode" + results + " info" + status);
                if (status == google.maps.GeocoderStatus.OK) {
                    lat = results[(results.length - 1)].geometry.location.lat();
                    lng = results[(results.length - 1)].geometry.location.lng();
                } else if (status == google.maps.GeocoderStatus.ZERO_RESULTS) {
                    console.log("Bad destination address.");
                } else {
                    console.log("Error calling Google Geocode API.");
                }


            });

        }

        if (lat == "" || lng == "") {
            lat = that.geoplugin_latitude();
            lng = that.geoplugin_longitude();
        }
        var myOptions = {
            zoom: zoomval,
            center: new google.maps.LatLng(lat, lng),
            // mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoomControl: true,
            panControl: false,
            scaleControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.SMALL,
                position: google.maps.ControlPosition.LEFT_BOTTOM
            },
            mapTypeControl: false,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.TOP_LEFT
            },
        }

        map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

        prevZoomLvl = zoomval;

        var centerControlDiv = document.createElement('div');
        var centerControl = new CenterControl(centerControlDiv, map);
        centerControlDiv.index = 1;
        map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(centerControlDiv);

        var loudhailerDiv = document.createElement('div');
        var loudhailer = new loudhailerControl(loudhailerDiv, map);
        loudhailerDiv.index = 1;
        map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(loudhailerDiv);

        var locations = [lat + ',' + lng];
        $('.marker-check').prop("checked", true);
        oms = new OverlappingMarkerSpiderfier(map, { markersWontMove: false, markersWontHide: false, keepSpiderfied: true });
        /*  */

        // geocoder = new google.maps.Geocoder();

        var latlng = new google.maps.LatLng(lat, lng);
        var on_load_country_code = '';
        console.log("latlang_value=" + latlng);
        geocoder.geocode({ 'latLng': latlng }, function(results, status) {


            // $('#UseraddressAddress').attr('placeholder', 'Enter Address as "' + results[1].formatted_address + '"');
            // $("#CountryAddress2").val(results[1].formatted_address);
            // $("#CountryAddress").val(results[1].formatted_address);
            if (status == google.maps.GeocoderStatus.OK) {

                if (results[1]) {

                    for (var i = 0; i < results[0].address_components.length; i++) {
                        for (var b = 0; b < results[0].address_components[i].types.length; b++) {
                            if (results[0].address_components[i].types[b] == "country") {

                                country = results[0].address_components[i];
                                break;
                            }
                        }
                    }
                    $("#country").val(country.short_name);
                    $('#country').trigger("chosen:updated");
                    on_load_country_code = country.short_name;
                } else {}
            } else {}
            //mapmarkers(lat, lng, "images/map_center.png", '', '', '', '', '', 'center', '');
            console.log('on_load_country_code' + on_load_country_code);
            gettutorList(on_load_country_code);
        });



        var rad = $('#CountryRadius').val();
        if (rad == '') {
            rad = 3000;
        }
        var subjectPoint = {
            point: new google.maps.LatLng(lat, lng),
            radius: parseInt(rad), //default radius
            color: '#00AA00'
        }

        // var map;
        var geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(lat, lng);

        //render the range
        var subjectMarker = new google.maps.Marker({
            position: subjectPoint.point,
            title: 'Subject'

        });


        var subjectRange = new google.maps.Circle({
            map: map,
            radius: subjectPoint.radius, // metres
            strokeColor: '#FE2C00',
            fillOpacity: 0.0,
            strokeWeight: 1,
            editable: false
        });
        subjectRange.bindTo('center', subjectMarker, 'position');


        /**
         * This functio  is used to add custom control to map (FOR MY LOCATION CONTROL)
         * @param   object  controlDiv  control container div
         * @param   object  map         Object of current map
         * @return  object              object and set currentlocation.
         */
        function CenterControl(controlDiv, map) {

            // Set CSS for the control border.
            var controlUI = document.createElement('div');
            controlUI.style.backgroundColor = '#fff';
            controlUI.style.border = '2px solid #fff';
            controlUI.style.borderRadius = '2px';
            controlUI.style.boxShadow = '0 1px 1px rgba(0,0,0,.3)';
            controlUI.style.cursor = 'pointer';
            controlUI.style.margin = '10px';
            controlUI.style.textAlign = 'center';
            controlUI.title = 'Click to recenter the map';
            controlUI.class = 'set-map-center';
            controlUI.id = 'map-center';
            controlDiv.appendChild(controlUI);

            // Set CSS for the control interior.
            var controlText = document.createElement('div');
            controlText.style.color = 'rgb(25,25,25)';
            controlText.style.paddingLeft = '3px';
            controlText.style.paddingRight = '3px';
            controlText.innerHTML = '<i class="material-icons" style="font-size:18px;line-height: 26px;">my_location</i>';
            controlUI.appendChild(controlText);

            // Setup the click event listeners: simply set the map to current location.
            controlUI.addEventListener('click', function() {
                if (navigator.geolocation) {

                    navigator.geolocation.getCurrentPosition(function(position) {
                        initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                        map.setCenter(initialLocation);
                        document.getElementById('UseraddressLattitude').value = position.coords.latitude;
                        document.getElementById('UseraddressLongitude').value = position.coords.longitude;
                        var marker = null;
                        marker = new google.maps.Marker({
                            map: map,
                            zoom: that.setzoom(),
                            position: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
                            icon: "images/map_center.png",
                            animation: google.maps.Animation.DROP
                        });
                        that.getAddress(position.coords.latitude, position.coords.longitude);
                        localStorage.setItem("allowedlocation", true);
                    });
                }
            });

        }

        function loudhailerControl(loudhailerDiv) {

            // Set CSS for the control border.
            var loudhailerUI = document.createElement('div');
            // loudhailerUI.style.backgroundColor = '#fff';
            // loudhailerUI.style.border = '2px solid #fff';
            loudhailerUI.style.borderRadius = '2px';
            loudhailerUI.style.boxShadow = '0 1px 1px rgba(0,0,0,.3)';
            loudhailerUI.style.cursor = 'pointer';
            loudhailerUI.style.position = 'absolute';
            loudhailerUI.style.bottom = '0';
            loudhailerUI.style.left = '10px';
            loudhailerUI.style.textAlign = 'center';
            loudhailerUI.title = 'Shout Out';
            loudhailerUI.class = 'map-shout-out';
            loudhailerUI.id = 'map-shout-out';
            loudhailerDiv.appendChild(loudhailerUI);

            // Set CSS for the loudhailer interior.
            var loudhailerText = document.createElement('div');
            loudhailerText.style.color = 'rgb(25,25,25)';
            loudhailerText.style.lineHeight = '8px';
            loudhailerText.style.paddingRight = '0px';
            loudhailerText.innerHTML = '<i class="shout-out-icons" style="font-size:18px;"></i>';
            loudhailerUI.appendChild(loudhailerText);

            // Setup the click event listeners: simply set the map to current location.
            loudhailerUI.addEventListener('click', function() {
                bypassC = false;
                // $('.shoutout-overlay').slideDown(500);
                $('#shoutout-btn').trigger('click');
            });

        }
        if (localStorage.getItem("allowedlocation") && countryChange != "change" && addChange != 'change') {
            // localStorage.setItem("allowedlocation",true);
            navigator.geolocation.getCurrentPosition(function(position) {
                initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                map.setCenter(initialLocation);
                document.getElementById('UseraddressLattitude').value = position.coords.latitude;
                document.getElementById('UseraddressLongitude').value = position.coords.longitude;
                var marker = null;
                marker = new google.maps.Marker({
                    map: map,
                    zoom: that.setzoom(),
                    position: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
                    icon: "images/map_center.png",
                    animation: google.maps.Animation.DROP
                });
                var subjectPoint2 = {
                    point: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
                    radius: parseInt(3000), //default radius
                    color: '#00AA00'
                }
                var subjectMarker2 = new google.maps.Marker({
                    position: subjectPoint2.point,
                    title: 'Subject'

                });
                map.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
                subjectRange.bindTo('center', subjectMarker2, 'position');

                that.getAddress(position.coords.latitude, position.coords.longitude);
            });
        }

        function mapmarkers(lat, lng, icon, name, qual, distance, address, image, type, userid, user_type, pinType, map_img, is_asset_id) {

            if (type == 'center') {
                map.setCenter(new google.maps.LatLng(lat, lng));
            }
            var marker = null;
            marker = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(lat, lng),
                icon: icon,
                oldImg: icon,
                animation: null
            });
            if (!markerGroups[pinType]) markerGroups[pinType] = [];
            markerGroups[pinType].push(marker);

            if (type == 'dup') {
                marker.setIcon('images/big_pin.png');
                /* marker.setMap(null); */
            }

            oms.addMarker(marker);
            if (name != '' && qual != '') {
                // distance = distance * 1609.34;
                // distance = Math.round(distance);
                if (image == '') {
                    image = "images/profile_pic.jpg";
                } else {
                    image = "uploads/" + image;
                }

                /*
                var url_string;
                if (is_asset_id > 0) {
                    url_string = 'searches/get_asset_availablity_map';
                } else {
                    url_string = user_type == 'S' ? 'searches/get_student_availablity_map' : 'searches/get_tutor_availablity_map';
                }
                */

                var url_string;
                if (is_agency == 'yes' && user_type == 'T') {
                    url_string = 'searches/get_asset_availablity_map';
                }
                if (is_agency == 'no' && user_type == 'T') {
                    url_string = 'searches/get_tutor_availablity_map';
                }
                if (is_agency == 'no' && user_type == 'S') {
                    url_string = 'searches/get_student_availablity_map';
                }


                //open infowindo on click event on marker.
                oms.addListener('click', function(marker, event) {
                    if (lastOpenInfoWin)
                        lastOpenInfoWin.close();
                });
                markers.push(marker);
                google.maps.event.addListener(marker, 'click', function(mk, idx) {
                    if (profile_type || qualification || subject || level || tutor_experience || student_experience || pricepersession || pricepermonth) {
                        urlParam = "?lat=" + this.position.lat() + "&lang=" + this.position.lng();
                    }
                    _setMarkerDefaultImage();

                    toggleBounce(marker);
                    currentMarker = marker;
                    if (lastOpenInfoWin)
                        lastOpenInfoWin.close();
                    // marker.setIcon(icon);
                    // marker.setIcon("images/big_"+map_img);
                    pinClk = true;
                    if (fNowClk == true) {
                        pinClk = false;
                        /*zoomval = setzoom();
                        map.setZoom(zoomval);*/
                        map.setCenter(new google.maps.LatLng(this.position.lat(), this.position.lng()));
                    }

                    $('#mymtoonton').html('<div class="loader-img"><div class="loading"><img src="images/loader.gif" class="loading-img" alt="Please Wait"/><br />Please Wait..</div>  </div>');
                    $('#mymtoonton').show();
                    $.ajax({
                        type: 'post',
                        url: '' + url_string,
                        data: { user_id: userid, user_type: user_type, pinclick: pinClk, nameclick: fNowClk, asset_id: is_asset_id, 'distance': (distance * 1.60934).toFixed(2) },
                        dataType: "html",
                        async: true,
                        success: function(contentString) {
                            $("#mymtoonton").html('<div class="close-pop"></div>');
                            $("#mymtoonton").append(contentString);

                            pinClk = false;
                            fNowClk = false;

                        }
                    });



                });


            }

            return marker;
        }

        google.maps.event.addListener(map, 'click', function(event) {
            $('.close-pop').trigger('click');
            _setMarkerDefaultImage();
        });

        function toggleBounce(marker) {
            if (marker.getAnimation() !== null) {
                marker.setAnimation(null);
            } else {
                marker.setAnimation(google.maps.Animation.BOUNCE);
            }
        }

        $(document).on('click', '.close-pop', function() {
            $(this).parent('div#mymtoonton').hide();
            _setMarkerDefaultImage();
        });

        function _setMarkerDefaultImage() {
            if (currentMarker) {
                currentMarker.setAnimation(null);
            }
        }

        function _getMatchIcon(match_profile, profileType, is_agency) {
            var map_img;

            if (match_profile == 2) {
                map_img = 'red_Marker' + profileType + '.png';
                pinType = 'matchRed';
                if (is_agency == "yes") {
                    map_img = 'agent_marker_red.png';
                    // map_img = 'agency_marker.png';
                    pinType = 'asset';
                    // pinType = 'matchAgentRed';
                }
            } else if (match_profile == 1) {
                map_img = 'pink_Marker' + profileType + '.png';
                pinType = 'matchPink';
                if (is_agency == "yes") {
                    map_img = 'agent_marker_pink.png';
                    // map_img = 'agency_marker.png';
                    pinType = 'asset';
                    // pinType = 'matchAgentPink';
                }
            } else if (match_profile == 0) {
                map_img = 'green_Marker' + profileType + '.png';
                pinType = (profileType == 'S') ? 'student' : 'tutor';
                if (is_agency == "yes") {
                    map_img = 'agency_marker.png';
                    pinType = 'asset';
                }

            } else {
                map_img = 'green_Marker' + profileType + '.png';
                pinType = (profileType == 'S') ? 'student' : 'tutor';
                if (is_agency == "yes") {
                    map_img = 'agency_marker.png';
                    pinType = 'asset';
                }

            }
            return map_img;
        }

        function setMapOnAll(map) {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
        }
        var ajaxBusy = false;








        function gettutorList(country_code) {
            that.showLoder();
            var first_lat = 0;
            var first_lng = 0;
            var param = {};
            var tot_res_pin = 0;
            var pin1;
            var pin2;
            var search_res = [];
            var dis2 = [];
            var dup_res = [];
            var lat = $('#UseraddressLattitude').val();
            var lng = $('#UseraddressLongitude').val();
            var cur_address = $("#UseraddressAddress").val();

            var match_profileT = 0;
            var match_profileS = 0;

            $("#search_results").html('');



            if ($('#profile_type').val() == 'S') {

                profile_type = $('#profile_type').val();
                qualification = $('#CountryQualification').val();
                subject = $('#CountrySubject').val();
                level = $('#CountryLevel').val();
                tutor_experience = $('#CountrySExperience').val();
                student_experience = $('#CountrySExperience').val();
                pricepersessionMin = $('#CountryPricepersessionMin').val();
                pricepersessionMax = $('#CountryPricepersessionMax').val();
                pricepermonthMin = $('#CountryPricepermonthMin').val();
                pricepermonthMax = $('#CountryPricepermonthMax').val();
                searchAddress = $('#CountryAddress').val();
                search_name = $('#CountrySearchName').val();
            } else if ($('#profile_type').val() == 'T') {

                profile_type = $('#profile_type').val();
                qualification = $('#CountryQualification').val();
                subject = $('#CountrySubject').val();
                level = $('#CountryLevel').val();
                tutor_experience = $('#CountrySExperience').val();
                student_experience = $('#CountrySExperience').val();

                pricepersessionMin = $('#CountryPricepersessionMin').val();
                pricepersessionMax = $('#CountryPricepersessionMax').val();
                pricepermonthMin = $('#CountryPricepermonthMin').val();
                pricepermonthMax = $('#CountryPricepermonthMax').val();

                searchAddress = $('#CountryAddress').val();
                search_name = $('#CountrySearchName').val();
            }

            if (resetClick == 'clicked' || !subject) {
                profile_type = '';
                qualification = '';
                subject = '';
                level = '';
                tutor_experience = '';
                student_experience = '';
                pricepersessionMin = '';
                pricepersessionMax = '';
                pricepermonthMin = '';
                pricepermonthMax = '';
                searchAddress = '';
                search_name = '';
            }
            if (searchClick == 'clicked') {
                isFindClicked = true;
            }

            var lat = $('#UseraddressLattitude').val();
            var lng = $('#UseraddressLongitude').val();

            //  var lat = userCurrLat;
            //  var lng = userCurrLng;


            var myOptions = {
                zoom: zoomval,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            $("#country").val(country.short_name);
            param = {
                'lat': lat,
                'lng': lng,
                'cur_country': $("#country").val(),
                'country_name': (that.readCookie('country_name')) ? that.readCookie('country_name') : $("#CountryAddress").val(),
                'cur_address': searchAddress,
                'profile_type': profile_type,
                'qualification': qualification,
                'subject': subject,
                'level': level,
                'tutor_experience': tutor_experience,
                'student_experience': student_experience,
                'pricepersessionMin': pricepersessionMin,
                'pricepermonthMin': pricepermonthMin,
                'search_name': search_name

            };
            var map_img;
            var user_type_img;
            var user_profile;
            var radiusDistance;
            var table = '';
            var atable = '';

            table = '<table style="width: 100%;" class="responstable">';
            table += '<tr>' +
                '<th colspan="2" rowspan="2"> Name </th>' +
                '<th rowspan="2">Distance From You (km)</th>' +
                '<th colspan="2" style="text-align: center !important;">' + ((profile_type == 'T') ? 'Rate' : 'Budget') + '</th>' +
                '</tr>' +
                '<tr>' +
                '<th style="text-align: center !important;">Session</th>' +
                '<th style="text-align: center !important;">Month</th>' +
                '</tr>';

            atable = '<table style="width: 100%;" class="responstable">';
            atable += '<tr>' +
                '<th colspan="2" rowspan="2"> Name </th>' +
                '<th rowspan="2">Distance From You (km)</th>' +
                '<th colspan="3" style="text-align: center !important;">' + ((profile_type == 'T') ? 'Rate' : 'Budget') + '</th>' +
                '</tr>' +
                '<tr>' +
                '<th style="text-align: center !important;">Session</th>' +
                '<th style="text-align: center !important;">Month</th>' +
                '<th style="text-align: center !important;">Terms</th>' +
                '</tr>';


            var bounds = map.getBounds();
            /*
            google.maps.event.addListener(map, 'bounds_changed', function() {
                bounds = map.getBounds();
            });
            */

            /* console.log('bounds'+bounds);
            
            var center = bounds.getCenter();
            var ne = bounds.getNorthEast();
            // r = radius of the earth in statute miles
            var r = 3963.0;
            // Convert lat or lng from decimal degrees into radians (divide by 57.2958)
            var lat1 = center.lat() / 57.2958;
            var lon1 = center.lng() / 57.2958;
            var lat2 = ne.lat() / 57.2958;
            var lon2 = ne.lng() / 57.2958; */

            // distance = circle radius from center to Northeast corner of bounds
            /* radiusDistance = r * Math.acos(Math.sin(lat1) * Math.sin(lat2) + Math.cos(lat1) * Math.cos(lat2) * Math.cos(lon2 - lon1)); */
            radiusDistance = '';
            // console.log('Dragged = '+isMapDragged);
            var currSelectedCountry = $("#country").val();
            /*  if (isMapDragged || zoomChange) {
                 lat = center.lat();
                 lng = center.lng();
                 // currSelectedCountry = '';
             } */


            $(document).ajaxStart(function() {
                ajaxBusy = true;
            }).ajaxStop(function() {
                ajaxBusy = false;
            });

            if (ajaxBusy && !isMapDragged) {
                console.log('ajax is busy');
                return false;
            }


            var fullCount = 0;
            var partialCount = 0;
            var greenCount = 0;

            var afullCount = 0;
            var apartialCount = 0;
            var agreenCount = 0;

            lat = parseFloat(lat);
            lng = parseFloat(lng);

            var temp_url = window.location.href;
            var get_url = new URL(temp_url);
            pageNumber = get_url.searchParams.get("page");

            if (!pageNumber) {
                pageNumber = 1;
            }


            var url = new URL(window.location.href);
            var param_level = url.searchParams.get("level");

            if (param_level) {
                level = param_level;
            }

            ajaxRequest = $.ajax({
                type: 'post',
                url: 'searches/getajaxtutorslist',
                async: true,
                cache: false,
                beforeSend: function() {

                    if (ajaxRequest) {
                        ajaxRequest.abort();
                    } else {
                        setTimeout(function() {
                            // null beforeSend to prevent recursive ajax call
                            // $.ajax($.extend(options, { beforeSend: $.noop }));
                        }, 1000);
                    }

                },
                data: {
                    lat: lat,
                    lng: lng,
                    cur_country: currSelectedCountry,
                    cur_address: cur_address,
                    profile_type: profile_type,
                    qualification: qualification,
                    subject: subject,
                    level: level,
                    tutor_experience: tutor_experience,
                    student_experience: student_experience,
                    pricepersessionMin: pricepersessionMin,
                    pricepersessionMax: pricepersessionMax,
                    pricepermonthMin: pricepermonthMin,
                    pricepermonthMax: pricepermonthMax,
                    isFindClicked: isFindClicked,
                    radiusDistance: radiusDistance,
                    search_name: search_name,
                    pageNumber: pageNumber
                },
                success: function(data) {
                    data = JSON.parse(data);

                    tot = data.records;
                    setMapOnAll(null);
                    isMapDragged = false;
                    zoomChange = false;

                    $('#st_address').attr('placeholder', $('#UseraddressAddress').attr('placeholder'));
                    $('#st_lat').val($('#UseraddressLattitude').val());
                    $('#st_lng').val($('#UseraddressLongitude').val());

                    markers = [];

                    var predTr = '<tr class="jump_pin"><td class="bdr_right_none" colspan="5"><img src="images/' + profile_type + '_red.png" >&nbsp; Subject & Level Match</td></tr>';
                    var ppinkTr = '<tr class="jump_pin"><td class="bdr_right_none" colspan="5"><img src="images/' + profile_type + '_pink.png" >&nbsp; Subject Match Only </td></tr>';

                    var apredTr = '<tr class="jump_pin"><td class="bdr_right_none" colspan="6"><img src="images/' + profile_type + '_red.png" >&nbsp; Subject & Level Match</td></tr>';
                    var appinkTr = '<tr class="jump_pin"><td class="bdr_right_none" colspan="6"><img src="images/' + profile_type + '_pink.png" >&nbsp; Subject Match Only </td></tr>';

                    var redTr = '';
                    var pinkTr = '';
                    var greenTr = '';

                    var aredTr = '';
                    var apinkTr = '';
                    var agreenTr = '';

                    var newHtml = '';

                    for (i = 0; i < tot.length; i++) {
                        var isredTr = false;
                        var ispinkTr = false;
                        var isgreenTr = false;
                        var perMonth = 0;
                        var persession = 0;
                        var terms = 0;

                        if (i == 0) {

                            if (tot[i].Useraddress.lattitude) {
                                var first_latlng = new google.maps.LatLng(tot[i].Useraddress.lattitude, tot[i].Useraddress.longitude);
                                map.setCenter(first_latlng);
                            }
                        }

                        address = tot[i].Useraddress.address;
                        latitude = tot[i].Useraddress.lattitude;
                        longitude = tot[i].Useraddress.longitude;
                        distance = tot[i].Useraddress.distance;
                        radius = tot[i].Useraddress.radius;
                        name = tot[i].User.name;
                        userid = tot[i].User.id;
                        image = tot[i].User.image;
                        is_agency = tot[i].User.is_agency;
                        user_type = tot[i].User.user_type;
                        transaction_exp_date = tot[i].Transaction.exp_date;

                        //  console.log("pinulat"+latitude+"pinlongi"+longitude);

                        var userMatch = (tot[i].Useraddress.userMatch).split(',');
                        match_profileT = userMatch[0];
                        match_profileS = userMatch[1];
                        is_profile_id = tot[i].Useraddress.profile_id;
                        is_asset_id = tot[i].Useraddress.asset_id;
                        asset_image = tot[i].Asset.image;
                        asset_name = (tot[i].Asset.display_name_2) ? tot[i].Asset.display_name_1 + ' - ' + tot[i].Asset.display_name_2 : tot[i].Asset.display_name_1;

                        var haveCourse = (tot[i].Course.id_course == null) ? false : true;

                        if (is_agency == 'yes' && user_type == 'T') {
                            perMonth = (tot[i].Course.pricepermonth == null) ? "N.A" : tot[i].Course.pricepermonth;
                            persession = (tot[i].Course.pricepersession == null) ? "N.A" : tot[i].Course.pricepersession;
                            terms = (tot[i].Course.priceperterm == null) ? "N.A" : tot[i].Course.priceperterm;

                        } else if (user_type == 'T') {
                            perMonth = (tot[i].Tutorprofile.pricepermonth == null) ? "N.A" : tot[i].Tutorprofile.pricepermonth;
                            persession = (tot[i].Tutorprofile.pricepersession == null) ? "N.A" : tot[i].Tutorprofile.pricepersession;
                        } else {
                            perMonth = (tot[i].Studentprofile.pricepermonth == null) ? "N.A" : tot[i].Studentprofile.pricepermonth;
                            persession = (tot[i].Studentprofile.pricepersession == null) ? "N.A" : tot[i].Studentprofile.pricepersession;
                        }

                        will_to_teach = user_type == 'T' ? tot[i].Tutorprofile.will_to_teach : tot[i].Studentprofile.will_to_teach;

                        if (will_to_teach == 'A') {
                            travel_ablity = 'Anywhere';
                        } else {
                            travel_ablity = radius == 0 ? 'No' : 'Up to ' + radius + ' m';
                        }

                        if (user_type == 'T') {
                            profile_sign = '<img src="images/profile_T.png" >';
                            isgreenTr = true;
                        } else if (user_type == 'S') {
                            profile_sign = '<img src="images/profile_S.png" >';
                            isgreenTr = true;
                        }
                        var exp_date = 1;
                        if (is_profile_id > 0) {
                            if (exp_date == 1) {
                                if (user_type == 'T' && typeof level !== 'undefined' && typeof subject !== 'undefined' && level != '' && subject != '' && level == tot[i].Level.rank && subject == tot[i].Tutorprofile.subject_id) {
                                    pinType = 'matchRed';
                                    map_img = 'red_MarkerT.png';

                                    if (is_agency == "yes") {
                                        map_img = 'agent_marker_red.png';
                                        // map_img = 'agency_marker.png';
                                        // pinType = 'matchAgentRed';
                                        pinType = 'asset';
                                    }
                                    profile_sign = '<img src="images/T_red.png" >';
                                    isredTr = true;
                                } else if (user_type == 'T' && typeof subject !== 'undefined' && subject != '' && subject == tot[i].Tutorprofile.subject_id) {
                                    map_img = 'pink_MarkerT.png';

                                    pinType = 'matchPink';
                                    if (is_agency == "yes") {
                                        map_img = 'agent_marker_pink.png';
                                        // map_img = 'agency_marker.png';
                                        // pinType = 'matchAgentPink';
                                        pinType = 'asset';
                                    }
                                    profile_sign = '<img src="images/T_pink.png" >';
                                    ispinkTr = true;
                                } else if (user_type == 'T') {
                                    pinType = 'tutor';
                                    map_img = _getMatchIcon(match_profileT, 'T', is_agency);
                                    profile_sign = '<img src="images/T_green.png" >';
                                    isgreenTr = true;
                                } else if (user_type == 'S' && typeof level !== 'undefined' && typeof subject !== 'undefined' && level != '' && subject != '' && level == tot[i].Level.rank && subject == tot[i].Studentprofile.subject_id) {
                                    map_img = 'red_MarkerS.png';

                                    pinType = 'matchRed';
                                    if (is_agency == "yes") {
                                        map_img = 'agent_marker_red.png';
                                        // map_img = 'agency_marker.png';
                                        // pinType = 'matchAgentRed';
                                        pinType = 'asset';
                                    }
                                    profile_sign = '<img src="images/S_red.png" >';
                                    isredTr = true;
                                } else if (user_type == 'S' && typeof subject !== 'undefined' && subject != '' && subject == tot[i].Studentprofile.subject_id) {
                                    map_img = 'pink_MarkerS.png';

                                    pinType = 'matchPink';
                                    if (is_agency == "yes") {
                                        map_img = 'agent_marker_pink.png';
                                        // map_img = 'agency_marker.png';
                                        pinType = 'matchAgentPink';
                                        pinType = 'asset';

                                    }
                                    ispinkTr = true;
                                    profile_sign = '<img src="images/S_pink.png" >';
                                } else if (user_type == 'S') {
                                    pinType = 'student';
                                    map_img = _getMatchIcon(match_profileS, 'S', is_agency);
                                    profile_sign = '<img src="images/S_green.png" >';
                                    isgreenTr = true;
                                }
                            } else {
                                if (user_type == 'S') {
                                    map_img = 'green_MarkerS.png';
                                    if (is_agency == "yes") {
                                        map_img = 'agency_marker.png';
                                    }
                                    pinType = 'student';
                                    isgreenTr = true;
                                } else if (user_type == 'T') {
                                    map_img = 'green_MarkerT.png';
                                    if (is_agency == "yes") {
                                        map_img = 'agency_marker.png';
                                    }
                                    pinType = 'tutor';
                                    isgreenTr = true;
                                }
                            }
                        } else if (is_asset_id > 0) {
                            pinType = 'asset';
                            if (user_type == 'T' && typeof level !== 'undefined' && typeof subject !== 'undefined' && level != '' && subject != '' && level == tot[i].Level.rank && subject == tot[i].Course.subject_id) {
                                pinType = 'matchRed';
                                map_img = 'red_MarkerT.png';

                                if (is_agency == "yes") {
                                    map_img = 'agent_marker_red.png';
                                    // map_img = 'agency_marker.png';
                                    pinType = 'asset';
                                    // pinType = 'matchAgentRed';
                                }
                                profile_sign = '<img src="images/T_pink.png" >';
                                ispinkTr = true;
                            } else if (user_type == 'T') {
                                pinType = 'tutor';
                                map_img = _getMatchIcon(match_profileT, 'T', is_agency);
                                profile_sign = '<img src="images/T_green.png" >';
                                isgreenTr = true;
                            } else if (user_type == 'S' && typeof level !== 'undefined' && typeof subject !== 'undefined' && level != '' && subject != '' && level == tot[i].Level.rank && subject == tot[i].Studentprofile.subject_id) {
                                map_img = 'red_MarkerS.png';

                                pinType = 'matchRed';
                                if (is_agency == "yes") {
                                    map_img = 'agent_marker_red.png';
                                    // map_img = 'agency_marker.png';
                                    // pinType = 'matchAgentRed';
                                    pinType = 'asset';
                                }
                                profile_sign = '<img src="images/S_red.png" >';
                                isredTr = true;
                            } else if (user_type == 'S' && typeof subject !== 'undefined' && subject != '' && subject == tot[i].Studentprofile.subject_id) {
                                map_img = 'pink_MarkerS.png';

                                pinType = 'matchPink';
                                if (is_agency == "yes") {
                                    map_img = 'agent_marker_pink.png';
                                    // map_img = 'agency_marker.png';
                                    // pinType = 'matchAgentPink';
                                    pinType = 'asset';

                                }
                                ispinkTr = true;
                                profile_sign = '<img src="images/S_pink.png" >';
                            } else if (user_type == 'S') {
                                pinType = 'student';
                                map_img = _getMatchIcon(match_profileS, 'S', is_agency);
                                profile_sign = '<img src="images/S_green.png" >';
                                isgreenTr = true;
                            }
                        } else {
                            if (user_type == 'S') {
                                map_img = 'green_MarkerS.png';
                                if (is_agency == "yes") {
                                    map_img = 'agency_marker.png';
                                }
                                pinType = 'student';
                                isgreenTr = true;
                            } else if (user_type == 'T') {
                                map_img = 'green_MarkerT.png';
                                if (is_agency == "yes") {
                                    map_img = 'agency_marker.png';
                                }
                                pinType = 'tutor';
                                isgreenTr = true;
                            }
                        }

                        var str = "";
                        var tiarr = new Array();
                        var totRc = 0;
                        /* if (tutor.length>0) */
                        if (latitude != '' && longitude != '' && (user_type == 'S' || (user_type == 'T'))) {

                            tot_res_pin++;
                            // alert((distance * 1.60934).toFixed(2));
                            dis2[i] = { 'dis': distance, 'lat': latitude, 'lng': longitude };
                            if (isredTr) {
                                if (is_agency == "yes") {
                                    if (haveCourse == true) {
                                        aredTr += '<tr class="jump_pin" data-lat="' + latitude + '"  data-lng="' + longitude + '" >' +
                                            '<td class="bdr_right_none"><a href="javascript:void(0);" id="gh890" onclick="myclick1(event,' + cntmmk + ')"  >' + asset_name + '  </a> </td>' +
                                            '<td class="bdr_left_none wid_10">' + profile_sign + '  </td>' +
                                            '<td style="text-align: center !important;">' + (distance * 1.60934).toFixed(2) + '</td>' +
                                            '<td style="text-align: center !important;">' + persession + '</td>' +
                                            '<td style="text-align: center !important;">' + perMonth + '</td>' +
                                            '<td style="text-align: center !important;">' + terms + '</td>' +
                                            '</tr>';
                                        afullCount++;
                                    }
                                } else {
                                    redTr += '<tr class="jump_pin" data-lat="' + latitude + '"  data-lng="' + longitude + '" >' +
                                        '<td class="bdr_right_none"><a href="javascript:void(0);" id="gh890" onclick="myclick1(event,' + cntmmk + ')"  >' + name + '  </a> </td><td class="bdr_left_none wid_10">' + profile_sign + '  </td>' +
                                        '<td style="text-align: center !important;">' + (distance * 1.60934).toFixed(2) + '</td>' +
                                        '<td style="text-align: center !important;">' + persession + '</td>' +
                                        '<td style="text-align: center !important;">' + perMonth + '</td>' +
                                        '</tr>';
                                    fullCount++;
                                }
                            } else if (ispinkTr) {
                                if (is_agency == "yes") {
                                    if (haveCourse == true) {
                                        apinkTr += '<tr class="jump_pin" data-lat="' + latitude + '"  data-lng="' + longitude + '" >' +
                                            '<td class="bdr_right_none"><a href="javascript:void(0);" id="gh890" onclick="myclick1(event,' + cntmmk + ')"  >' + asset_name + '  </a> </td><td class="bdr_left_none wid_10">' + profile_sign + '  </td>' +
                                            '<td style="text-align: center !important;">' + (distance * 1.60934).toFixed(2) + '</td>' +
                                            '<td style="text-align: center !important;">' + persession + '</td>' +
                                            '<td style="text-align: center !important;">' + perMonth + '</td>' +
                                            '<td style="text-align: center !important;">' + terms + '</td>' +
                                            '</tr>';
                                        apartialCount++;
                                    }
                                } else {
                                    pinkTr += '<tr class="jump_pin" data-lat="' + latitude + '"  data-lng="' + longitude + '" >' +
                                        '<td class="bdr_right_none"><a href="javascript:void(0);" id="gh890" onclick="myclick1(event,' + cntmmk + ')"  >' + name + '  </a> </td><td class="bdr_left_none wid_10">' + profile_sign + '  </td>' +
                                        '<td style="text-align: center !important;">' + (distance * 1.60934).toFixed(2) + '</td>' +
                                        '<td style="text-align: center !important;">' + persession + '</td>' +
                                        '<td style="text-align: center !important;">' + perMonth + '</td>' +
                                        '</tr>';
                                    partialCount++;
                                }
                            } else {
                                if (is_agency == "yes") {
                                    if (haveCourse == true) {
                                        agreenTr += '<tr class="jump_pin" data-lat="' + latitude + '"  data-lng="' + longitude + '" >' +
                                            '<td class="bdr_right_none"><a href="javascript:void(0);" id="gh890" onclick="myclick1(event,' + cntmmk + ')"  >' + asset_name + '  </a> </td><td class="bdr_left_none wid_10">' + profile_sign + '  </td>' +
                                            '<td style="text-align: center !important;">' + (distance * 1.60934).toFixed(2) + '</td>' +
                                            '<td style="text-align: center !important;">' + persession + '</td>' +
                                            '<td style="text-align: center !important;">' + perMonth + '</td>' +
                                            '<td style="text-align: center !important;">' + terms + '</td>' +
                                            '</tr>';
                                        agreenCount++;
                                    }

                                } else {
                                    greenTr += '<tr class="jump_pin" data-lat="' + latitude + '"  data-lng="' + longitude + '" >' +
                                        '<td class="bdr_right_none"><a href="javascript:void(0);" id="gh890" onclick="myclick1(event,' + cntmmk + ')"  >' + name + '  </a> </td><td class="bdr_left_none wid_10">' + profile_sign + '  </td>' +
                                        '<td style="text-align: center !important;">' + (distance * 1.60934).toFixed(2) + '</td>' +
                                        '<td style="text-align: center !important;">' + persession + '</td>' +
                                        '<td style="text-align: center !important;">' + perMonth + '</td>' +
                                        '</tr>';
                                    greenCount++;
                                }
                            }

                            qual = 'test';
                            if (is_agency == "yes") {
                                // if (haveCourse == true) {
                                    newHtml += that._resultTemplate(tot[i], cntmmk, pinType);
                                    var mk = mapmarkers(latitude, longitude, "images/" + map_img, name, qual, distance, address, image, 'list', userid, user_type, pinType, map_img, is_asset_id);
                                    if (first_lat == 0 && first_lng == 0 && show_res_box == 1) {
                                        first_lat = latitude;
                                        first_lng = longitude;
                                        var n_latlng = new google.maps.LatLng(first_lat, first_lng);
                                        var subjectPoint2 = {
                                            point: n_latlng,
                                            radius: parseInt(rad), //default radius
                                            color: '#00AA00'
                                        }
                                        var subjectMarker2 = new google.maps.Marker({
                                            position: subjectPoint2.point,
                                            title: 'Subject'

                                        });
                                        map.setCenter(n_latlng);
                                        subjectRange.bindTo('center', subjectMarker2, 'position');
                                    }
                                    tiarr.push(mk);
                                // }
                            } else {

                                newHtml += that._resultTemplate(tot[i], cntmmk, pinType);
                                var mk = mapmarkers(latitude, longitude, "images/" + map_img, name, qual, distance, address, image, 'list', userid, user_type, pinType, map_img, is_asset_id);

                                if (first_lat == 0 && first_lng == 0 && show_res_box == 1) {
                                    first_lat = latitude;
                                    first_lng = longitude;
                                    var n_latlng = new google.maps.LatLng(first_lat, first_lng);
                                    var subjectPoint2 = {
                                        point: n_latlng,
                                        radius: parseInt(rad), //default radius
                                        color: '#00AA00'
                                    }
                                    var subjectMarker2 = new google.maps.Marker({
                                        position: subjectPoint2.point,
                                        title: 'Subject'

                                    });
                                    map.setCenter(n_latlng);
                                    subjectRange.bindTo('center', subjectMarker2, 'position');
                                }
                                tiarr.push(mk);
                            }



                            /* str +="</td></tr>";  */


                        }

                        ggmarkers[cntmmk] = tiarr;
                        cntmmk = cntmmk + 1;

                    }
                    that.hideLoder();
                    if (redTr != '') {
                        redTr = predTr + '' + redTr;
                    }
                    if (pinkTr != '') {
                        pinkTr = ppinkTr + '' + pinkTr;
                    }

                    if (aredTr != '') {
                        aredTr = apredTr + '' + aredTr;
                    }
                    if (apinkTr != '') {
                        apinkTr = appinkTr + '' + apinkTr;
                    }

                    table += redTr + '' + pinkTr + '' + greenTr;
                    atable += aredTr + '' + apinkTr + '' + agreenTr;

                    if ((eval(fullCount) + eval(partialCount) + eval(greenCount)) == 0) {
                        first_lat = 0;
                        first_lng = 0;
                        table += '<tr><td colspan="5">No Result Found.</td></tr>';
                    }
                    if ((eval(afullCount) + eval(apartialCount) + eval(agreenCount)) == 0) {
                        atable += '<tr><td colspan="6">No Result Found.</td></tr>';
                    }
                    table += '</table>';
                    atable += '</table>';

                    $('.result-list').html(newHtml);
                    $('#search,#search2').val('Find');
                    $('#GO,#GO2').val('Go');
                    $('#search,#search2, #GO,#GO2').removeAttr('disabled');
                    /* alert(tot_res_pin); */
                    var totRes = tot.length;
                    if (typeof totRes == "undefined") {
                        totRes = 0;
                    }

                    function OpenWindowWithPost(url, windowoption, name, params) {
                        var form = document.createElement("form");
                        form.setAttribute("method", "post");
                        form.setAttribute("action", url);
                        form.setAttribute("target", name);
                        // console.log(params);
                        for (var i in params) {
                            if (params.hasOwnProperty(i)) {
                                var input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = i;
                                input.value = params[i];
                                form.appendChild(input);
                            }
                        }

                        document.body.appendChild(form);

                        //note I am using a post.htm page since I did not want to make double request to the page 
                        //it might have some Page_Load call which might screw things up.
                        window.open("searches/expanded_view", name, windowoption);

                        form.submit();

                        document.body.removeChild(form);
                    }

                    $(document).on('click', '.showExpandView', function(event) {
                        event.preventDefault();
                        // var param = { 'uid' : '1234'};
                        var width = screen.width;
                        var height = screen.height - 100;
                        var left = (screen.width - width) / 2;
                        var top = (screen.height - height) / 2;

                        OpenWindowWithPost("searches/expanded_view",
                            "width=1024,height=600,left=150,top=50,fullscreen=no.resizable=no,scrollbars=yes",
                            "NewFile", param);
                        /* Act on the event */
                    });
                    /*
                     $('.listing > h4').html('Showing ' + (eval(fullCount) + eval(partialCount) + eval(greenCount)) + ' <span id="resultsLabel">' + ((profile_type == 'T') ? 'Trainers' : 'Learners') + '</span> available');
                    */


                    that.set404Image();
                }
            });
        }

        $('#CountryRadius').on('keypress', function(e) {
            if (e.keyCode == 13) {
                $(this).blur();
                e.preventDefault();
            }
        });

        $('#CountryRadius').on('blur', function() {

            if ($("#remradius").is(':checked')) {
                zoomval = setzoom();
                // initialize(zoomval);
                map.setZoom(zoomval);
                subjectRange.setRadius(parseInt(0));
            } else {
                zoomval = that.setzoom();
                //initialize(zoomval);
                map.setZoom(zoomval);
                subjectRange.setRadius(parseInt($(this).val()));
            }
        });

        $('#remradius').click(function() {
            if ($(this).is(':checked')) {
                subjectRange.setRadius(parseInt(0));
                $('#div_remradius').attr('title', 'Show Circle');
            } else {
                zoomval = setzoom();
                subjectRange.setRadius(parseInt($("#CountryRadius").val()));
                map.setZoom(zoomval);
                $('#div_remradius').attr('title', 'Hide Circle');
            }
        });
        /* show_res_box = 0; */

        google.maps.event.addListenerOnce(map, 'idle', function() {



            if (nLat != "" && nLang != "" && (searchClick != 'clicked' && resetClick != "clicked") && countryChange != "change") {
                // initialLocation = new google.maps.LatLng(nLat, nLang);
                // map.setCenter(initialLocation);
            }
            setTimeout(function() {

                /*if (is_login != "") {
                    touraftrLogin.init();
                    touraftrLogin.start();

                } else {
                    tourbForLogin.init();
                    tourbForLogin.start();
                }*/




            }, 1000);

        });
        google.maps.event.addListener(map, 'zoom_changed', function(event) {

            // console.log(prevZoomLvl +' > '+map.getZoom());
            if (prevZoomLvl > map.getZoom()) {
                zoomChange = true;
                // gettutorList();
            }

            prevZoomLvl = map.getZoom();
        });
        var oldCenter;
        google.maps.event.addListener(map, 'dragstart', function(event) {
            oldCenter = map.getCenter();
        });
        google.maps.event.addListener(map, 'dragend', function(event) {

            map.set('dragging', false);
            // /that.get('oldCenter')!==that.getCenter()
            function distance(lat1, lon1, lat2, lon2, unit) {
                var radlat1 = Math.PI * lat1 / 180
                var radlat2 = Math.PI * lat2 / 180
                var theta = lon1 - lon2
                var radtheta = Math.PI * theta / 180
                var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                dist = Math.acos(dist)
                dist = dist * 180 / Math.PI
                dist = dist * 60 * 1.1515
                if (unit == "K") { dist = dist * 1.609344 }
                if (unit == "N") { dist = dist * 0.8684 }
                return dist
            }
            var newCenter = map.getCenter();

            if (typeof oldCenter == 'undefined') {
                oldCenter = map.getCenter();
            }
            console.log(oldCenter.lat() + ", " + oldCenter.lng() + ' == ' + newCenter.lat() + ", " + newCenter.lng());

            var draggedDistance = distance(oldCenter.lat(), oldCenter.lng(), newCenter.lat(), newCenter.lng(), 'K');

            var metersPerPixel = Math.cos(newCenter.lat() * Math.PI / 180) * 2 * Math.PI * 6378137 / (256 * Math.pow(2, map.getZoom()));
            console.log(' zoom in meter ==> ' + (metersPerPixel / 10));
            oldCenter = map.getCenter();
            if (draggedDistance > (metersPerPixel / 10)) {
                isMapDragged = true;
            }
        });

        google.maps.event.addDomListener(map, 'tilesloaded', function() {
            if ($('#newPos').length == 0) {
                $('div.gmnoprint.gm-style-cc').next('div').next('div').wrap('<div id="newPos" />');
            }
        });


    },
    setzoom: function() {
        rad = $("#CountryRadius").val();
        if (rad >= 0 && rad <= 17) {
            return 20;

        } else if (rad >= 18 && rad <= 35) {
            return 19;
        } else if (rad >= 36 && rad <= 69) {
            return 18;
        } else if (rad >= 70 && rad <= 99) {
            return 17;
        } else if (rad >= 100 && rad <= 280) {
            return 16;
        } else if (rad >= 281 && rad <= 399) {
            return 15;
        } else if (rad >= 400 && rad <= 560) {
            return 15;
        } else if (rad >= 561 && rad <= 1100) {
            return 14;
        } else if (rad >= 1101 && rad <= 2200) {
            return 13;
        } else if (rad >= 2201 && rad <= 4500) {
            return 12;
        } else if (rad >= 4501 && rad <= 9000) {
            return 11;
        } else if (rad >= 9001 && rad <= 16000) {
            return 10;
        } else if (rad >= 16001 && rad <= 35000) {
            return 9;
        } else if (rad >= 35001 && rad <= 70000) {
            return 8;
        } else if (rad >= 70001 && rad <= 150000) {
            return 7;
        } else if (rad >= 150001 && rad <= 280000) {
            return 6;
        } else {
            return 5;
        }
    },
    getAvgRating: function(tutorRating) {
        var sum = 0;
        for (var i = 0; i < tutorRating.length; i++) {
            sum += parseFloat(tutorRating[i].rate); //don't forget to add the base
        }
        var avg = sum / tutorRating.length;
        return avg;
    },
    _resultTemplate: function(responseData, cntmmk, pinType) {

        //  var template = '<script type="text/javascript">new SimpleStarRating(document.getElementById("'+responseData.User.slug_url+'"));</script>';

        var profileURL =
            (responseData.User.is_agency == "no") ?
            this.base_url() + responseData.User.slug_url :
            this.base_url() + responseData.User.slug_url + '?assetid=' + responseData.Asset.id_asset;
        var userName = (((responseData.User.is_agency == "no") ? responseData.User.name : responseData.Asset.display_name_1 + '' + responseData.Asset.display_name_2));
        var template = '';
        if (responseData.User.user_type == 'T' && responseData.User.is_agency == "no") {
            // template = '<script type="text/javascript">new SimpleStarRating(document.getElementById("' + responseData.User.slug_url + '"));</script>';
        }

        template += '<div class="listing-card">';
        template += '<div class="listing-header">';
        template += '<div class="image">';
        //template += '<a href="' + profileURL + '" target="_blank"><img src="' + this.getAwsImageUrl(((responseData.User.is_agency == "no") ? responseData.User.image : responseData.Asset.image)) + '"  onError="this.src=\'images/tueeter_signin-logo.png\'"> </a>';
        if (responseData.User.is_agency == "no") {
            template += '<a href="' + profileURL + '" target="_blank"><img src="' + this.getAwsImageUrl(responseData.User.image) + '"  onError="this.src=\'images/tueeter_signin-logo.png\'"> </a>';
        }
        else {
            template += '<a href="' + profileURL + '" target="_blank"><img style="border-radius: 0%;" src="' + this.getAwsImageUrl(responseData.Asset.image) + '"  onError="this.src=\'images/tueeter_signin-logo.png\'"> </a>';
        }
        template += '</div>';
        template += '<div class="details">';
        template += '<a class="title" href="' + profileURL + '" target="_blank">' + userName + '</a>';
        template += '<div class="response-time">';
        template += 'Response time: ' + responseData.ResponseTime.counter;
        template += '</div>';

        if (responseData.TutorRating) {
            if (responseData.TutorRating.length > 0) {
                //  template += that.getAvgRating(responseData.TutorRating) + '/5 Stars'
                var sum = 0;
                var count_review = 1;
                for (var i = 0; i < responseData.TutorRating.length; i++) {
                    sum += parseFloat(responseData.TutorRating[i].rate); //don't forget to add the base
                    if (responseData.TutorRating[i].subject != 'Starter Rating') {
                        count_review++;
                    }
                }
                var avg = sum / count_review;

                // template += avg
                if (responseData.User.user_type == 'T' && responseData.User.is_agency == "no") {
                    template += '<div id="' + responseData.User.slug_url + '" class="rating" data-stars="5" data-default-rating="' + avg + '" disabled=""></div>';
                    template += '<script type="text/javascript">new SimpleStarRating(document.getElementById("' + responseData.User.slug_url + '"));</script>';
                }
                //  console.log(template);

            } else {
                template += ' unrated';
            }
        }

        template += '<div class="buttons">';
        template += '<a class="primary-btn" href="javascript:void(0)" onclick="sendMessage(this,' + responseData.User.id + ',\'' + responseData.User.user_type + '\')" >Message</a>';
        template += '<a class="cta-btn secondary-btn" href="javascript:void(0)" onclick="that.makefavorite(' + responseData.User.id + ', \'' + responseData.User.user_type + '\') ' + '" >Shortlist</a>';
        template += '</div>';
        template += '</div>';
        var iconClass = 'green';
        if (responseData.User.user_type == 'T') {
            iconClass = 'blue';
        }

        template += '<div class="actions">';
        template += '<a class="map-link ' + iconClass + '" onclick="myclick1(event,' + cntmmk + ')">';
        template += '<span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>';
        template += '</a>';
        template += '</div>';
        template += '</div>';
        template += '<div class="listing-table">';

        if (responseData.User.user_type == "T" && responseData.User.is_agency == 'no') {
            if ((responseData.Tutorprofile_details).length > 0) {

                $(responseData.Tutorprofile_details).each(function(key, value) {
                    template += '<div class="listing">';
                    template += '<div class="title">';
                    template += '<span>' + value.Subject.name + '</span>';
                    template += '<span class="distance">' + (responseData.Useraddress.distance * 1.60934).toFixed(2) + ' km from you</span>';
                    template += '</div>';
                    template += '<div class="subtitle">';
                    template += '<span class="level">' + value.Level.name + '</span>';
                    template += '</div>';
                    template += '<div class="price-section">';
                    template += '<div class="section">';
                    template += '<span class="label">Per Session</span>';
                    template += '<span class="price">' + currency_symbol + ' ' + ((value.Tutorprofile.pricepersession == null) ? 'N/A' : value.Tutorprofile.pricepersession) + '</span>';
                    template += '</div>';
                    template += '<div class="section">';
                    template += '<span class="label">Per Month</span>';
                    template += '<span class="price">' + currency_symbol + ' ' + ((value.Tutorprofile.pricepermonth == null) ? 'N/A' : value.Tutorprofile.pricepermonth) + '</span>';
                    template += '</div>';
                    template += '</div>';
                    template += '</div>';

                });
            } else {
                template += '';
            }
        }
        if (responseData.User.is_agency == 'yes') {

            if ((responseData.Course_details).length > 0) {
                $(responseData.Course_details).each(function(key, value) {
                    template += '<div class="listing">';
                    template += '<div class="title">';
                    template += '<span>' + value.course_name + '</span>';
                    template += '<span class="distance">' + (responseData.Useraddress.distance * 1.60934).toFixed(2) + ' km from you</span>';
                    template += '</div>';
                    template += '<div class="subtitle">';
                    template += '<span class="level">' + responseData.Level.name + '</span>';
                    template += '</div>';
                    template += '<div class="price-section">';
                    template += '<div class="section">'
                    template += '<span class="label">Per Session</span>'
                    template += '<span class="price">' + currency_symbol + ' ' + ((value.pricepersession == null) ? 'N/A' : value.pricepersession) + '</span>'
                    template += '</div>'
                    template += '<div class="section">'
                    template += '<span class="label">Per Month</span>'
                    template += '<span class="price">' + currency_symbol + ' ' + ((value.pricepermonth == null) ? 'N/A' : value.pricepermonth) + '</span>'
                    template += '</div>'
                    template += '</div>'
                    template += '</div>'
                });
            } else {
                template += ''
            }
        }
        if (responseData.User.user_type == "S") {
            if ((responseData.Studentprofile_details).length > 0) {
                $(responseData.Studentprofile_details).each(function(key, value) {
                    template += '<div class="listing">'
                    template += '<div class="title">'
                    template += '<span>' + value.Subject.name + '</span>'
                    template += '<span class="distance">' + (responseData.Useraddress.distance * 1.60934).toFixed(2) + ' km from you</span>'
                    template += '</div>'
                    template += '<div class="subtitle">'
                    template += '<span class="level">' + value.Level.name + '</span>'
                    template += '</div>'
                    template += '<div class="price-section">'
                    template += '<div class="section">'
                    template += '<span class="label">Per Session</span>'
                    template += '<span class="price">' + currency_symbol + ' ' + ((value.Studentprofile.pricepersession == null) ? 'N/A' : value.Studentprofile.pricepersession) + '</span>'
                    template += '</div>'
                    template += '<div class="section">'
                    template += '<span class="label">Per Month</span>'
                    template += '<span class="price">' + currency_symbol + ' ' + ((value.Studentprofile.pricepermonth == null) ? 'N/A' : value.Studentprofile.pricepermonth) + '</span>'
                    template += '</div>'
                    template += '</div>'
                    template += '</div>'
                });
            } else {
                template += ''
            }
        }


        template += '</div>'
        template += '<div class="listing-buttons">'
        template += '<a href="' + profileURL + '" target="_blank">View Profile</a>'
        template += '</div>'
        template += '</div>'
        template += '</div>';
        return template;
    },
    set404Image: function() {

        /*$('img').error(function() {

            var ext = $(this).attr('src').filename('exetnsion');
            var nFileName = $(this).attr('src').filename()

            if (nFileName) {
                nFileName = nFileName + '.' + ext.toUpperCase();
                $(this).attr('src', nFileName);
            }
        });*/

        // $('img').error(function() {
        //     $(this).attr('src', 'images/tueeter_signin-logo.png');
        // });
    },
    makefavorite: function(uID, userType) {
        if (is_user_loggedin == null || is_user_loggedin == "") {
            $('#loginModal').modal('show');
        } 
        else {
            var isRcActiveUser = (is_user_loggedin.reach_program == 'yes' && is_user_loggedin.delete_from_rc == 'no' && is_user_loggedin.rc_status == 'active');
            
            if (!isRcActiveUser && is_user_loggedin.user_type == userType) {
                var user_type = (userType == 'S') ? 'tutors' : 'students';
                var alertMsg = "This option is available to registered " + user_type + " only";

                $.alert({
                    title: 'Tueetor',
                    content: alertMsg,
                    type: 'red',
                    icon: 'fa fa-exclamation-triangle',
                    animation: 'top',
                    closeAnimation: 'top',
                    buttons: {
                        okay: {
                            text: 'Ok',
                            btnClass: 'btn-green'
                        }
                    }
                });
            } 
            else {
                var favUrl = window.sgmap.base_url() + 'users/favorite/' + uID;

                $.fancybox.open({
                    'padding': 0,
                    'height': 380,
                    'width': 500,
                    'href': favUrl,
                    'type': 'iframe',
                    'fitToView': false,
                    'autoSize': false
                });
            }
        }
    }

}


function myclick1(th, idx) {
    th.preventDefault();
    pinClk = false;
    fNowClk = true;
    var curlist = ggmarkers[idx];

    for (k in curlist) {
        var cmark = curlist[k];

        google.maps.event.trigger(cmark, "click");

    }

}

function getresult(pageNumber) {
    resetClick = '';
    var pagination_pageNumber = pageNumber;

    if (pagination_pageNumber == 1) {
        document.getElementById("pagi_1").className = "link";
    } else {
        var remove_previous = pagination_pageNumber - 1;
        if (document.getElementById("pagi_" + remove_previous)) {
            document.getElementById("pagi_" + remove_previous).className = "link";
        }

    }

    var url = window.location.href;

    if (url.indexOf('?') > -1) {
        
        window.location.href = updateURLParameter(url, 'page', pagination_pageNumber);

    } else {
        url += '?subject=' + subject + '&page=' + pagination_pageNumber + '&profile_type=' + profile_type
        window.location.href = url;
    }

    document.getElementById("pagi_" + pagination_pageNumber).className = "link current";


    that.showLoder();

}

function updateURLParameter(url, param, paramVal) {

    var TheAnchor = null;
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";

    if (additionalURL) {
        var tmpAnchor = additionalURL.split("#");
        var TheParams = tmpAnchor[0];
        TheAnchor = tmpAnchor[1];
        if (TheAnchor)
            additionalURL = TheParams;

        tempArray = additionalURL.split("&");

        for (var i = 0; i < tempArray.length; i++) {
            if (tempArray[i].split('=')[0] != param) {
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    } else {
        var tmpAnchor = baseURL.split("#");
        var TheParams = tmpAnchor[0];
        TheAnchor = tmpAnchor[1];

        if (TheParams)
            baseURL = TheParams;
    }

    if (TheAnchor)
        paramVal += "#" + TheAnchor;

    var rows_txt = temp + "" + param + "=" + paramVal;


    return baseURL + "?" + newAdditionalURL + rows_txt;
}