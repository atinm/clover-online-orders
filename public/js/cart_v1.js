function moo_updateCart()
{
    jQuery(".moo-cart-modal-lg .modal-body").html('Loading');

    jQuery.post(moo_params.ajaxurl,{'action':'moo_get_cart'}, function (data) {
        //console.log(data);
        if(data.status=="success")
        {
            // console.log(data.data);
            var html = ''+
                '<div  class="table-responsive">'+
                '<table class="table table-striped"><thead>'+
                '<tr>'+
                '<th>Product</th>'+
                '<th>Price</th>'+
                '<th>Quantity</th>'+
                '<th>Sub-total</th>'+
                '<th></th>'+
                '</tr>'+
                '</thead><tbody>';
            for(item in data.data)
            {
                if(item == "") continue;
                MOO_CART[item] = {uuid:item,name:item.name};
                var product = data.data[item];
                var price = (product.item.price*product.quantity/100);
                var subtotal = price;

                if(Object.keys(product.modifiers).length>0){

                    //line of the cart
                    html +="<tr class='warning' id='moo_cart_line_"+item+"'>";
                    html +="<td>"+product.item.name+"</td>"; //The name of the item
                    html +="<td>$"+(product.item.price/100)+"</td>"; // The price
                    html +='<td>' + //The quantity and buttons of commands
                    '<div class="row" style="width: 130px;">' +
                    '<div class="col-md-4 col-xs-12 col-sm-4">' +
                    '<div class="moo_btn_qte" onclick="moo_decQte('+product.item.price+',\''+item+'\')">-</div>' +
                    '</div>' +
                    '<div class="col-md-4 col-xs-12 col-sm-4">' +
                    '<div id="moo_itemqte_'+item+'" class="moo_qte" >'+product.quantity+'</div>' +
                    '</div>' +
                    '<div class="col-md-4 col-xs-12 col-sm-4">' +
                    '<div class="moo_btn_qte" onclick="moo_incQte('+product.item.price+',\''+item+'\')">+</div>' +
                    '</div>' +
                    '</div>' +
                    '</td>';
                    html +='<td></td>'; //The Tax
                    html +='<td></td>'; //Sub total  ( price + taxes )
                    html +='<td></td>'; //Controlles Btn
                    html +='</tr>'; //Controlles Btn
                    // the Modifiers
                    for(uuid in product.modifiers){
                        var modifier = product.modifiers[uuid];
                        var modifierPrice = modifier.price/100;

                        html +='<tr id="moo_cart_modifier_'+uuid+'" class="warning MooLineModifier4_'+item+'" style="font-size: 0.8em;text-align: right;">';
                        html +='<td style="text-align: right;">'+modifier.name+'</td>';
                        html +='<td style="text-align: left;">$'+modifierPrice+'</td>';
                        html +='<td></td>';
                        html +='<td></td>';
                        html +='<td></td>';
                        html +='<td style="text-align: left;"><i class="fa fa-close" style="cursor: pointer;" onclick="moo_cart_DeleteItemModifier(\''+uuid+'\',\''+item+'\')"></i></td>';
                        html +="</tr>";
                        subtotal += modifierPrice*product.quantity;
                    }
                    var total = Math.round((subtotal)*100)/100;

                    html +='<tr class="warning MooLineModifier4_'+item+'" ><td colspan="3"></td>';
                    html +='<td id="moo_itemsubtotal_'+item+'">$'+total+'</td>'; //Sub total  ( price + taxes )
                    html +='<td><i class="fa fa-trash" style="cursor: pointer;" onclick="moo_cart_DeleteItem(\''+item+'\')"></i></td>'; //Controlles Btn
                    html +="</tr>";
                    html +='<tr class="warning MooLineModifier4_'+item+'" ><td colspan="6"></td></tr>';

                    //Fin line

                }
                else
                {
                    var total = Math.round((subtotal)*100)/100;

                    html +="<tr id='moo_cart_line_"+item+"'>";
                    html +="<td>"+product.item.name+"</td>"; //The name of the item
                    html +="<td>$"+(product.item.price/100)+"</td>"; // The price
                    html +='<td>' + //The quantity and buttons of commands
                    '<div class="row" style="width: 130px;">' +
                    '<div class="col-md-4 col-xs-12 col-sm-4">' +
                    '<div class="moo_btn_qte" onclick="moo_decQte('+product.item.price+',\''+item+'\')">-</div>' +
                    '</div>' +
                    '<div class="col-md-4 col-xs-12 col-sm-4">' +
                    '<div id="moo_itemqte_'+item+'" class="moo_qte" >'+product.quantity+'</div>' +
                    '</div>' +
                    '<div class="col-md-4 col-xs-12 col-sm-4">' +
                    '<div class="moo_btn_qte" onclick="moo_incQte('+product.item.price+',\''+item+'\')">+</div>' +
                    '</div>' +
                    '</div>' +
                    '</td>';
                    html +='<td id="moo_itemsubtotal_'+item+'">$'+total+'</td>'; //Sub total  ( price + taxes )
                    html +='<td><i class="fa fa-trash" style="cursor: pointer;" onclick="moo_cart_DeleteItem(\''+item+'\')"></i></td>'; //Controlles Btn
                    html +="</tr>";
                }



            }

            html += "</tbody></table></div>"
            jQuery(".moo-cart-modal-lg .modal-body").html(html);
            moo_updateCartTotal();
        }
        else
        {
            jQuery(".moo-cart-modal-lg .modal-body").html(data.message);
        }
    })
}
function moo_updateCartTotal()
{

    jQuery(".moo_cart_total > td:last").html("Calculating...");

    jQuery.post(moo_params.ajaxurl,{'action':'moo_cart_getTotal'}, function (data) {
        if(data.status=="success")
        {
            if(data.total == 0 || Object.keys(MOO_CART).length == 0 ){
                jQuery(".moo_cart_total").remove();
                jQuery(".moo-cart-modal-lg .modal-body").html("Your cart is empty !");
                return;
            }

            html ="<tr  class='moo_cart_total'><td colspan='6'></td></tr>";
            html +="<tr class='moo_cart_total'>";
            html +="<td colspan='3' style='text-align: right;font-weight: bold'>Subtotal:</td>";
            html +="<td colspan='3'>$"+data.sub_total+"</td>";
            html +="</tr>";

            html +="<tr  class='moo_cart_total'>";
            html +="<td colspan='3' style='text-align: right;font-weight: bold'>Tax:</td>";
            html +="<td colspan='3'>$"+data.total_of_taxes+"</td>";
            html +="</tr>";

            html +="<tr  class='moo_cart_total'>";
            html +="<td colspan='3' style='text-align: right;font-weight: bold'>Total:</td>";
            html +="<td colspan='3'>$"+data.total+"</td>";
            html +="</tr>";

            jQuery(".moo_cart_total").remove();
            jQuery(".moo-cart-modal-lg .modal-body>.table-responsive>table").append(html);
        }
        else
        {
            moo_updateCart();
        }

    });
}

