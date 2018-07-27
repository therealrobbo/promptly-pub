var cslMap;
$(document).ready(function(){

    var map_div    = $( '.facs-map' );
    var map_div_id = map_div.attr( 'id' );

    var map_data_url  = map_div.attr( 'data-search');
    var def_lat       = map_div.attr( 'data-lat' );
    var def_lon       = map_div.attr( 'data-lon' );
    var search_radius = map_div.attr( 'data-radius' );
    var lat_long_diff = ( search_radius / 2 ) * .01447;
    var fit_bounds    = map_div.attr( 'data-fit' );
    var autopop       = map_div.attr( 'data-pop' );

    var markers = new Array();

    // Go get the map data points
    $.getJSON( map_data_url, function( data ){

        cslMap = new GMaps({
            div: '#' + map_div_id,
            lat: def_lat,
            lng: def_lon
        });

        var bounds = new google.maps.LatLngBounds();

        var myLatLng;
        if ( ( data.markers != null ) && ( typeof data.markers[0] !== 'undefined') ) {
            for (x in data.markers) {

                var current_marker;
                var store = data.markers[x];
                var storeView = "{{#name}}<h2><a href='/store/{{id}}'>{{{name}}}</a></h2>{{/name}}{{#addr1}}<div class='address1'>{{{addr1}}}</div>{{/addr1}}{{#addr2}}<div class='address2'>{{{addr2}}}</div>{{/addr2}}<div class='city'>{{city}}, {{state}} {{zip}}</div>{{#phone}}<div class='phone'>{{phone}}</div>{{/phone}}";
                var windowContent = Mustache.render(storeView, store);

                myLatLng = new google.maps.LatLng(store.lat, store.lon);

                bounds.extend(myLatLng);
                current_marker = cslMap.addMarker({
                    lat: myLatLng.lat(),
                    lng: myLatLng.lng(),
                    title: store.name,
                    index: store.id,
                    infoWindow: {
                        content: windowContent
                    },
                    click: function(e) {
                        e.infoWindow.open(e.map, e);
                    }
                });
            }
        } else {
            myLatLng = new google.maps.LatLng( def_lat, def_lon );
            bounds.extend(myLatLng);
            myLatLng = new google.maps.LatLng(def_lat - lat_long_diff , def_lon - lat_long_diff);
            bounds.extend(myLatLng);
            myLatLng = new google.maps.LatLng(def_lat + lat_long_diff , def_lon + lat_long_diff);
            bounds.extend(myLatLng);
        }

        cslMap.map.setCenter(bounds.getCenter());

        if ( fit_bounds > 0  ) {
            cslMap.map.fitBounds(bounds);
        }
    });


    function FaCS_trigger( id, doscroll ) {

        doscroll = typeof doscroll !== 'undefined' ? doscroll : true;

        if ( doscroll ) {
            var mappos = $('.content').offset();
            $('html,body').animate({scrollTop: mappos.top });
        }

        var cur_marker;
        var found_marker = -1;
        for ( x in cslMap.markers ) {
            cur_marker = cslMap.markers[x];
            if( cur_marker.index == id ) {
                found_marker = x;
                break;
            }
        }

        if ( found_marker != -1 )
            google.maps.event.trigger(cslMap.markers[found_marker], "click");
    }

    if ( autopop ) {
        setTimeout( function( ) {
            FaCS_trigger(0, false);
        }, 6000);
    }

    $( '.facs-pop-map' ).click( function( e ) {
        var store_id = $(this).attr( 'data-store-id' );
        FaCS_trigger( store_id, true );
    });
});



