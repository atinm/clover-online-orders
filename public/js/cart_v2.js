function moo_updateCart()
{
    jQuery(".moo_cart .CartContent").html('<img src="'+moo_params.plugin_img+'/loading.gif" style="text-align: center;margin: 15px auto 0px;display: block;width: 50px;" />');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_get_cart'}, function (data) {
        var html = ''+
                    '<table class="table"><thead>'+
                    '<tr>'+
                    '<th>Item</th>'+
                    '<th>Qty</th>'+
                    '<th colspan="2">Sub-total</th>'+
                    '</tr>'+
                    '</thead><tbody>';
        if(data.status=="success")
        {
            var nb_items = 0;
            for(item in data.data)
            {
                if(item == "") continue;
                var product = data.data[item];
                if(product.item == null ) continue;
                var price = (product.item.price*product.quantity/100);
                var tax = price*product.tax_rate/100;
                var subtotal = price;
                nb_items++;
                if(Object.keys(product.modifiers).length>0){
                    //line of the cart
                    html +="<tr id='moo_cart_line_"+item+"'>";
                    html +="<td  style='cursor: pointer' onclick=\"ChangeQuantity('"+item+"')\" ><strong>"+product.item.name+"</strong>"; //The name of the item
                    // the Modifiers
                    for(uuid in product.modifiers){
                        var modifier = product.modifiers[uuid];
                        var modifierPrice = modifier.price/100;
                        html +='<br/><small>- '+modifier.name+': $'+modifierPrice.toFixed(2)+'</small>';
                        subtotal += modifierPrice*product.quantity;
                    }
                    html +="</td>";

                    html +='<td>'+product.quantity+'</td>'; //qty
                    html +='<td>$'+subtotal.toFixed(2)+'</td>'; //Sub total  ( price + taxes )
                    html +='<td><i class="fa fa-trash" style="cursor: pointer;" onclick="moo_cart_DeleteItem(\''+item+'\')"></i></td>'; //Controlles Btn
                    html +='</tr>';
                }
                else
                {
                    tax = Math.ceil(tax*100)/100;
                    total = Math.ceil((subtotal+tax)*100)/100;

                    html +="<tr id='moo_cart_line_"+item+"' >";
                    html +="<td onclick=\"ChangeQuantity('"+item+"')\" style='cursor:pointer;'><strong>"+product.item.name+"</strong></td>"; //The name of the item
                    html +="<td>"+product.quantity+"</td>"; // The quantiy
                    html +='<td id="moo_itemsubtotal_'+item+'">$'+subtotal.toFixed(2)+'</td>'; //Sub total  ( price + taxes )
                    html +='<td><i class="fa fa-trash" style="cursor: pointer;" onclick="moo_cart_DeleteItem(\''+item+'\')"></i></td>'; //Controlles Btn
                    html +="</tr>";
                }
                //Add the item to Our JS Cart
                MOO_CART[item] = {uuid:item,name:product.item.name,quantity:product.quantity,price:price};
            }
            if(nb_items>0)
            {
                html += "</tbody></table>"
                jQuery(".moo_cart .CartContent").html(html);
                moo_updateCartTotal();
            }
            else
            {
                html += "<tr><td colspan='4' style='text-align: center'>Your cart is empty</td></tr>";
                html += "</tbody></table>"
                jQuery(".moo_cart .CartContent").html(html);
            }
        }
        else
        {
            html += "<tr><td colspan='4' style='text-align: center'>"+data.message+"</td></tr>";
            html += "</tbody></table>";
            jQuery(".moo_cart .CartContent").html(html);
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
                        return;
                    }
                    html ="<tr class='moo_cart_total'>";
                    html +="<td colspan='1' style='text-align: right;'>Subtotal:</td>";
                    html +="<td colspan='3'>$"+data.sub_total+"</td>";
                    html +="</tr>";
                    html +="<tr  class='moo_cart_total'>";
                    html +="<td colspan='1' style='text-align: right;'>Tax:</td>";
                    html +="<td colspan='3'>$"+data.total_of_taxes+"</td>";
                    html +="</tr>";
                    html +="<tr  class='moo_cart_total'>";
                    html +="<td colspan='1' style='text-align: right;'>Total:</td>";
                    html +="<td colspan='3'>$"+data.total+"</td>";
                    html +="</tr>";

                    jQuery(".moo_cart_total").remove();
                    jQuery(".moo_cart .CartContent>table").append(html);
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

    if(Object.keys(MOO_CART).length>0)
    {
        moo_updateCartTotal();
    }
    else
    {
        jQuery(".moo_cart .CartContent>table>tbody").html('<tr><td colspan="4">Your Cart is empty</td></tr>');
        jQuery(".moo_cart_total").remove();
    }


}
function moo_emptyCart()
{
    //send delete query to server
    jQuery.post(moo_params.ajaxurl,{'action':'moo_emptycart'}, function (data) {
        if(data.status == "success"){
            moo_updateCart();
        };
    });
}

