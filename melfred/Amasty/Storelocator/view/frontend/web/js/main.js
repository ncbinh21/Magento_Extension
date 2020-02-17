define([
    "jquery",
    "jquery/ui"
], function ($) {

    $.widget('mage.amLocator', {
        options: {},
        url: null,
        useGeo: null,
        imageLocations: null,
        filterAttributeUrl: null,
        
        _create: function () {
            this.url = this.options.ajaxCallUrl;
            this.filterAttributeUrl = this.options.filterAttributeUrl;
            this.useGeo = this.options.useGeo;
            this.imageLocations = this.options.imageLocations;
            this.Amastyload();
            var self = this;
            $('#locateNearBy').click(function(){
                self.navigateMe()
            });
            $('#sortByFilter').click(function(){
                self.sortByFilter()
            });

            $('#filterAttribute').click(function(){
                self.filterByAttribute()
            });
            $("[name='leftLocation']").click(function(){
                var id =  $(this).attr('data-amid');
                self.gotoPoint(id, this);
            });

            if ( (navigator.geolocation) && (this.useGeo == 1) ) {
                navigator.geolocation.getCurrentPosition( function(position) {
                    document.getElementById("am_lat").value = position.coords.latitude;
                    document.getElementById("am_lng").value = position.coords.longitude;
                }, this.navigateFail );
            }

            $( ".today_schedule" ).click(function(event) {
                $(this).next( ".all_schedule" ).toggle( "slow", function() {
                    // Animation complete.
                });
                $(this).find( ".locator_arrow" ).toggleClass("arrow_active");
                event.stopPropagation();
            });
        },

        goHome: function(){
            window.location.href = window.location.pathname;
        },

        navigateMe: function(){

            if ( (navigator.geolocation) && (this.useGeo==1) ) {
                var self = this;
                navigator.geolocation.getCurrentPosition( function(position) {
                    self.makeAjaxCall(position);
                }, this.navigateFail );
            }else{
                this.makeAjaxCall();
            }
        },

        navigateFail: function(error){

        },

        getQueryVariable: function(variable) {
            var query = window.location.search.substring(1);
            var vars = query.split("&");
            for (var i=0;i<vars.length;i++) {
                var pair = vars[i].split("=");
                if (pair[0] == variable) {
                    return pair[1];
                }
            }
        },

        makeAjaxCall: function(position) {
            var self = this;
            if ( (position != "") && (typeof position!=="undefined")){

                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                $.ajax({
                    url     : this.url,
                    type    : 'POST',
                    data: {"lat": lat, "lng": lng},
                    showLoader: true
                }).done($.proxy(function(response) {
                    response = JSON.parse(response);
                    $("#amlocator_left").replaceWith(response.block);
                    self.options.jsonLocations = response;
                    self.Amastyload(response);
                    self.gotoPoint(1, this);
                    $("[name='leftLocation']").click(function(){
                        var id =  $(this).attr('data-amid');
                        self.gotoPoint(id, this);
                    });
                }));
            }else{
                $.ajax({
                    url     : this.url,
                    type    : 'POST',
                    showLoader: true,
                    data: {"sort":"distance", "lat": lat, "lng": lng},
                }).done($.proxy(function(response) {
                    response = JSON.parse(response);
                    $("#amlocator_left").replaceWith(response.block);
                    self.options.jsonLocations = response;
                    self.Amastyload(response);
                    self.gotoPoint(1, this);
                    $("[name='leftLocation']").click(function(){
                        var id =  $(this).attr('data-amid');
                        self.gotoPoint(id, this);
                    });
                }));
            }

        },

        Amastyload: function() {
            this.initializeMap();
            this.processLocation(this.options.jsonLocations);

            var markerCluster = new MarkerClusterer(this.map, this.marker, {imagePath: this.imageLocations+'/m'});

            this.geocoder = new google.maps.Geocoder();

            var address = document.getElementById('amlocator-search');
            var autocomplete = new google.maps.places.Autocomplete(address);
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();
                document.getElementById("am_lat").value = place.geometry.location.lat();
                document.getElementById("am_lng").value = place.geometry.location.lng();
            });
        },

        sortByFilter: function() {

            var e = document.getElementById("amlocator-radius");
            var radius = e.options[e.selectedIndex].value;
            var lat = document.getElementById("am_lat").value;
            var lng = document.getElementById("am_lng").value;
            if (!lat || !lng) {
                alert('Please fill Current Location field');
                return false;
            }
            var self = this;

            $.ajax({
                url     : this.url,
                type    : 'POST',
                data: {"lat": lat, "lng": lng, "radius": radius},
                showLoader: true
            }).done($.proxy(function(response) {
                response = JSON.parse(response);
                $("#amlocator_left").replaceWith(response.block);
                self.options.jsonLocations = response;
                self.Amastyload(response);
                $("[name='leftLocation']").click(function(){
                    var id =  $(this).attr('data-amid');
                    self.gotoPoint(id, this);
                });
                $( ".today_schedule" ).click(function(event) {
                    $(this).next( ".all_schedule" ).toggle( "slow", function() {
                        // Animation complete.
                    });
                    $(this).find( ".locator_arrow" ).toggleClass("arrow_active");
                    event.stopPropagation();
                });
            }));

        },

        filterByAttribute: function(){

            var form = $("#attribute-form").serialize();

            var self = this;

            $.ajax({
                url     : this.filterAttributeUrl,
                type    : 'POST',
                data: {"attributes": form},
                showLoader: true
            }).done($.proxy(function(response) {
                response = JSON.parse(response);
                $("#amlocator_left").replaceWith(response.block);
                self.options.jsonLocations = response;
                self.Amastyload(response);
                $("[name='leftLocation']").click(function(){
                    var id =  $(this).attr('data-amid');
                    self.gotoPoint(id, this);
                });
                $( ".today_schedule" ).click(function(event) {
                    $(this).next( ".all_schedule" ).toggle( "slow", function() {
                        // Animation complete.
                    });
                    $(this).find( ".locator_arrow" ).toggleClass("arrow_active");
                    event.stopPropagation();
                });
            }));

        },

        initializeMap: function() {
            this.infowindow = [];
            this.marker = [];
            var myOptions = {
                zoom: 9,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            this.map = new google.maps.Map(document.getElementById("amlocator-map-canvas"), myOptions);
        },

        processLocation: function(locations) {
            var template = baloonTemplate.baloon; // document.getElementById("amlocator_window_template").innerHTML;
            var curtemplate = "";

            if (typeof locations.totalRecords=="undefined" || locations.totalRecords==0){
                this.map.setCenter(new google.maps.LatLng( document.getElementById("am_lat").value, document.getElementById("am_lng").value ));
                return false;
            }

            for (var i = 0; i < locations.totalRecords; i++) {

                curtemplate = template;
                curtemplate = curtemplate.replace("{{name}}",locations.items[i].name);
                curtemplate = curtemplate.replace("{{country}}",locations.items[i].country);
                curtemplate = curtemplate.replace("{{state}}",locations.items[i].state);
                curtemplate = curtemplate.replace("{{city}}",locations.items[i].city);
                curtemplate = curtemplate.replace("{{description}}",locations.items[i].description);
                curtemplate = curtemplate.replace("{{zip}}",locations.items[i].zip);
                curtemplate = curtemplate.replace("{{address}}",locations.items[i].address);
                curtemplate = curtemplate.replace("{{phone}}",locations.items[i].phone);
                curtemplate = curtemplate.replace("{{email}}",locations.items[i].email);
                curtemplate = curtemplate.replace("{{website}}",locations.items[i].website);
                curtemplate = curtemplate.replace("{{lat}}",locations.items[i].lat);
                curtemplate = curtemplate.replace("{{lng}}",locations.items[i].lng);

                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].name,'name');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].country,'country');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].state,'state');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].city,'city');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].description,'description');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].zip,'zip');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].address,'address');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].phone,'phone');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].email,'email');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].website,'website');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].lat,'lat');
                curtemplate = this.replaceIfStatement(curtemplate, locations.items[i].lng,'lng');

                curtemplate = this.showAttributeInfo(curtemplate, locations.items[i], locations.currentStoreId);

                if (locations.items[i].store_img != "") {
                    curtemplate = curtemplate.replace("{{photo}}",locations.items[i].store_img);
                } else {
                    curtemplate = curtemplate.replace(/<img[^>]*>/g,"");
                }
                if (locations.items[i].marker_img != "") {
                    markerImage = amMediaUrl + locations.items[i].marker_img;
                } else {
                    markerImage = "";
                }
                this.createMarker(locations.items[i].lat, locations.items[i].lng,  curtemplate, markerImage);
            }
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0; i < this.marker.length; i++) {
                bounds.extend(this.marker[i].getPosition());
            }

            this.map.fitBounds(bounds);
            if (locations.totalRecords == 1) {
                google.maps.event.addListenerOnce(this.map, 'bounds_changed', function() {
                    this.setZoom(parseInt(mapZoom));
                })
            }
        },

        showAttributeInfo: function (curtemplate, item, currentStoreId) {
            var attributeTemplate = baloonTemplate.attributeTemplate;
            if (item.attributes) {
                for (var j = 0; j < item.attributes.length; j++) {
                    var label = item.attributes[j].frontend_label;
                    var labels = item.attributes[j].labels;
                    if (labels[currentStoreId]) {
                        label = labels[currentStoreId];
                    }

                    var value = item.attributes[j].value;
                    if (item.attributes[j].boolean_title) {
                        value = item.attributes[j].boolean_title;
                    }
                    if (item.attributes[j].option_title) {
                        var optionTitles = item.attributes[j].option_title;
                        value = '<br>';
                        for (var k = 0; k < optionTitles.length; k++) {
                            value += '- ';
                            if (optionTitles[k][currentStoreId]) {
                                value += optionTitles[k][currentStoreId];
                            } else {
                                value += optionTitles[k][0];
                            }
                            value += '<br>';
                        }
                    }
                    attributeTemplate = attributeTemplate.replace("{{title}}",label);
                    curtemplate += attributeTemplate.replace("{{value}}",value);

                    attributeTemplate = baloonTemplate.attributeTemplate;
                }
            }
            return curtemplate;
        },

        gotoPoint: function(myPoint,element){
            this.closeAllInfoWindows();
            if (typeof element!=="undefined"){
                element.className = element.className + " active";
            }else{
                var spans = document.getElementById('amlocator_left').getElementsByTagName('span');
                if(spans.length > 0) {
                    spans[0].className = spans[0].className + "active";
                }
            }
            this.map.setCenter(new google.maps.LatLng( this.marker[myPoint-1].position.lat(), this.marker[myPoint-1].position.lng()));
            this.map.setZoom(parseInt(mapZoom));
            this.marker[myPoint-1]['infowindow'].open(this.map, this.marker[myPoint-1]);
        },

        replaceIfStatement: function(text,value,template){
            var patt = new RegExp("\{\{if"+template+"\}\}([\\s\\S]*)\{\{\/\if"+template+"}\}","g");
            var cuteText = patt.exec(text);
            if (cuteText!=null ){
                if (value=="" || value==null){
                    text = text.replace(cuteText[0], '');
                }else{
                    var finalText = cuteText[1].replace('{{'+template+'}}', value);
                    text = text.replace(cuteText[0], finalText);
                }

                return text;
            }
            return text;
        },

        createMarker: function(lat, lon, html, marker) {
            var image = marker.split('/').pop();
            if (marker && image != 'null') {
                var marker = {
                    url: marker,
                    size: new google.maps.Size(48, 48),
                    scaledSize: new google.maps.Size(48, 48)
                };
                var newmarker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, lon),
                    map: this.map,
                    icon: marker
                });
            } else {
                var newmarker = new google.maps.Marker({
                    position: new google.maps.LatLng(lat, lon),
                    map: this.map
                });
            }

            newmarker['infowindow'] = new google.maps.InfoWindow({
                content: html
            });
            var self = this;
            google.maps.event.addListener(newmarker, 'click', function() {
                self.closeAllInfoWindows();
                this['infowindow'].open(self.map, this);
                self.map.setZoom(parseInt(mapZoom));
            });

            this.marker.push(newmarker);
        },

        closeAllInfoWindows: function () {

            var spans = document.getElementById('amlocator_left').getElementsByTagName('span');

            for(var i = 0, l = spans.length; i < l; i++){

                spans[i].className = spans[i].className.replace(/\active\b/,'');
            }

            if (typeof this.marker !=="undefined"){
                for (var i=0;i<this.marker.length;i++) {
                    this.marker[i]['infowindow'].close();
                }
            }

        },

    });

    return $.mage.amLocator;
});
