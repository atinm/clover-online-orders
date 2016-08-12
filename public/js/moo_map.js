/**
 * Created by Med EL.
 */
var map = null;
var marker = null;

google.maps.Circle.prototype.contains = function(latLng,circle) {
    return circle.getBounds().contains(latLng) && google.maps.geometry.spherical.computeDistanceBetween(this.getCenter(), latLng) <= circle.getRadius();
}

function moo_getLatLong()
{
    if(moo_merchantLat!= "" && moo_merchantLng != "")
    {
        var Merchantlocation = {};
        Merchantlocation.lng = parseFloat(moo_merchantLng);
        Merchantlocation.lat = parseFloat(moo_merchantLat);
       moo_initMap(Merchantlocation);
    }
}
function moo_initMap(myLatLng) {
    var map = new google.maps.Map(document.getElementById('moo_merchantmap'), {
        zoom: 10,
        center: myLatLng
    });

    var marker = new google.maps.Marker({
        position: myLatLng,
        map: map
    });
}

function moo_InitZones()
{
    if(moo_merchantLat!= "" && moo_merchantLng != "")
    {
        var Merchantlocation = {};
        Merchantlocation.lng = parseFloat(moo_merchantLng);
        Merchantlocation.lat = parseFloat(moo_merchantLat);
        moo_initMapDZ(Merchantlocation);
    }
}

function moo_initMapDZ(myLatLng) {
    map = new google.maps.Map(document.getElementById('moo_dz_map'), {
        zoom: 10,
        center: myLatLng
    });

   /* var marker = new google.maps.Marker({
        position: myLatLng,
        editable:true,
        map: map
    });
    */
    moo_draw_zones();
}

function moo_draw_zones()
{
    var moo_delivery_areas  = JSON.parse(moo_delivery_zones);
    if(moo_delivery_areas.length >= 1)
    {
        for(i in moo_delivery_areas)
        {
            var element = moo_delivery_areas[i];

            if(element.type=='circle')
            {
                var Circle =  new google.maps.Circle({
                    strokeColor: element.color,
                    strokeOpacity: 0.8,
                    strokeWeight: 3.5,
                    fillColor: element.color,
                    fillOpacity: 0.35,
                    map: map,
                    center: element.center,
                    radius: element.radius
                });
            }
            else
                var Polygon = new google.maps.Polygon({
                    fillColor: element.color,
                    strokeColor: element.color,
                    fillOpacity: 0.35,
                    strokeWeight: 3.5,
                    strokeOpacity: 0.8,
                    map:map,
                    paths:element.path
                });
        }
    }
    else
    {
        jQuery('#moo-delivery-details').hide();
        var order_total             = parseFloat(moo_Total.sub_total);
        var delivery_free_after     = parseFloat(moo_delivery_free_amount)  ; //Free delivery after this amount
        var delivery_fixed_amount   = parseFloat(moo_delivery_fixed_amount) ; //Fixed delivery amount
        var delivery_for_other_zone = parseFloat(moo_delivery_other_zone_fee) ; //Amount of delivery for other zones


        if(isNaN(delivery_free_after) || delivery_free_after > order_total )
        {
            if(isNaN(delivery_fixed_amount) || delivery_fixed_amount < 0 )
            {
                document.getElementById('moo_delivery_amount').value = '';
            }
            else
            {
                document.getElementById('moo_delivery_amount').value = delivery_fixed_amount;
                moo_update_totals();
            }
        }
        else
        {
            //Free delivery after spending X
            if(delivery_free_after <= order_total )
            {
                // Enjoy FREE Delivery
                document.getElementById('moo_delivery_amount').value = 'FREE';
                moo_update_totals();

            }
        }

    }

}
function moo_address_changed()
{
    var address   = document.getElementById('address').value;
    var city      = document.getElementById('city').value;
    var state     = document.getElementById('state').value;
    var country   = document.getElementById('country').value;
    document.getElementById('moo_dz_address').innerText  = address + ' ' + city + ' ' + state + ' ' + country;
}