function moo_cart_DeleteItem(item)
{
    //send delete query to server
    jQuery.post(moo_params.ajaxurl,{'action':'moo_deleteItemFromcart',"item":item}, function (data) {
        if(data.status != "success"){
            moo_updateCart();
        };
    });

    jQuery("#moo_cart_line_"+item).remove();
    jQuery(".MooLineModifier4_"+item).remove();
    delete(MOO_CART[item]);
    moo_updateCartTotal();
}
function moo_cart_DeleteItemModifier(uuid,item)
{
    //send delete query to server
    jQuery.post(moo_params.ajaxurl,{'action':'moo_cart_DeleteItemModifier',"modifier":uuid,"item":item}, function (data) {
        if(data.status != "success" || data.last ){
            moo_updateCart();
        }
    });

    jQuery("#moo_cart_modifier_"+uuid).remove();
    moo_updateCartTotal();
}
function moo_emptyCart()
{
    //send delete query to server
    jQuery.post(moo_params.ajaxurl,{'action':'moo_emptycart'}, function (data) {
        if(data.status == "success"){
            console.log(data);
            moo_updateCart();
        };
    });
}
function moo_incQte(productPrice,item)
{
    // inc in the session by sending request to the server
    var qte = (jQuery("#moo_itemqte_"+item).text())*1;
    if(qte<10) {
        qte = qte+1;
        var sub_total =productPrice*qte/100;

        jQuery("#moo_itemqte_"+item).text(qte);

        jQuery.post(moo_params.ajaxurl,{'action':'moo_cart_incQuantity','item':item}, function (data) {
            if(data.status == "success"){
                if( jQuery("#moo_cart_line_"+item).hasClass('warning'))
                {
                    jQuery.post(moo_params.ajaxurl,{'action':'moo_cart_getItemTotal','item':item}, function (data) {
                        if(data.status == "success"){
                            jQuery("#moo_itemsubtotal_"+item).text('$'+data.total);
                        }
                    });
                }
                else
                {
                    jQuery("#moo_itemsubtotal_"+item).text('$'+sub_total);
                }
                moo_updateCartTotal();
            }
            else
            {
                moo_updateCart();
            };
        });



    }
}
function moo_decQte(productPrice,item)
{
    // dec in the session by sending request to the server
    var qte = (jQuery("#moo_itemqte_"+item).text())*1;
    if(qte>1) {
        qte = qte-1;

        var sub_total =productPrice*qte/100;
        jQuery("#moo_itemqte_"+item).text(qte);

        jQuery.post(moo_params.ajaxurl,{'action':'moo_cart_decQuantity','item':item}, function (data) {
            if(data.status == "success"){
                if( jQuery("#moo_cart_line_"+item).hasClass('warning'))
                {
                    jQuery.post(moo_params.ajaxurl,{'action':'moo_cart_getItemTotal','item':item}, function (data) {
                        if(data.status == "success"){
                            jQuery("#moo_itemsubtotal_"+item).text('$'+data.total);
                        }
                    });
                }
                else
                {
                    jQuery("#moo_itemsubtotal_"+item).text('$'+sub_total);
                }
                moo_updateCartTotal();
            }
            else
            {
                moo_updateCart();
            };
        });
    }

}
/*
function moo_Checkout(){
    jQuery(".moo-cart-modal-lg .modal-body").html("<h1 align='center'>Preparing your cart...</h1>");
    jQuery.post(moo_params.ajaxurl,{'action':'moo_cart_getTotal'}, function (data) {

        if(data.status=="success")
        {
            window.total = data.total.toFixed(2);
            window.sub_total = data.sub_total.toFixed(2);
        }
        else
            window.total = 0;

    }).done(function(){
        jQuery.post(moo_params.ajaxurl,{'action':'moo_getodertybes'}, function (data) {
            if(data.status =="success")
            {
                window.OrderTypes = data.data;

                window.OrderTypes_html = '<select class="form-control" name="OrderType" id="OrderType" onchange="moo_OrderTypeChanged(this)">';
                window.OrderTypesSelected = false;

                for(i in data.data)
                {
                    if(data.data[i].taxable && data.data[i].isDefault) window.OrderTypesSelected = true;
                    var label = data.data[i].label;
                    if(data.data[i].taxable)
                        window.OrderTypes_html += (data.data[i].isDefault)?'<option value="'+data.data[i].id+'" selected>'+label+'</option>' : '<option value="'+data.data[i].id+'">'+label+'</option>';
                    else
                        window.OrderTypes_html += (data.data[i].isDefault)?'<option value="'+data.data[i].id+'" selected>'+label+ ' (Not taxable)</option>':'<option value="'+data.data[i].id+'">'+label+ ' (Not taxable)</option>';
                }
                window.OrderTypes_html += '</select>';
            }
            else
            {
                window.OrderTypes_html = 'Default Type';
            }
        }).done(
            function () {
                if(window.total != 0) {

                    var d = new Date();
                    var n = d.getFullYear();
                    if(n < 2015) n=2015;
                    window.Years = '<select name="expiredDateYear" class="form-control">';
                    for(var i=n;i<n+10;i++)
                    {
                        window.Years += '<option value="'+i+'">'+i+'</option>';
                    }
                    window.Years += '</select>';

                    var html = '<form id="moo_form_address" method="post" action="#">'+
                        '<div class="row">'+
                        '<div class="col-md-6">'+
                        '<div class="panel panel-default">'+
                        '<div class="panel-heading">'+
                        '<p style="font-size: 16px !important; margin:0;">Address</p>'+
                        '</div>'+
                        '<div class="panel-body">'+
                        '<div class="form-group">'+
                        '<label for="OrderType">Order Type:</label>'+
                        window.OrderTypes_html+
                        '</div>'+
                        '<div class="form-group">'+
                        '<label for="name">Name:</label>'+
                        '<input class="form-control" name="name" id="name">'+
                        '</div>'+
                        '<div class="form-group">'+
                        '<label for="address">Address:</label>'+
                        '<input class="form-control" name="address" id="address">'+
                        '</div>'+
                        '<div class="form-group">'+
                        '<label for="city">City:</label>'+
                        '<input class="form-control" name="city" id="city">'+
                        '</div>'+
                        '<div class="form-group">'+
                        '<label for="zipcode">Zip code:</label>'+
                        '<input class="form-control" name="zipcode" id="zipcode">'+
                        '</div>'+
                        '<div class="form-group">'+
                        '<label for="phone">Phone number:</label>'+
                        '<input class="form-control" name="phone" id="phone">'+
                        '</div>'+
                        '<div class="form-group">'+
                        '<label for="email">Email address:</label>'+
                        '<input class="form-control" name="email" id="email">'+
                        '</div>'+
                            // '</form>'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '<div class="col-md-6">'+
                        '<div class="panel panel-default">'+
                        '<div class="panel-heading">'+
                        '<p style="font-size: 16px !important; margin:0;">Payment</p>'+
                        '</div>'+
                        '<div class="panel-body">'+
                            //'<form id="moo_form_payment">'+
                        '<div class="form-group">'+
                        '<label for="nameOnCard">Name on the card:</label>'+
                        '<input class="form-control" name="nameOnCard" id="nameOnCard">'+
                        '</div>'+
                        '<div class="form-group">'+
                        '<label for="Moo_cardNumber">Card number:</label>'+
                        '<div class="input-group">'+
                        '<input class="form-control" name="cardNumber" id="Moo_cardNumber">'+
                        '<div class="input-group-addon"><img style="min-width:116px;height: 20px;" class="moo_credit_cards"/></div>'+
                        '</div>'+
                        '<label for="Moo_cardNumber" class="error"></label>'+
                        '</div>'+
                        '<div class="row">'+
                        '<div class="col-md-4" style="margin-bottom: 10px;">'+
                        'Expired date:'+
                        '</div>'+
                        '<div class="col-md-4 col-xs-7 col-sm-7">'+
                        '<select name="expiredDateMonth" id="expiredDate" class="form-control">'+
                        '<option value="1">January</option>'+
                        '<option value="2">February</option>'+
                        '<option value="3">March</option>'+
                        '<option value="4">April</option>'+
                        '<option value="5">May</option>'+
                        '<option value="6">June</option>'+
                        '<option value="7">July</option>'+
                        '<option value="8">August</option>'+
                        '<option value="9">September</option>'+
                        '<option value="10">October</option>'+
                        '<option value="11">November</option>'+
                        '<option value="12">December</option>'+
                        '</select>'+
                        '</div>'+
                        '<div class="col-md-4  col-xs-5 col-sm-5" >'+
                        window.Years+
                        '</div>'+
                        '</div>'+
                        '<div class="row" style="margin-top: 13px;">'+
                        '<div class="col-md-4">'+
                        '<label for="moo_cardcvv">CVV:</label>'+
                        '</div>'+
                        '<div class="col-md-8">'+
                        '<input class="form-control" name="cvv" id="moo_cardcvv">'+
                        '</div>'+
                        '</div>'+
                            // '</form>'+
                        '</div>'+
                        '</div>'+
                        '<div class="row">'+ //SubTotal
                        '<div class="col-md-8 col-sm-8 col-xs-8">'+
                        '<h1 style="text-align: right; font-size: 20px !important;">Total :</h1>'+
                        '</div>'+
                        '<div class="col-md-4 col-sm-4 col-xs-4">'+
                        '<h1 style="font-size: 20px !important;" id="moo_Total_inCheckout">$'+((window.OrderTypesSelected)?window.total:window.sub_total)+'</h1>'+
                        '</div>'+
                        '</div>'+
                        '<div class="row" style="margin-top: 70px;">'+ //Finalaize
                        '<div class="col-md-12">'+
                        '<div id="moo_checkout_loading" style="display: none; width: 100%;text-align: center"><svg xmlns="http://www.w3.org/2000/svg" width="44px" height="44px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-default"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"/><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(0 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(30 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.08333333333333333s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(60 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.16666666666666666s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(90 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.25s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(120 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.3333333333333333s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(150 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.4166666666666667s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(180 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.5s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(210 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.5833333333333334s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(240 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.6666666666666666s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(270 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.75s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(300 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.8333333333333334s" repeatCount="indefinite"/></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(330 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.9166666666666666s" repeatCount="indefinite"/></rect></svg></div>'+
                        '<button id="moo_btn_submit_order" type="submit" class="btn btn-lg btn-primary"  style="display: block; width: 100%;">FINALIZE ORDER</button>'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>';
                    '</form>';
                    jQuery(".moo-cart-modal-lg .modal-body").html(html);
                    jQuery("#moo_form_address").validate({
                        rules: {
                            name: {
                                required: true,
                                minlength: 3,
                                maxlength:100
                            },
                            email:{
                                required: true,
                                email: true
                            },
                            phone:{
                                required: true,
                                minlength: 3,
                                maxlength:100
                            },
                            zipcode:{
                                required: true,
                                minlength: 3,
                                maxlength:100
                            },
                            cardNumber:{
                                required: true
                            },
                            cvv:{
                                required: true
                            },
                            address:{
                                required: true,
                                minlength: 3,
                                maxlength:100
                            },
                            city:{
                                required: true,
                                minlength: 3,
                                maxlength:100
                            }
                        },
                        messages: {
                            name: {
                                required: "Please enter your full name "
                            },
                            email: {
                                required: "We need your email address to contact you"
                            },
                            phone: {
                                required: "We need your phone to contact you"
                            },
                            cardNumber: {
                                minlength: "Please enter at least 16 numbers",
                                maxlength: "Please enter no more than 16 numbers."
                            }
                        },
                        submitHandler: function(form) {
                            jQuery('#moo_checkout_msg').remove();
                            jQuery('#moo_btn_submit_order').hide();

                            //Show loading Icon
                            jQuery('#moo_checkout_loading').show();

                            var DataArray = jQuery(form).serializeArray();
                            var DataObject = {};

                            for(i in DataArray) DataObject[DataArray[i]['name']] = DataArray[i]['value'] ;

                            jQuery.post(moo_params.ajaxurl,{'action':'moo_checkout','form':DataObject}, function (data) {
                                if(data.status == 'APPROVED'){
                                    html = '<div align="center" class="alert alert-success" role="alert">Thank you for your order<br> You can see your receipt <a href="https://www.clover.com/r/'+data.order+'" target="_blank">here</a></a> </div>';
                                    jQuery(".moo-cart-modal-lg .modal-body").html(html);
                                }

                                else
                                {
                                    //Hide Loading Icon and Show the button if there is an error
                                    jQuery('#moo_checkout_loading').hide();
                                    jQuery('#moo_btn_submit_order').show();
                                    html = '<div class="alert alert-danger" role="alert" id="moo_checkout_msg"><strong>Error : </strong>'+data.message+'</div>'
                                    jQuery(".moo-cart-modal-lg .modal-body").prepend(html);
                                }

                            });
                        }
                    });

                    //Validate Input credit cards
                    jQuery('#Moo_cardNumber').payment('formatCardNumber');
                    jQuery('#moo_cardcvv').payment('formatCardCVC');

                }
            });
    });
}
*/
function moo_addModifiers()
{
    var selected_modifies = jQuery("#moo_form_modifiers").serializeArray();

    var Mgroups = {};
    var Modifiers = [];
    for(m in selected_modifies)
    {
        var modifier = selected_modifies[m];
        if(modifier.value=='on'){
            var name = modifier.name; //the format is : moo_modifiers['item','modifierGroup','Modifier']

            var string = name.split(','); // the new format is a table
            var item = string[0].substr(15);  // 15 is the length of moo_modifiers['
            item = item.substr(0,item.length-1); // remove the last '

            var modifierGroup = string[1].substr(1);
            modifierGroup = modifierGroup.substr(0,modifierGroup.length-1);

            var modif = string[2].substr(1);
            modif = modif.substr(0,modif.length-2);


            if(typeof Mgroups[modifierGroup] === 'undefined') Mgroups[modifierGroup] = 1;
            else Mgroups[modifierGroup] +=1;

            var modifier = {
                "item":item,
                "modifier": modif,
                "modifierGroup": modifierGroup
            };
            Modifiers.push(modifier);
        }
    }
    var flag = false;
    if(Object.keys(Mgroups).length==0) {
        alert('Please select at least one modifier')
        return;
    }

    for(mg in Mgroups){
        jQuery.post(moo_params.ajaxurl,{'action':'moo_modifiergroup_getlimits',"modifierGroup":mg}, function (data) {
            if(data.status == 'success' )
            {
                if(data.min!=0 && Mgroups[mg] < data.min) {
                    //TODO
                    //alert("Minumum required is "+data.min);
                    flag=true;
                } // Message not atteind the min_required
                if(data.max!=0 && Mgroups[mg] > data.max) {
                   // alert("Max allowed is "+ data.max);
                    flag=true;
                } // Message max_allowed
            }
        })
    }
    if(!flag)
    {
        //send the request to the server
        jQuery.post(moo_params.ajaxurl,{'action':'moo_modifier_add',"modifiers":Modifiers}, function (data) {
            if(data.status == 'success' )
            {
                window.history.back();
            }
        })

    }
}