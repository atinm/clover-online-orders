(function( $ ) {
    'use strict';
})( jQuery );

jQuery('.open-popup-link').magnificPopup({
    type:'inline',
    overflowY:'scroll',
    midClick: true, // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
    closeBtnInside: true
});
function moo_openFirstModifierG(id)
{
    jQuery('#'+id).removeClass('accordion-close');
    jQuery('#'+id).addClass('accordion-open');
    jQuery('#'+id).next().show();
};

function moo_addToCart(event,item,name)
{
    toastr.success(name+ ' added to cart');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_add_to_cart',"item":item}, function (data) {
        if(data.status != 'success')
        {
            toastr.error('Error, please try again');
        }
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

