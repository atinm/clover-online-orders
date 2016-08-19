(function( $ ) {
	'use strict';
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-left",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "1000",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    jQuery.post(moo_params.ajaxurl,{'action':'moo_store_isopen'}, function (data) {

       if(data.status =='Success' && data.data=='close')
        {
            var store_time = {}
            try {
                store_time = JSON.parse(data.infos).store_time;
            } catch (e) {
                console.log("Parsing error: store_time");
            }
            
            if(store_time.length>0)
                var html = '<div class="alert alert-danger" role="alert" id="moo_checkout_msg"><strong>Today\'s Online Ordering hours</strong> <br/> '+store_time+'<br/>Currently Not Available - Order in Advance.</div>';
            else
                var html = '<div class="alert alert-danger" role="alert" id="moo_checkout_msg">Currently Not Available - Order in Advance.</div>';

            jQuery('#moo_OnlineStoreContainer').prepend(html);
        }
      //  jQuery('#moo_OnlineStoreContainer').prepend(html);
    });

    jQuery( document ).ready(function() {
        jQuery('#moo_OnlineStoreContainer').removeClass('moo_loading')
    });

})( jQuery );


function moo_OrderTypeChanged(ot)
{
    var OrderType = window.OrderTypes[ot.selectedIndex];
    if( ! OrderType['taxable'])
    {
        document.getElementById('moo_Total_inCheckout').innerText = '$'+window.sub_total;
    }
    else
        document.getElementById('moo_Total_inCheckout').innerText = '$'+window.total;
}

function moo_btn_addToCart(event,item,name)
{
    event.preventDefault();
    toastr.success(name+ ' added to cart');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_add_to_cart',"item":item}, function (data) {
        if(data.status != 'success')
        {
            toastr.error('Error, please try again');
        }
    })
}