/**
 * Created by Intents Coder on 4/14/2016.
 */
function moo_getLatLong()
{
    if(moo_merchantLat!= "" && moo_merchantLng != "")
    {
        var Merchantlocation = {};
        Merchantlocation.lng = parseFloat(moo_merchantLng);
        Merchantlocation.lat = parseFloat(moo_merchantLat);
        initMap(Merchantlocation);
    }
}
function initMap(myLatLng) {
    var map = new google.maps.Map(document.getElementById('moo_merchantmap'), {
        zoom: 18,
        center: myLatLng
    });

    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map
    });

}