jQuery(document).ready(function($) {
    //accordion
    $('.moo_accordion').accordion();
    $('.open-popup-link').magnificPopup({
        type:'inline',
        overflowY:'scroll',
        midClick: true,
        closeBtnInside: true
    });

});
function moo_check(event,id)
{
    event.preventDefault();
    event.stopPropagation();
    var checked =   jQuery('#moo_checkbox_'+id).prop('checked');
    jQuery('#moo_checkbox_'+id).prop("checked", !checked);
}

function moo_cartv3_addtocart(uuid,name)
{
    var qte = jQuery('#moo_popup_quantity').val();
    var special_instruction = jQuery('#moo_popup_si').val();
    jQuery.magnificPopup.close();
    //toastr.success(name+ ' added to cart');
    swal({ title: name, text: 'Added to cart',   type: "success",   confirmButtonText: "OK" });

    jQuery.post(moo_params.ajaxurl,{'action':'moo_add_to_cart',"item":uuid,"quantity":qte,"special_ins":special_instruction}, function (data) {
        if(data.status != 'success')
        {
            swal({ title: name, text: data.message,   type: "error",   confirmButtonText: "OK" });
        }
    });

}




