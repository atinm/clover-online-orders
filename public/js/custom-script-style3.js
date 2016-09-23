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

    jQuery.post(moo_params.ajaxurl,{'action':'moo_add_to_cart',"item":uuid}, function (data) {
        if(data.status == 'success')
        {
            if(qte > 1)
                jQuery.post(moo_params.ajaxurl,{'action':'moo_update_qte',"item":uuid,"qte":qte});
            if(special_instruction != '')
                jQuery.post(moo_params.ajaxurl,{'action':'moo_update_special_ins',"item":uuid,"special_ins":special_instruction});
        }
    });

}