function moo_address_SetOnMap(event)
{
    event.preventDefault();
    //Get the address
    var address = document.getElementById('moo_dz_address').innerText;

    if(address == "")
        address = moo_merchantAddress;

     jQuery.get('https://maps.googleapis.com/maps/api/geocode/json?&address='+address+'&key=AIzaSyBv1TkdxvWkbFaDz2r0Yx7xvlNKe-2uyRc',function (data) {
        if(data.results.length>0)
        {
            var location = data.results[0].geometry.location;
            document.getElementById("moo_customer_lat").value = location.lat;
            document.getElementById("moo_customer_lng").value = location.lng;

            if(marker != null ) marker.setMap(null);
            marker = new google.maps.Marker({
                position: location,
                draggable: true,
                icon:{
                    url:moo_params['plugin_img']+'/moo_marker.png'
                },
                map: map
            });

            google.maps.event.addListener(marker, 'drag', function() {
                document.getElementById("moo_customer_lat").value = marker.getPosition().lat();
                document.getElementById("moo_customer_lng").value = marker.getPosition().lng();
            });
            google.maps.event.addListener(marker, 'dragend', function() {
                moo_calculate_delivery_fee();
            });
            moo_calculate_delivery_fee();
        }
        else
        {
            var location = {'lat':parseFloat(moo_merchantLat),'lng':parseFloat(moo_merchantLng)};

            document.getElementById("moo_customer_lat").value = location.lat;
            document.getElementById("moo_customer_lng").value = location.lng;

            if(marker != null ) marker.setMap(null);
            marker = new google.maps.Marker({
                position: location,
                draggable: true,
                icon:{
                    url:moo_params['plugin_img']+'/moo_marker.png'
                },
                map: map
            });

            google.maps.event.addListener(marker, 'drag', function() {
                document.getElementById("moo_customer_lat").value = marker.getPosition().lat();
                document.getElementById("moo_customer_lng").value = marker.getPosition().lng();
            });
            google.maps.event.addListener(marker, 'dragend', function() {
                moo_calculate_delivery_fee();
            });
            moo_calculate_delivery_fee();
        }
    })

}
function moo_calculate_delivery_fee()
{
    toastr.remove();

    var order_total             = parseFloat(moo_Total.sub_total);
    var delivery_free_after     = parseFloat(moo_delivery_free_amount)  ; //Free delivery after this amount
    var delivery_fixed_amount   = parseFloat(moo_delivery_fixed_amount) ; //Fixed delivery amount
    var delivery_for_other_zone = parseFloat(moo_delivery_other_zone_fee) ; //Amount of delivery for other zones
    var moo_delivery_areas      = JSON.parse(moo_delivery_zones);
    var customer_lat            = document.getElementById("moo_customer_lat").value;
    var customer_lng            = document.getElementById("moo_customer_lng").value;

    /*
     The order of fees is :
     1- Fixed amount of the delivery
     2- Free Delivery after spending X
     3- Zone fee or other zone fee
     */

    if(isNaN(delivery_fixed_amount))
    {
        if(isNaN(delivery_free_after) || delivery_free_after > order_total )
        {
            //Customer coordinate
            if(customer_lat != '' && customer_lng != '')
            {
                var zones_contain_point = new Array();


                for(i in moo_delivery_areas)
                {
                    var el = moo_delivery_areas[i];

                    //Verify if the selected address in any zone
                    if(el.type == 'polygon')
                    {
                       if(google.maps.geometry.poly.containsLocation( new google.maps.LatLng(parseFloat(customer_lat),parseFloat(customer_lng)), new google.maps.Polygon({paths:el.path})))
                       {
                           zones_contain_point.push({zone_id:el.id,zone_fee:el.fee});
                       }
                    }
                    else
                        if(el.type == 'circle')
                        {
                            var point  = new google.maps.LatLng(parseFloat(customer_lat),parseFloat(customer_lng));
                            var center = new google.maps.LatLng(parseFloat(el.center.lat),parseFloat(el.center.lng));
                            if(google.maps.geometry.spherical.computeDistanceBetween(point, center) <= el.radius)
                            {
                                zones_contain_point.push({zone_id:el.id,zone_fee:el.fee});
                            }
                         }
                }
                // If the selected point on the map exists in at least one merchant's zones
                // Then we we update the delivry amount by this zone fee
                // else we verify if the mercahnt allow other zones
                if( zones_contain_point.length>=1 )
                {
                    var delivery_final_amount = zones_contain_point[0].zone_fee;
                    var delivery_zone_id      = zones_contain_point[0].zone_id;

                    for (i in zones_contain_point)
                    {

                        if(parseFloat(delivery_final_amount) >= parseFloat(zones_contain_point[i].zone_fee))
                        {
                            delivery_final_amount = zones_contain_point[i].zone_fee;
                            delivery_zone_id = zones_contain_point[i].zone_id;
                        }
                    }

                    moo_update_delivery_amount(parseFloat(delivery_final_amount),delivery_zone_id)

                }
                else
                {
                    if(isNaN(delivery_for_other_zone))
                    {
                        moo_update_delivery_amount('ERROR');
                    }
                    else
                    {
                        toastr.success('Delivery amount is : $'+ delivery_for_other_zone.toFixed(2));
                        moo_update_delivery_amount(delivery_for_other_zone,'-1')
                    }


                }
             }

        }
        else
        {
            if(delivery_free_after <= order_total )
            {
                toastr.success('Free Delivery For This Zone');
                moo_update_delivery_amount('FREE','-1')

            }
        }
    }
    else
    {
        toastr.success('Delivery amount is : $'+ delivery_fixed_amount.toFixed(2));
        moo_update_delivery_amount(delivery_fixed_amount,'-1')
    }
}

