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
                    html +='<td colspan="2"></td>'; //Controlles Btn
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
            if(data.total == 0 ){
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
    qte = qte+1;
    var sub_total =productPrice*qte/100;

    jQuery("#moo_itemqte_"+item).text(qte);

    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_qte','item':item,'qte':qte}, function (data) {
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
                /* If the min is not null then we display a message if the custmet not choose the minimum */
                if(data.min != null && data.min != 0 && Mgroups[mg] < data.min) {
                    toastr.error("Minimum number of modifiers required is "+data.min);
                    flag=true;
                }
                if(data.max!= null && data.max != 0 && Mgroups[mg] > data.max) {
                    toastr.error("Maximum number of modifiers allowed is "+ data.max);
                    flag=true;
                }
            }
        })
    }
    if(!flag)
    {
        //send the request to the server
        jQuery.post(moo_params.ajaxurl,{'action':'moo_modifier_add',"modifiers":Modifiers}, function (data) {
            if(data.status == 'success' )
            {
                toastr.success("Item added to your cart ");
                setTimeout(function(){window.history.back();},2000)
            }
        })

    }
}