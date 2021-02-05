/**
 * Created by Intents Coder on 4/14/2016.
 */

function moo_getLatLong() {
    if(mooDeliveryOptions == null || mooDeliveryOptions.moo_merchantLat == "" || mooDeliveryOptions.moo_merchantLng == "" || mooDeliveryOptions.moo_merchantLat == null || mooDeliveryOptions.moo_merchantLng == null) {
        jQuery.get('https://maps.googleapis.com/maps/api/geocode/json?&address='+mooDeliveryOptions.moo_merchantAddress+'&key=AIzaSyBwB0ahDw6k1CLf9mZxfXd7j5I7rq1bw70',function (data) {
            if(data.results.length>0) {
                var location = data.results[0].geometry.location;
                moo_initMap(location,18);
                jQuery('#Moo_Lat').val(location.lat);
                jQuery('#Moo_Lng').val(location.lng);
            } else {
                var location = {};
                location.lat = 40.748817 ;
                location.lng = -73.985428;
                moo_initMap(location,10);
                jQuery('#Moo_Lat').val(location.lat);
                jQuery('#Moo_Lng').val(location.lng);
            }
        })
    } else {
        var Merchantlocation = {};
        Merchantlocation.lng = parseFloat(mooDeliveryOptions.moo_merchantLng);
        Merchantlocation.lat = parseFloat(mooDeliveryOptions.moo_merchantLat);
        moo_initMap(Merchantlocation,18);
        jQuery('#Moo_Lat').val(Merchantlocation.lat);
        jQuery('#Moo_Lng').val(Merchantlocation.lng);
    }
}
function moo_initMap(myLatLng,zoom) {
    var map = new google.maps.Map(document.getElementById('moo_map'), {
        zoom: zoom,
        center: myLatLng
    });

    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        draggable:true
    });
    google.maps.event.addListener(marker, 'drag', function() {
        moo_updateMarkerPosition(marker.getPosition());
    });
}
function moo_updateMarkerPosition(newPosition)
{
    jQuery('#Moo_Lat').val(newPosition.lat());
    jQuery('#Moo_Lng').val(newPosition.lng());
}

moo_getLatLong();