var MOO_CART = [];
(function( $ ) {
    'use strict';

    /*
    $('.moo_item_flip').on("mouseenter",function (e) {
       // $(".moo_item_flip_modifiers",e.currentTarget).slideDown();
        console.log(e.currentTarget);
    })
    $('.moo_item_flip').on("mouseleave",function (e) {
       // $(".moo_item_flip_modifiers",e.currentTarget).slideUp();
        console.log(e.currentTarget);
    })

    $('.moo_item_flip').on("click",function (e) {
        console.log(e);
        $.post(moo_params.ajaxurl,{'action':'moo_add_to_cart'}, function (data) {
            console.log(data);
        })
    })
    */
    //moo_item_flip
    var DivFiltere_offset = jQuery('#Moo_FileterContainer').offset();

    if(! (typeof DivFiltere_offset === 'undefined')){
        var scrollIntervalID = setInterval(stickIt, 10);

    }
    function stickIt(){
        if (jQuery(window).scrollTop() >= (DivFiltere_offset.top)) {
            jQuery('#Moo_FileterContainer').addClass('FixedOnTop');
            jQuery('#Moo_FileterContainer').removeClass('moo_items');

            jQuery('#Moo_FileterContainer').width(jQuery('#Moo_ItemContainer').outerWidth());
        } else {
            jQuery('#Moo_FileterContainer').removeClass('FixedOnTop');
            jQuery('#Moo_FileterContainer').addClass('moo_items');

        }
       // console.log("Div 0 "+jQuery('#Moo_ItemContainer').width());
        //console.log("Div 1 "+jQuery('#Moo_FileterContainer').width());
    }




})( jQuery );



function moo_addToCart(event,item,name)
{
    toastr.success(name+ ' added to cart');
    MOO_CART[item] = {uuid:item,name:name};
    jQuery.post(moo_params.ajaxurl,{'action':'moo_add_to_cart',"item":item}, function (data) {
        if(data.status != 'success')
        {
            toastr.error('Error, please try again');
        }
       // console.log(data);
    })

}

function Moo_CategoryChanged(event)
{
    jQuery('#MooSearchFor').val('');
    var cat_uuid = jQuery(event).find('option:selected').val();
    jQuery.post(moo_params.ajaxurl,{'action':'moo_getitemsfiltered',"Category":cat_uuid,"FilterBy":'Name',"Order":'asc'}, function (data) {
        jQuery('#Moo_ItemContainer').html(data);
       // jQuery(window).scrollTop(0);

    })
}
function Moo_SortBy(e,element,order)
{
    e.preventDefault();
    jQuery('#MooSearchFor').val('');
    var cat_uuid = jQuery('#ListCats').find('option:selected').val();

    jQuery.post(moo_params.ajaxurl,{'action':'moo_getitemsfiltered',"Category":cat_uuid,"FilterBy":element,"Order":order}, function (data) {
        jQuery('#Moo_ItemContainer').html(data);
       // jQuery(window).scrollTop(0);

    })
}
function Moo_Search(e)
{
    e.preventDefault();
    var moo_motCle = jQuery('#MooSearchFor').val();
    if(moo_motCle.length<=0) return;
    jQuery.post(moo_params.ajaxurl,{'action':'moo_getitemsfiltered',"Category":null,"FilterBy":null,"Order":null,"search":moo_motCle}, function (data) {
        jQuery('#Moo_ItemContainer').html(data);
       // jQuery(window).scrollTop(0);

    })
}
function Moo_ClickOnGo(e){
    if(e.keyCode==13)
        jQuery('#MooSearchButton').click();
}