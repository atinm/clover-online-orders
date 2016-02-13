(function( $ ) {
    'use strict';
})( jQuery );
jQuery("#moo_form_address").validate({
    rules: {
        name: {
            required: true,
            minlength: 3,
            maxlength:100
        },
        email:{
            required: true,
            email: true
        },
        phone:{
            required: true,
            minlength: 3,
            maxlength:100
        },
        zipcode:{
            required: true,
            minlength: 3,
            maxlength:100
        },
        cardNumber:{
            required: true
        },
        cvv:{
            required: true
        },
        address:{
            required: true,
            minlength: 3,
            maxlength:100
        },
        city:{
            required: true,
            minlength: 3,
            maxlength:100
        },
        instructions:{
            required: false,
            minlength: 5,
            maxlength:250
        }
    },
    messages: {
        name: {
            required: "Please enter your full name "
        },
        email: {
            required: "We need your email address to contact you"
        },
        phone: {
            required: "We need your phone to contact you"
        },
        cardNumber: {
            minlength: "Please enter at least 16 numbers",
            maxlength: "Please enter no more than 16 numbers."
        }
    },
    submitHandler: function(form) {
        jQuery('#moo_checkout_msg').remove();
        jQuery('#moo_btn_submit_order').hide();

        //Show loading Icon
        jQuery('#moo_checkout_loading').show();

        var DataArray = jQuery(form).serializeArray();
        var DataObject = {};

        for(i in DataArray) DataObject[DataArray[i]['name']] = DataArray[i]['value'] ;

        jQuery.post(moo_params.ajaxurl,{'action':'moo_checkout','form':DataObject}, function (data) {
            if(data.status == 'APPROVED'){
                html = '<div align="center" class="alert alert-success" role="alert">Thank you for your order<br> You can see your receipt <a href="https://www.clover.com/r/'+data.order+'" target="_blank">here</a></a> </div>';
               // console.log(html);
                jQuery("#moo_form_address").parent().html(html);
                jQuery("html, body").animate({
                    scrollTop: 0
                }, 600);
            }

            else
            {
                //Hide Loading Icon and Show the button if there is an error
                jQuery('#moo_checkout_loading').hide();
                jQuery('#moo_btn_submit_order').show();
                html = '<div class="alert alert-danger" role="alert" id="moo_checkout_msg"><strong>Error : </strong>'+data.message+'</div>'
                jQuery(".entry-content").prepend(html);
            }

        });
    }
});

//Validate Input credit cards
jQuery('#Moo_cardNumber').payment('formatCardNumber');
jQuery('#moo_cardcvv').payment('formatCardCVC');

function moo_OrderTypeChanged(obj)
{
    var OrderTypeID = jQuery(obj).val();
    for(i in moo_OrderTypes)
    {

        if(OrderTypeID == moo_OrderTypes[i].ot_uuid) {
           if( moo_OrderTypes[i].taxable == "1"){
               document.getElementById('moo_Total_inCheckout').innerText = '$'+(moo_Total.total);
           }
            else
               document.getElementById('moo_Total_inCheckout').innerText = '$'+(moo_Total.sub_total);
        }
    }
}
