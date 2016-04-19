(function( $ ) {
    'use strict';
    console.log(moo_Key);
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
        DataObject['cardEncrypted'] = cryptCardNumber(DataObject.cardNumber);

        jQuery.post(moo_params.ajaxurl,{'action':'moo_checkout','form':DataObject}, function (data) {
            if(data.status == 'APPROVED'){
                html = '<div align="center" class="alert alert-success" role="alert">Thank you for your order<br> You can see your receipt <a href="https://www.clover.com/r/'+data.order+'" target="_blank">here</a></a> </div>';
               // console.log(html);
                jQuery("#moo_form_address").html('');
                jQuery("#moo_form_address").parent().prepend(html);
                jQuery("#moo_merchantmap").show();
                moo_getLatLong();
                jQuery("html, body").animate({
                    scrollTop: 0
                }, 600);
            }
            else
            {
                //Hide Loading Icon and Show the button if there is an error
                jQuery('#moo_checkout_loading').hide();
                jQuery('#moo_btn_submit_order').show();
                html = '<div class="alert alert-danger" role="alert" id="moo_checkout_msg"><strong>Error : </strong>Payment card was declined. Check card info or try another card.</div>';
                console.log(data.message);
                jQuery("#moo_form_address").prepend(html);
                jQuery("html, body").animate({
                    scrollTop: 0
                }, 600);
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
            if( moo_OrderTypes[i].show_sa == "0"){
                jQuery('#city').parent().hide();
                jQuery('#address').parent().hide();
            }
            else
            {
                jQuery('#city').parent().show();
                jQuery('#address').parent().show();
            }
        }
    }
}
function cryptCardNumber(ccn)
{
    var rsa = forge.pki.rsa;

    var modulus = moo_Key.modulus;
    var exponent = moo_Key.exponent;
    var prefix = moo_Key.prefix;
    var text = prefix + ccn;
    modulus = new forge.jsbn.BigInteger(modulus);
    exponent = new forge.jsbn.BigInteger(exponent);
    text = text.split(' ').join('');
    var publicKey = rsa.setPublicKey(modulus, exponent);
    var encryptedData = publicKey.encrypt(text, 'RSA-OAEP');
    return forge.util.encode64(encryptedData);
}

moo_OrderTypeChanged(jQuery('#OrderType'));