function moo_addModifiers(event,item_name,item_uuid,item_price)
{
    jQuery.post(moo_params.ajaxurl,{'action':'moo_check_item_modifiers',"item":item_uuid}, function (data) {
        if(data.status == 'success')
        {
            var required_modifiers_groups = "";
            required_modifiers_groups = data.uuids.split(';');
            var selected_modifies = jQuery("#moo_form_modifiers").serializeArray();
            var Mgroups = {};
            var Modifiers = [];
            jQuery.magnificPopup.close();
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
                    if(item == '' || modifierGroup == '' || modif == '') continue;
                    if(typeof Mgroups[modifierGroup] === 'undefined') Mgroups[modifierGroup] = 1;
                    else Mgroups[modifierGroup] +=1;
                    var modifier = {
                        "item":item,
                        "modifier": modif,
                        "modifierGroup": modifierGroup
                    };
                    Modifiers.push(modifier);
                }
                else
                {
                    var name = modifier.name; //the format is : moo_modifiers['item','modifierGroup']
                    var string = name.split(','); // the new format is a table
                    var item = string[0].substr(15);  // 15 is the length of moo_modifiers['
                    item = item.substr(0,item.length-1); // remove the last '
                    var modifierGroup = string[1].substr(1);
                    modifierGroup = modifierGroup.substr(0,modifierGroup.length-2);
                    var modif = modifier.value;
                    if(item == '' || modifierGroup == '' || modif == '') continue;
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
            if(Object.keys(Mgroups).length == 0 && Object.keys(required_modifiers_groups).length <= 1) {
                jQuery.magnificPopup.close();
                moo_cartv3_addtocart(item_uuid,item_name);
                return false;
            }

            /* verify if required modifier Groups are chooses */
            for(mg in required_modifiers_groups){
                var element = required_modifiers_groups[mg];
                if(element != "")
                {
                    if(Object.keys(Mgroups).indexOf(element) == -1)
                    {
                        swal({ title: "Error!", text: "You didn't choose all required modifiers",   type: "error",   confirmButtonText: "Try again" });
                        flag=true;
                        return;
                    }
                }
            }

            /* verify the min and max in modifier Group */
            var compteur = 0;
            for(mg in Mgroups){
                jQuery.post(moo_params.ajaxurl,{'action':'moo_modifiergroup_getlimits',"modifierGroup":mg}, function (data) {
                    if(data.status == 'success' )
                    {
                        /* If the min is not null then we display a message if the custmet not choose the minimum */
                        if(data.min != null && data.min != 0 && Mgroups[data.uuid] < data.min) {
                            var error_msg = "Please choose "+data.min+" options in "+data.name;
                            swal({ title: "Error!", text: error_msg,   type: "error",   confirmButtonText: "Try again" });
                            flag=true;
                        }
                        if(data.max!= null && data.max != 0 && Mgroups[data.uuid] > data.max) {
                            var error_msg = "You can't choose more than "+ data.max+" options in "+data.name;
                            swal({ title: "Error!", text: error_msg,   type: "error",   confirmButtonText: "Try again" });
                            flag=true;
                        }
                    }
                    else
                    {
                        flag = true;
                    }
                }).done(function () {
                    compteur++;
                    if(compteur == Object.keys(Mgroups).length)
                    {
                        if(!flag)
                        {
                            //toastr.success(item_name+' added to cart');
                            swal({ title:  item_name+' added to cart', text: 'To add special instructions to this item or change the quantity, select this item from the cart',    type: "success",   confirmButtonText: "OK" });
                            //send the request to the server
                            jQuery.post(moo_params.ajaxurl,{'action':'moo_modifier_add',"modifiers":Modifiers}, function (data) {
                                if(data.status == 'success' )
                                {
                                    moo_updateCart();
                                }
                            })
                        }
                        else
                        {
                            return 'error';
                        }

                    }
                });
            }
        }
    });
}
function moo_addItemWithModifiersToCart(event,item_uuid,item_name,item_price)
{
    event.preventDefault();
    if(moo_addModifiers(event,item_name,item_uuid,item_price)== false ){
        if(item_price==0)
        {
            swal({ title: "Error!", text: "Please choose a modifier",   type: "error",   confirmButtonText: "Try again" });
        }
    }
}
