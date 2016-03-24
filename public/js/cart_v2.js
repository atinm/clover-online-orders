function moo_updateCart()
{
    jQuery(".moo_cart .CartContent").html('<img src="'+moo_params.plugin_img+'/loading.gif" style="text-align: center;margin: 15px auto 0px;display: block;width: 50px;" />');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_get_cart'}, function (data) {
        //console.log(data);
        var html = ''+
                    '<table class="table"><thead>'+
                    '<tr>'+
                    '<th>Product</th>'+
                    '<th>Qty</th>'+
                    '<th colspan="2">Sub-total</th>'+
                    '</tr>'+
                    '</thead><tbody>';
        if(data.status=="success")
        {
            // console.log(data.data);
            for(item in data.data)
            {
                if(item == "") continue;

                var product = data.data[item];
                var price = (product.item.price*product.quantity/100);
                var tax = price*product.tax_rate/100;
                var subtotal = price;

                if(Object.keys(product.modifiers).length>0){

                    //line of the cart
                    html +="<tr class='warning' id='moo_cart_line_"+item+"'>";
                    html +="<td  style='cursor: pointer' onclick=\"ChangeQuantity('"+item+"')\" ><strong>"+product.item.name+"</strong></td>"; //The name of the item
                    html +='<td>'+product.quantity+'</td>'; //qty
                    html +='<td>$'+subtotal+'</td>'; //Sub total  ( price + taxes )
                    html +='<td></td>'; //Controlles Btn
                    html +='</tr>'; //Controlles Btn
                    // the Modifiers
                    for(uuid in product.modifiers){
                        var modifier = product.modifiers[uuid];
                        var modifierPrice = modifier.price/100;

                        tax += product.tax_rate*modifierPrice/100;

                        html +='<tr id="moo_cart_modifier_'+uuid+'" class="warning MooLineModifier4_'+item+'" style="font-size: 0.8em;text-align: right;">';
                        html +='<td style="text-align: right;">'+modifier.name+'</td>';
                        html +='<td></td>';
                        html +='<td>$'+modifierPrice+'</td>';
                        html +='<td style="text-align: left;"><i class="fa fa-close" style="cursor: pointer;" onclick="moo_cart_DeleteItemModifier(\''+uuid+'\',\''+item+'\')"></i></td>';
                        html +="</tr>";

                        subtotal += modifierPrice*product.quantity;
                    }
                     tax = Math.ceil(tax*100)/100;
                     total = Math.ceil((subtotal+tax)*100)/100;

                    html +='<tr class="warning MooLineModifier4_'+item+'" ><td colspan="2"></td>';
                    html +='<td id="moo_itemsubtotal_'+item+'">$'+subtotal.toFixed(2)+'</td>'; //Sub total
                    html +='<td><i class="fa fa-trash" style="cursor: pointer;" onclick="moo_cart_DeleteItem(\''+item+'\')"></i></td>'; //Controlles Btn
                    html +="</tr>";

                    //Fin line


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

            html += "</tbody></table>"
            jQuery(".moo_cart .CartContent").html(html);

           moo_updateCartTotal();


        }
        else
        {
            html += "<tr><td colspan='4'>"+data.message+"</td></tr>";
            html += "</tbody></table>";
            jQuery(".moo_cart .CartContent").html(html);
        }
    })
}
function moo_updateCartTotal()
{

    jQuery(".moo_cart_total > td:last").html("Calculating...");

    abortAJAXCalls();

    MOO_AJAX_REQS.push(jQuery.post(moo_params.ajaxurl,{'action':'moo_cart_getTotal'}, function (data) {
            if(data.status=="success")
            {
                html ="<tr class='moo_cart_total'>";
                html +="<td colspan='1' style='text-align: right;'>Subtotal:</td>";
                html +="<td colspan='3'>$"+data.sub_total.toFixed(2)+"</td>";
                html +="</tr>";

                html +="<tr  class='moo_cart_total'>";
                html +="<td colspan='1' style='text-align: right;'>Tax:</td>";
                html +="<td colspan='3'>$"+data.total_of_taxes.toFixed(2)+"</td>";
                html +="</tr>";

                html +="<tr  class='moo_cart_total'>";
                html +="<td colspan='1' style='text-align: right;'>Total:</td>";
                html +="<td colspan='3'>$"+data.total.toFixed(2)+"</td>";
                html +="</tr>";

                jQuery(".moo_cart_total").remove();
                jQuery(".moo_cart .CartContent>table").append(html);
            }
            else
            {
                moo_updateCart();
            }
    }));
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
        jQuery(".moo_cart .CartContent>table>tbody").html('<tr><td colspan="4">Your Cart is empty !!</td></tr>');
        moo_updateCartTotal();
    }

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

function moo_addModifiers(item_name)
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
        return false;
    }

    for(mg in Mgroups){
        jQuery.post(moo_params.ajaxurl,{'action':'moo_modifiergroup_getlimits',"modifierGroup":mg}, function (data) {
            if(data.status == 'success' )
            {
                if(data.min!=0 && Mgroups[mg] < data.min) {
                    //TODO
                   // alert("Minimum required is "+data.min);
                    flag=true;
                } // Message not atteind the min_required
                if(data.max!=0 && Mgroups[mg] > data.max) {
                  //  alert("Max allowed is "+ data.max);
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
                toastr.success(item_name +' added to cart');
                moo_updateCart();
                return true;
            }
        })

    }
}
function abortAJAXCalls()
{
    MOO_AJAX_REQS.forEach(function(element, index, array) {
        MOO_AJAX_REQS[index].abort();
       delete(MOO_AJAX_REQS[index]);
    });
}

function moo_addItemWithModifiersToCart(event,item_uuid,item_name,item_price)
{
    if(moo_addModifiers(item_name) == false ) moo_addToCart(event,item_uuid,item_name,item_price);
    history.back();
}
