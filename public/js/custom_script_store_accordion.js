var MOO_CART = [];

jQuery(document).ready(function() {

    //accordion
    jQuery('.moo_accordion').accordion({defaultOpen: 'MooCat_NoCategory',cookieName:"MooCategories"});
    
    jQuery('.popup-text').magnificPopup({
        type: 'inline',
        closeBtnInside: true,
        midClick: true,
        overflowY:'scroll'
    });

});
function moo_addToCart(e,item_uuid,name,price)
{
    e.preventDefault();
    if(MOO_CART[item_uuid])
    {
        MOO_CART[item_uuid].quantity++;
        swal({ title: name+"'s", text: 'quantity updated',   type: "success",   confirmButtonText: "OK" });
        jQuery('#moo_cart_line_'+item_uuid+'>td:nth-child(2)').html(MOO_CART[item_uuid].quantity);
        jQuery('#moo_cart_line_'+item_uuid+'>td:nth-child(3)').html('$'+(parseInt(MOO_CART[item_uuid].quantity)*price/100).toFixed(2));

    }
    else {
        swal({ title: name, text: 'Added to cart',   type: "success",   confirmButtonText: "OK" });
        addLineToHtmlCart(name,price,item_uuid);
        MOO_CART[item_uuid] = {uuid:item_uuid,name:name,quantity:1,price:price};
    }

    jQuery.post(moo_params.ajaxurl,{'action':'moo_add_to_cart',"item":item_uuid}, function (data) {
        if(data.status != 'success')
        {
            swal({ title: "Error", text: 'please try again',   type: "error",   confirmButtonText: "Try again" });
        }
        // console.log(data);
    }).done(function() {
        moo_updateCartTotal();
        //console.log(MOO_CART);
    })
}
// Add one line To the Cart just html code )
function addLineToHtmlCart(item_name,item_price,item_uuid)
{
    var price = item_price/100
    html ="<tr id='moo_cart_line_"+item_uuid+"'>";
    html +="<td style='cursor: pointer' onclick=\"ChangeQuantity('"+item_uuid+"')\"><strong>"+item_name+"</strong></td>"; //The name of the item
    html +="<td>"+1+"</td>"; // The quantiy
    html +='<td id="moo_itemsubtotal_'+item_uuid+'">$'+price.toFixed(2)+'</td>'; //Sub total  ( price + taxes )
    html +='<td><i class="fa fa-trash" style="cursor: pointer;" onclick="moo_cart_DeleteItem(\''+item_uuid+'\')"></i></td>'; //Controlles Btn
    html +="</tr>";
    if(Object.keys(MOO_CART).length>0) //jQuery(html).insertBefore(".moo_cart_total:first");
        jQuery(".moo_cart .CartContent>table>tbody").prepend(html);
    else jQuery(".moo_cart .CartContent>table>tbody").html(html);

}

// Change the quantity ans spesilat instruction for one item from the POPUP
function ChangeQuantity(item_uuid)
{
    var currentQte='';
    var currentIns='';

    jQuery.post(moo_params.ajaxurl,{'action':'moo_get_item_options',"item":item_uuid}, function (data) {
        if(data.status == 'success')
        {
            currentQte = data.quantity;
            currentIns = data.special_ins;
            jQuery('#MooQteForChange').val(data.quantity);
            if(data.special_ins != '')
                jQuery('#MooItemSpecialInstructions').val(data.special_ins)

        }
    });

    var html  = 'New quantity : <input id="MooQteForChange" class="form-control" type="text"/> <br/> ';
        html += 'Special Requests : <input id="MooItemSpecialInstructions" class="form-control" type="text" placeholder="Enter any special requests for this item"/> ';
    jQuery.fn.SimpleModal({btn_ok: 'Change', title: 'More options', contents: html,"model":"confirm",
        "callback": function(){
                                var new_qte = jQuery('#MooQteForChange').val();
                                var new_ins = jQuery('#MooItemSpecialInstructions').val();

                               if(new_qte>0 && new_qte != currentQte )
                                jQuery.post(moo_params.ajaxurl,{'action':'moo_update_qte',"item":item_uuid,"qte":new_qte}, function (data) {
                                    if(data.status == 'success')
                                    {
                                        toastr.warning("Updating the quantity...");

                                    }
                                }).done(function(e){
                                    moo_updateCart();
                                    swal({ title: "Quantity updated", text: '',   type: "success",   confirmButtonText: "OK" });

                                });
                                else
                                   if(new_qte != currentQte)
                                        swal({ title: "Error", text: 'The quantity should be more than 1',   type: "error",   confirmButtonText: "Try again" });

                                if(new_ins != currentIns )
                                    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_special_ins',"item":item_uuid,"special_ins":new_ins}, function (data) {
                                        if(data.status == 'success')
                                        {
                                            swal({ title: "Special Instructions updated", text: '',   type: "success",   confirmButtonText: "OK" });
                                        }
                                    });

                               }
    }).showModal();
}
function moo_check(event,id)
{
    event.preventDefault();
    event.stopPropagation();
    var checked =   jQuery('#moo_checkbox_'+id).prop('checked');
    jQuery('#moo_checkbox_'+id).prop("checked", !checked);
    
}
function moo_openFirstModifierG(id)
{
    jQuery('#'+id).removeClass('accordion-close');
    jQuery('#'+id).addClass('accordion-open');
    jQuery('#'+id).next().show();
}

moo_updateCart();


