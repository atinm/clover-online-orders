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
    }

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


