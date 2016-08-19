jQuery(document).ready(function($) {
    //accordion
    $('.moo_accordion').accordion({defaultOpen: 'MooCat_NoCategory'});
    $('.open-popup-link').magnificPopup({
        type:'inline',
        overflowY:'scroll',
        midClick: true,
        closeBtnInside: true
    });

});

function moo_openFirstModifierG(id)
{
    jQuery('#'+id).removeClass('accordion-close');
    jQuery('#'+id).addClass('accordion-open');
    jQuery('#'+id).next().show();
};
function moo_check(event,id)
{
    event.preventDefault();
    event.stopPropagation();
    var checked =   jQuery('#moo_checkbox_'+id).prop('checked');
    jQuery('#moo_checkbox_'+id).prop("checked", !checked);

}
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

