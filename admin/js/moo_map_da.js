/**
 * Created by Intents Coder on 4/14/2016.
 */

var moo_delivery_areas = new Array();
var map;
var selectedShape;
var merchantLocation = null;

document.addEventListener('keydown', function (e){
    if(e.keyCode == '46') {
        e.preventDefault();
        e.stopPropagation();
       moo_deleteSelectedShape();
    }
});

function moo_getLatLongforMapDa()
{
    if(moo_merchantLat === "" || moo_merchantLng === "" || moo_merchantLat === null || moo_merchantLng === null)
    {
        if(moo_merchantAddress === '')
            moo_merchantAddress ='united state';

        jQuery.get('https://maps.googleapis.com/maps/api/geocode/json?&address='+moo_merchantAddress+'&key=AIzaSyBv1TkdxvWkbFaDz2r0Yx7xvlNKe-2uyRc',function (data) {
            if(data.results.length>0)
            {
                var location = data.results[0].geometry.location;
                moo_initMapDa(location,14);
                jQuery('#Moo_Lat').val(location.lat);
                jQuery('#Moo_Lng').val(location.lng);
            }
            else
            {
                var location = {};
                location.lat = 40.748817 ;
                location.lng = -73.985428;
                moo_initMapDa(location,8);
                jQuery('#Moo_Lat').val(location.lat);
                jQuery('#Moo_Lng').val(location.lng);
            }
        })
    }
    else
    {
        var Merchantlocation = {};
        Merchantlocation.lng = parseFloat(moo_merchantLng);
        Merchantlocation.lat = parseFloat(moo_merchantLat);
        moo_initMapDa(Merchantlocation,10);
    }

}
function moo_initMapDa(myLatLng,zoom)
{
     map = new google.maps.Map(document.getElementById('moo_map_da'), {
        zoom: zoom,
        center: myLatLng
    });
    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map,
        draggable:true
    });
    google.maps.event.addListener(map, 'click', moo_clearSelection);
    merchantLocation = myLatLng;

}
function moo_draw_circle(color,radius)
{
    if(parseFloat(radius)>0)
    {
        var CircleWithRadius = new google.maps.Circle({
            strokeOpacity: 0.8,
            strokeWeight: 3.5,
            strokeColor : color,
            fillColor: color,
            fillOpacity: 0.35,
            map: map,
            editable:true,
            center: merchantLocation,
            radius: parseFloat(radius)*1609.34
        });
        var newShape = CircleWithRadius;
        newShape.type = 'circle';
        moo_setSelection(newShape);

        google.maps.event.addListener(newShape, 'radius_changed', function() {
            document.querySelector('#moo_Circleradius').innerText = "Radius : "+(CircleWithRadius.getRadius()/1609.34).toFixed(3)+" Miles / "+(CircleWithRadius.getRadius()/1000).toFixed(3)+" Kilometers";
        });
        document.querySelector('#moo_Circleradius').innerText = "Radius : "+(CircleWithRadius.getRadius()/1609.34).toFixed(3)+" Miles / "+(CircleWithRadius.getRadius()/1000).toFixed(3)+" Kilometers";
    }
    else
    {
        var drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing.OverlayType.CIRCLE,
            drawingControl: false,
            circleOptions: {
                strokeOpacity: 0.8,
                strokeWeight: 3.5,
                strokeColor : color,
                fillColor: color,
                fillOpacity: 0.35,
                map: map,
                editable:true
            }

        });
        drawingManager.setMap(map);

        google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
            var newShape = event.overlay;
            newShape.type = event.type;
            drawingManager.setMap(null);
            google.maps.event.addListener(newShape, 'click', function (e) {
                moo_setSelection(newShape);
                document.querySelector('#moo_Circleradius').innerText = "Radius : "+(newShape.getRadius()/1609.34).toFixed(3)+" Miles / "+(newShape.getRadius()/1000).toFixed(3)+" Kilometers";
            });
            google.maps.event.addListener(drawingManager, 'circlecomplete', function(circle) {
                document.querySelector('#moo_Circleradius').innerText = "Radius : "+(circle.getRadius()/1609.34).toFixed(3)+" Miles / "+(circle.getRadius()/1000).toFixed(3)+" Kilometers";

                google.maps.event.addListener(circle, 'radius_changed', function() {
                    document.querySelector('#moo_Circleradius').innerText = "Radius : "+(circle.getRadius()/1609.34).toFixed(3)+" Miles / "+(circle.getRadius()/1000).toFixed(3)+" Kilometers";
                });
            });
            moo_setSelection(newShape);
        });
    }


}
function moo_draw_polygon(color)
{
    var drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: false,
        polygonOptions: {
            fillColor: color,
            strokeColor: color,
            fillOpacity: 0.35,
            strokeWeight: 3.5,
            strokeOpacity: 0.8,
            editable:true,
            zIndex: 1
        }
    });
    drawingManager.setMap(map);
    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
        var newShape = event.overlay;
        newShape.type = event.type;
        drawingManager.setMap(null);
        google.maps.event.addListener(newShape, 'click', function (e) {
            if (e.vertex !== undefined) {
                if (newShape.type === google.maps.drawing.OverlayType.POLYGON) {
                    var path = newShape.getPaths().getAt(e.path);
                    path.removeAt(e.vertex);
                    if (path.length < 3) {
                        newShape.setMap(null);
                    }
                }
            }
            moo_setSelection(newShape);
        });
        moo_setSelection(newShape);
    });
}
function moo_clearSelection()
{
    document.querySelector('#moo_Circleradius').innerText = '';
    if (selectedShape) {
        if (selectedShape.type !== 'marker') {
            selectedShape.setEditable(false);
        }
        selectedShape = null;
    }
}
function moo_setSelection(shape)
{

    if (shape.type !== 'marker') {
        moo_clearSelection();
        shape.setEditable(true);
    }
    selectedShape = shape;
    document.querySelector('#moo_Circleradius').innerText = '';
}

