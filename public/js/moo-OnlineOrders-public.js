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
    swal.setDefaults({ customClass: 'moo-custom-dialog-class' });
    jQuery( document ).ready(function($) {
        jQuery('#moo_OnlineStoreContainer').removeClass('moo_loading');
        jQuery('.demo').imagesRotation({
            interval: 1000,     // ms
            intervalFirst: 500, // first image change, ms
            callback: null});      // first argument would be the current image url
    });

})(jQuery);


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