function moo_update_delivery_amount(amount,zone_id)
{
    if(amount == 'FREE')
    {
        document.getElementById('moo_delivery_amount').value = parseFloat(0.00);
        jQuery('#moo-cart-delivery-fee').html('0.00');
        return;
    }
    if(amount == 'ERROR')
    {
        document.getElementById('moo_delivery_amount').value = 'ERROR';
        moo_update_totals();
        toastr.error('Zone Not Supported');
        return;
    }

    if(amount != 'FREE' && amount != "ERROR")
    {
        if(zone_id == '-1')
        {
            document.getElementById('moo_delivery_amount').value = parseFloat(amount);
            moo_update_totals();
        }
        else
        {
            var  moo_delivery_areas = JSON.parse(moo_delivery_zones);
            //Verify the min amount
            for(i in moo_delivery_areas)
            {
                var el = moo_delivery_areas[i];
                if(zone_id == el.id)
                {
                    if(parseFloat(el.minAmount) > parseFloat(moo_Total.sub_total) )
                    {
                        document.getElementById('moo_delivery_amount').value = 'ERROR';
                        jQuery('#moo-cart-delivery-fee').html(0.00);
                        toastr.error("The minimum order's total for the selected zone is $"+parseFloat(el.minAmount).toFixed(2));
                    }
                    else
                    {
                        toastr.success('Delivery amount is : $'+amount.toFixed(2));
                        document.getElementById('moo_delivery_amount').value = parseFloat(amount);
                        moo_update_totals();
                    }
                }
            }
        }

    }


}

 function moo_update_totals()
 {
     var delivery_amount = parseFloat(document.getElementById('moo_delivery_amount').value);
     var tips_amount     = parseFloat(document.getElementById('moo_tips').value);

     var orderTypes_id   = document.getElementById('OrderType').value;
     var orderType = null;

     for(i in moo_OrderTypes)
     {
         if(orderTypes_id == moo_OrderTypes[i].ot_uuid)
             orderType =  moo_OrderTypes[i];
     }

     if(isNaN(delivery_amount) || delivery_amount < 0)
         delivery_amount = 0.00;
     if(isNaN(tips_amount) || tips_amount < 0)
         tips_amount = 0.00;

     //Calculate the new Total
     if(orderType.taxable == '1')
        var new_total = parseFloat(moo_Total.total) + tips_amount + delivery_amount;
     else
        var new_total = parseFloat(moo_Total.sub_total) + tips_amount + delivery_amount;

     jQuery('.moo-totals-value').fadeOut(300, function() {
         jQuery('#moo-cart-subtotal').html(moo_Total.sub_total);
         if(orderType.taxable == '1')
            jQuery('#moo-cart-tax').html(moo_Total.total_of_taxes);
         else
            jQuery('#moo-cart-tax').html(0);
         jQuery('#moo-cart-tip').html(tips_amount.toFixed(2));
         jQuery('#moo-cart-delivery-fee').html(delivery_amount.toFixed(2));
         jQuery('#moo-cart-total').html(new_total.toFixed(2));
         jQuery('.moo-totals-value').fadeIn(300);
     });
 }