function moo_deleteSelectedShape()
{
    if (selectedShape) {
        selectedShape.setMap(null);
        for (i in moo_delivery_areas )
        {
            var element = moo_delivery_areas[i];
            if(element.shape == selectedShape)
                delete(moo_delivery_areas[i]);
        }
    }
    moo_create_areas_infos();
    jQuery('#moo_areas_container').append("<p style='color: green;'>PS : Don't forget to click on save changes</p>");
}
function moo_show_form_adding_zone()
{
    jQuery('#moo_adding-zone').show('slow');
    jQuery('.MooAddingZoneBtn').show('slow');
    jQuery('#moo_areas_container').hide('slow');
    jQuery('#moo_dz_action_for_adding').show();
    jQuery('#moo_dz_action_for_updating').hide();

    jQuery('#moo_dz_type_line').show();
    jQuery('#moo_dz_color_line').show();

    jQuery("#moo_dz_typeC").prop('checked',true);
    jQuery("#moo_dz_radius").parent().parent().show();
}
function moo_show_form_updating_zone()
{
    jQuery('#moo_adding-zone').show('slow');
    jQuery('#moo_areas_container').hide('slow');
    jQuery('#moo_dz_action_for_adding').hide();
    jQuery('#moo_dz_action_for_updating').show();
    jQuery('#moo_dz_type_line').hide();
    jQuery('#moo_dz_color_line').hide();
}
function moo_hide_form_adding_zone()
{
    jQuery('#moo_adding-zone').hide('slow');
    jQuery('.MooAddingZoneBtn').hide('slow');
    jQuery('#moo_areas_container').show('slow');
}
function moo_draw_zone()
{
    var color  = jQuery('#moo_dz_color').val();
    var radius = jQuery('#moo_dz_radius').val();
    if(jQuery("#moo_dz_typeC").is(':checked'))
        moo_draw_circle(color,radius);
    else
        moo_draw_polygon(color)

}
function moo_validate_selected_zone()
{
    if(typeof selectedShape == 'undefined' || selectedShape == null || selectedShape.map == null)
    {
        alert('Please select the zone');
        return;
    };
    console.log(selectedShape);
    var zone = {};
    zone.id =  new Date().getUTCMilliseconds();

    var type = selectedShape.type;
    var color = selectedShape.fillColor;
    var tmpMinAmount = jQuery('#moo_dz_min').val();
    var tmpFee = jQuery('#moo_dz_fee').val();

    if(jQuery("#moo_dz_fee_type_value").is(':checked'))
        var tmpFeeType = "value";
    else
        var tmpFeeType = "percent";


    zone.shape     = selectedShape;
    zone.name      = jQuery('#moo_dz_name').val();
    zone.minAmount = (tmpMinAmount!="")?tmpMinAmount:0.00;
    zone.fee       = (tmpFee!="")?tmpFee:0.00;
    zone.type      = type;
    zone.color     = color;
    zone.center    = null;
    zone.path      = null;
    zone.radius    = null;
    zone.feeType   = tmpFeeType;

    if( zone.name == '' )
    {
        alert('Please enter a name of this zone');
        return;
    };

    for (i in moo_delivery_areas )
    {
        var element = moo_delivery_areas[i];
        if(element.name == zone.name)
        {
            alert("Zone's name already exists");
            return;
        }
        if(element.shape == selectedShape)
        {
            alert("This zone already selected, please draw a new zone");
            return;
        }
        if(zone.id == element.id)
        {
            zone.id =  new Date().getUTCMilliseconds();
            zone.id +=  new Date().getUTCMilliseconds();
        }
    }
    moo_delivery_areas.push(zone);
    moo_hide_form_adding_zone();
    moo_create_areas_infos();
    jQuery('#moo_areas_container').append("<p style='color: green;'>PS : Don't forget to click on save changes</p>");

}
function moo_update_selected_zone()
{
    var zone_id = jQuery('#moo_dz_id_for_update').val();
    if(zone_id != "")
    {
        var color = jQuery('#moo_dz_color').val();
        var name      = jQuery('#moo_dz_name').val();
        var minAmount = jQuery('#moo_dz_min').val();
        var fee       = jQuery('#moo_dz_fee').val();

        if(jQuery("#moo_dz_fee_type_value").is(':checked'))
            var tmpFeeType = "value";
        else
            var tmpFeeType = "percent";


        for (i in moo_delivery_areas )
        {
            var element = moo_delivery_areas[i];
            if(zone_id == element.id)
            {
                moo_delivery_areas[i].color = color;
                moo_delivery_areas[i].name  = name;
                moo_delivery_areas[i].minAmount  = minAmount;
                moo_delivery_areas[i].fee  = fee;
                moo_delivery_areas[i].shape.fillColor = color;
                moo_delivery_areas[i].feeType   = tmpFeeType;
                moo_delivery_areas[i].radius   = element.radius;
            }
        }
        moo_hide_form_adding_zone();
        moo_create_areas_infos();
        jQuery('#moo_areas_container').append("<p style='color: green;'>PS : Don't forget to click on save changes</p>");
    }

}
function moo_create_areas_infos()
{
    var html ='';
    for (i in moo_delivery_areas)
    {
        var element = moo_delivery_areas[i];
        if(typeof element.feeType != 'undefined' && element.feeType != null && element.feeType == "percent")
             var fee = element.fee+"%";
        else
             var fee = "$"+element.fee;


        html += '<div class="moo_delivery_zone_label" onmouseenter="moo_show_dz_actions(\''+element.id+'\')" ' ;
        html += 'onmouseleave="moo_hide_dz_actions(\''+element.id+'\')" onclick="moo_updateZone(\''+element.id+'\')"> ' ;
        html += '<span style="background-color: '+element.color+'"></span>'+element.name+' | Delivery fee : '+fee+' | Amount min : $'+element.minAmount ;
        html +='<div class="moo_da_actions" id="moo_da_actions_for_'+element.id+'" ><a href="#" onclick="moo_da_edit_zone(event,\''+element.id+'\')" >Edit</a> | <a href="#" onclick="moo_da_delete_zone(event,\''+element.id+'\')" >Delete</a></div></div>';
    }
    jQuery('#moo_areas_container').html(html);
}
function moo_cancel_adding_form()
{
    jQuery('#moo_dz_name').val('');
    jQuery('#moo_dz_min').val('');
    jQuery('#moo_dz_fee').val('');
    moo_hide_form_adding_zone();
    moo_create_areas_infos();
}
function moo_updateZone(zone_id)
{
    for (i in moo_delivery_areas )
    {
        var element = moo_delivery_areas[i];
        if(element.id == zone_id)
        {
           //console.log(element);
            moo_setSelection(element.shape);
        }
    }
}
function moo_show_dz_actions(id)
{
    jQuery('#moo_da_actions_for_'+id).show();
}
function moo_hide_dz_actions(id)
{
    jQuery('#moo_da_actions_for_'+id).hide();
}
function moo_da_edit_zone(event,ZoneId)
{
    event.preventDefault();
    jQuery("#moo_dz_radius").parent().parent().hide();
    var ZoneToEdit={};
    for (i in moo_delivery_areas )
    {
        var element = moo_delivery_areas[i];
        if(element.id == ZoneId)
            ZoneToEdit =element;
    }

    moo_show_form_updating_zone();
    jQuery('#moo_dz_name').val(ZoneToEdit.name);
    jQuery('#moo_dz_min').val(ZoneToEdit.minAmount);
    jQuery('#moo_dz_fee').val(ZoneToEdit.fee);
    jQuery('#moo_dz_color').val(ZoneToEdit.color);
    jQuery('#moo_dz_id_for_update').val(ZoneToEdit.id);

    if(typeof ZoneToEdit.feeType != 'undefined' && ZoneToEdit.feeType != null && ZoneToEdit.feeType == "percent")
       //Select percent in Type
        jQuery("#moo_dz_fee_type_percent").prop("checked",true);
    else
       //Select value in Type
        jQuery("#moo_dz_fee_type_value").prop("checked",true);
}
function moo_da_delete_zone(event,ZoneId)
{
    event.preventDefault();
    event.preventDefault();
    var ZoneToDelete={};
    for (i in moo_delivery_areas )
    {
        var element = moo_delivery_areas[i];
        if(element.id == ZoneId)
        {
            ZoneToDelete =element;
            ZoneToDelete.shape.setMap(null);
            delete(moo_delivery_areas[i]);

        }
    }
    moo_create_areas_infos();
    moo_clearSelection();
    jQuery('#moo_areas_container').append("<p style='color: green;'>PS : Don't forget to click on save changes</p>");
}
function moo_save_changes()
{
    var zones =  new Array();
    for (i in moo_delivery_areas )
    {
        var element = moo_delivery_areas[i];
        console.log(element);
        var zone = {};
        zone.id        = element.id;
        zone.name      = element.name;
        zone.minAmount = element.minAmount;
        zone.fee       = element.fee;
        zone.type      = element.type;
        zone.color     = element.color;
        zone.center    = null;
        zone.radius    = null;
        zone.path      = null;

        if(element.type == 'circle')
         {
             var radius = element.shape.getRadius();
             var center = element.shape.getCenter();
             zone.center = center;
             zone.radius = radius;
         }
         else
         {
             var vertices = element.shape.getPath();
             zone.path =  new Array();
             for (var i =0; i < vertices.getLength(); i++) {
                 var xy = vertices.getAt(i);
                 zone.path.push({lat:xy.lat(),lng:xy.lng()})
             }
         }

         if(typeof element.feeType != 'undefined' && element.feeType != null && element.feeType == "percent")
            //Select percent in Type
             zone.feeType = "percent";
         else
            //Select value in Type
             zone.feeType = "value";

        zones.push(zone);
    }
    var zones_txt = JSON.stringify(zones);

    jQuery('#moo_zones_json').val(zones_txt);
    return true;
}
function moo_setup_existing_zones() {
    var zones_txt = jQuery('#moo_zones_json').val();
    var zones = null;
    try {
        zones = JSON.parse(zones_txt);
    } catch (e) {
        console.log("Parsing error: zones");
    }
    for (i in zones) {
        var tmp_zone = zones[i];
        tmp_zone.shape = null;

        if(tmp_zone.type=='circle')
        {
            tmp_zone.shape = new google.maps.Circle({
                strokeColor: tmp_zone.color,
                strokeOpacity: 0.8,
                strokeWeight: 3.5,
                fillColor: tmp_zone.color,
                fillOpacity: 0.35,
                map: map,
                center: tmp_zone.center,
                radius: tmp_zone.radius
            });
            google.maps.event.addListener(tmp_zone.shape, 'radius_changed', function() {
                document.querySelector('#moo_Circleradius').innerText = "Radius : "+(tmp_zone.shape.getRadius()/1609.34).toFixed(3)+" Miles / "+(tmp_zone.shape.getRadius()/1000).toFixed(3)+" Kilometers";
            });
        }

        else
            tmp_zone.shape = new google.maps.Polygon({
                fillColor: tmp_zone.color,
                strokeColor: tmp_zone.color,
                fillOpacity: 0.35,
                strokeWeight: 3.5,
                strokeOpacity: 0.8,
                map:map,
                paths:tmp_zone.path
        });

        moo_delivery_areas.push(tmp_zone);
    }
    moo_create_areas_infos();
}
function mooZone_type_Clicked()
{
    if(jQuery("#moo_dz_typeC").is(':checked'))
        jQuery("#moo_dz_radius").parent().parent().show();
    else
        jQuery("#moo_dz_radius").parent().parent().hide();
}
