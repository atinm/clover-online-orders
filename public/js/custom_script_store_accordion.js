var MOO_CART = [];
var MOO_AJAX_REQS = [];
jQuery(document).ready(function() {
    //accordion

    jQuery('.moo_accordion').accordion();
    jQuery('.popup-text').magnificPopup({
        type: 'inline',
        closeBtnInside: true,
        midClick: true
    });
/*
    var cart_offset = jQuery('.moo_cart').offset();
    var cart_width = jQuery('.moo_cart').outerWidth();
   
    if(! (typeof cart_offset === 'undefined')){
        var scrollIntervalID = setInterval(stickIt, 10);
    }
    function stickIt(){
            if (jQuery(window).scrollTop() >= (cart_offset.top)) {
                jQuery('.moo_cart').addClass('Fixedthecart');
                jQuery('.moo_cart').css('left',cart_offset.left);
                jQuery('.moo_cart').css('width',cart_width);


            } else {
                jQuery('.moo_cart').removeClass('Fixedthecart');
                jQuery('.moo_cart').css('left','');
                jQuery('.moo_cart').css('width','100%');
               // jQuery('#Moo_FileterContainer').addClass('moo_items');

            }
        // console.log("Div 0 "+jQuery('#Moo_ItemContainer').width());
        //console.log("Div 1 "+jQuery('#Moo_FileterContainer').width());
    }
    jQuery( window ).resize(function() {
        jQuery('.moo_cart').css('left','');
        jQuery('.moo_cart').css('width','100%');
        cart_offset = jQuery('.moo_cart').offset();
        cart_width = jQuery('.moo_cart').outerWidth();
    });
    */

});
function moo_addToCart(e,item_uuid,name,price)
{
    e.preventDefault();

    if(MOO_CART[item_uuid])
    {
        if( MOO_CART[item_uuid].quantity<10){
                MOO_CART[item_uuid].quantity++;
                toastr.success('The quantity of ' + name+ ' updated');
        }
        else  {
            MOO_CART[item_uuid].quantity=10;
            toastr.error("You can't add more than 10 item");
        }
        jQuery('#moo_cart_line_'+item_uuid+'>td:nth-child(2)').html(MOO_CART[item_uuid].quantity);

    }
    else {
        toastr.success(name+ ' added to cart');
        addLineToHtmlCart(name,price,item_uuid);
        MOO_CART[item_uuid] = {uuid:item_uuid,name:name,quantity:1,price:price};
    }

    jQuery.post(moo_params.ajaxurl,{'action':'moo_add_to_cart',"item":item_uuid}, function (data) {
        if(data.status != 'success')
        {
            toastr.error('Error, please try again');
        }
        // console.log(data);
    }).done(function() {
        moo_updateCartTotal();
        //console.log(MOO_CART);
    })
}
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
function ItemHasModifiers(element,event,item_uuid,item_name,item_price)
{
    event.preventDefault();
    jQuery('#Moo_ItemWithModifierContainer').html('Loading ...');
    var html = "<h4>Sorry there is no Modifier</h4>";

    jQuery.post(moo_params.ajaxurl,{'action':'moo_getitemmodifiers',"item_uuid":item_uuid}, function (data) {
        if(data)
        {
            html = data;
            jQuery('#Moo_ItemWithModifierContainer').html(html);
        }
    });
}
function ChangeQuantity(item_uuid)
{
    var html = 'New quantity : <input id="MooQteForChange" class="form-control" type="text"/> '
    jQuery.fn.SimpleModal({btn_ok: 'Change', title: 'Change the quantity', contents: html,"model":"confirm",
        "callback": function(){
           if(jQuery('#MooQteForChange').val()>0 && jQuery('#MooQteForChange').val()<=10 )
            jQuery.post(moo_params.ajaxurl,{'action':'moo_update_qte',"item":item_uuid,"qte":jQuery('#MooQteForChange').val()}, function (data) {
                if(data.status == 'success')
                {
                    toastr.warning("Updating the quantity...");
                }
            }).done(function(e){
                moo_updateCart();
                setTimeout(function(){toastr.success("The quantity updated")},2000)

            });
            else
               toastr.error('The quantity should be between 1 and 10');
        }}).showModal();
}
function clickLineInModifiersTab(target)
{
        var tr_table = jQuery(target).parent();
        jQuery("td:first input",tr_table).each(function() {
            jQuery(this).prop("checked", !jQuery(this).prop("checked"));
        });

}
moo_updateCart();
//jQuery('.CartContent>table>tbody').css('max-height',"100px");

