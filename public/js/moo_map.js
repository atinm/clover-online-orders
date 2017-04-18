/**
 * Created by Med EL.
 * Updated on Dec 2016 to add a new checkout page
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

function moo_initMap(myLatLng)
{
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
    if(!(typeof moo_merchantLat === 'undefined'))
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
    moo_draw_zones();
}

function moo_draw_zones()
{
    var moo_delivery_areas = null;
    try {
        moo_delivery_areas  = JSON.parse(moo_delivery_zones);
    } catch (e) {
        console.log("Parsing error: moo_delivery_areas");
    }

    if(moo_delivery_areas != null && moo_delivery_areas.length >= 1)
    {
        for(i in moo_delivery_areas)
        {
            var element = moo_delivery_areas[i];

            if(element.type == 'circle')
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
    var country   = '';
    document.getElementById('moo_dz_address').innerText  = address + ' ' + city + ' ' + state + ' ' + country;
}
function moo_calculate_delivery_fee(customer_lat,customer_lng,callback)
{
    var order_total             = parseFloat(moo_Total.sub_total);
    var delivery_free_after     = parseFloat(moo_delivery_free_amount)  ; //Free delivery after this amount
    var delivery_fixed_amount   = parseFloat(moo_delivery_fixed_amount) ; //Fixed delivery amount
    var delivery_for_other_zone = parseFloat(moo_delivery_other_zone_fee) ; //Amount of delivery for other zones
    var moo_delivery_areas = null;
    try {
        moo_delivery_areas  = JSON.parse(moo_delivery_zones);
    } catch (e) {
        console.log("Parsing error: moo_delivery_areas");
    }

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
            if(!isNaN(delivery_free_after))
            {
                var amountToAdd = delivery_free_after-order_total;
                swal({ title: 'Spend $'+delivery_free_after.toFixed(2)+" to get free delivery",text:'Add $'+(amountToAdd.toFixed(2))+' to your order to enjoy free delivery',   type: "warning",   showCancelButton: true,  confirmButtonColor: "#DD6B55",   confirmButtonText: "Continue shopping",cancelButtonText: "Checkout",   closeOnConfirm: false },function(){ window.history.back() });
            }
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
                // Then we we update the delivery amount by this zone fee
                // else we verify if the merchant allow other zones
                if(zones_contain_point.length>=1 )
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

                    if(!isNaN(delivery_free_after))
                    {
                        var amountToAdd = delivery_free_after-order_total;
                        if(amountToAdd > 0 && amountToAdd < delivery_final_amount)
                            delivery_final_amount = amountToAdd;
                    }

                    //Verify the min amount
                    for(i in moo_delivery_areas)
                    {
                        var el = moo_delivery_areas[i];
                        if(delivery_zone_id == el.id)
                        {
                            if(parseFloat(el.minAmount) > parseFloat(moo_Total.sub_total) )
                            {
                                var res ={};
                                res.type='min_error';
                                res.amount='';
                                res.message="The minimum order total for this selected zone is $"+parseFloat(el.minAmount).toFixed(2);
                                callback(res);
                            }
                            else
                            {
                                var res ={};
                                res.type='success';
                                res.amount=delivery_final_amount;
                                res.zoneName=el.name;
                                callback(res);
                            }
                        }
                    }
                }
                else
                {
                    if(isNaN(delivery_for_other_zone))
                    {
                        var res ={};
                        res.type='zone_error';
                        res.amount='';
                        callback(res);
                    }
                    else
                    {
                        if(!isNaN(delivery_free_after))
                        {
                            var amountToAdd = delivery_free_after-order_total;
                            if(amountToAdd > 0 && amountToAdd < delivery_for_other_zone)
                                delivery_for_other_zone = amountToAdd;
                        }
                        var res ={};
                        res.type='other_zone';
                        res.amount=delivery_for_other_zone.toFixed(2);
                        callback(res);
                    }
                }
             }
        }
        else
        {
            if(delivery_free_after <= order_total )
            {
                var res ={};
                res.type='free';
                res.amount='0.00';
                callback(res);
            }
        }
    }
    else
    {
        var res ={};
        res.type='fixed';
        res.amount=delivery_fixed_amount.toFixed(2);
        callback(res);
    }
}

function moo_update_delivery_amount(result)
{
    var html='<strong>Delivery amount :</strong><br/>';
    switch(result.type) {
        case 'other_zone':
            html+= '$'+result.amount;
            MooDeliveryfees = result.amount;
            MooIsDeliveryError = false;
            break;
        case 'zone_error':
            html = 'Sorry, zone not supported. We do not deliver to this address at this time';
            swal('Sorry, zone not supported. We do not deliver to this address at this time',"","error");
            MooDeliveryfees = false;
            MooIsDeliveryError = true;
            break;
        case 'min_error':
            html = result.message;
            swal(result.message,"","error");
            MooDeliveryfees = false;
            MooIsDeliveryError = true;
            break;
        case 'success':
            html += '$'+result.amount;
            MooDeliveryfees =result.amount;
            MooIsDeliveryError = false;
            break;
         case 'free':
             html += 'Free';
             MooDeliveryfees = 0.00;
             MooIsDeliveryError = false;
             break;
        case 'fixed':
            html += '$'+result.amount;
            MooDeliveryfees = result.amount;
            MooIsDeliveryError = false;
            break;
    }
    jQuery('#mooDeliveryAmountInformation').html(html);
}
function moo_update_totals()
{
   // console.log(moo_Total);
    if(document.getElementById('moo_tips') != null)
        var tips_amount     = parseFloat(document.getElementById('moo_tips').value);
    else
        var tips_amount = 0;

    if(MooDeliveryfees == null || isNaN(MooDeliveryfees) || MooDeliveryfees < 0 || MooDeliveryfees === false )
    {
        MooDeliveryfees = 0;
    }
    else
    {
        MooDeliveryfees = parseFloat(MooDeliveryfees);
    }

    if(isNaN(tips_amount) || tips_amount < 0)
        tips_amount = 0;


    //Calculate the new Total
    if(MooOrderTypeIsTaxable)
        var new_total = parseFloat(moo_Total.total) + tips_amount + MooDeliveryfees;
    else
        var new_total = parseFloat(moo_Total.sub_total) + tips_amount + MooDeliveryfees;

    jQuery('.moo-totals-value').fadeOut(300, function() {
        jQuery('#moo-cart-subtotal').html(moo_Total.sub_total);
        if(MooOrderTypeIsTaxable)
        {
            if(moo_Total.coupon == null)
                jQuery('#moo-cart-tax').html(moo_Total.total_of_taxes_without_discounts);
            else
                jQuery('#moo-cart-tax').html(moo_Total.total_of_taxes);

        }
        else
            jQuery('#moo-cart-tax').html("0.00");


        if(moo_Total.coupon == null)
        {
            //Hide the coupon section in total
            jQuery('#MooCouponInTotalsSection').hide();
        }
        else
        {
            //Show coupon section in total
            jQuery('#MooCouponInTotalsSection').show();
            jQuery('#mooCouponName').html(moo_Total.coupon.name);

            if(moo_Total.coupon.type == 'amount')
                jQuery('#mooCouponValue').html("-"+moo_Total.coupon.value);
            else
                jQuery('#mooCouponValue').html("-"+moo_Total.coupon.value*moo_Total.sub_total/100);

            //update the total when the order is not taxable
            if(!MooOrderTypeIsTaxable)
            {
                if(moo_Total.coupon.type == 'amount')
                    new_total =  parseFloat(moo_Total.total) + tips_amount + MooDeliveryfees - moo_Total.coupon.value;
                else
                    new_total =  parseFloat(moo_Total.sub_total) + tips_amount + MooDeliveryfees - new_total*moo_Total.coupon.value/100;

            }
        }

        jQuery('#moo-cart-tip').html(tips_amount.toFixed(2));
        jQuery('#moo-cart-delivery-fee').html(MooDeliveryfees.toFixed(2));
        jQuery('#moo-cart-total').html(new_total.toFixed(2));
        jQuery('.moo-totals-value').fadeIn(300);
    });
}

 function moo_show_map_information()
 {
     console.log('show map');
 }