
//Validate Input credit cards
jQuery('#Moo_cardNumber').payment('formatCardNumber');
jQuery('#moo_cardcvv').payment('formatCardCVC');

jQuery('#moo_paymentOptions_cc').iCheck({
    checkboxClass: 'icheckbox_square',
    radioClass: 'iradio_square-blue',
    increaseArea: '20%' // optional
});
jQuery('#moo_paymentOptions_cash').iCheck({
    checkboxClass: 'icheckbox_square',
    radioClass: 'iradio_square-blue',
    increaseArea: '20%' // optional
});

jQuery('#moo_paymentOptions_cc').on('ifClicked', function () {
    moo_changePaymentMethod('cc')
});
jQuery('#moo_paymentOptions_cash').on('ifClicked', function () {
    moo_changePaymentMethod('cash')
});

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
        cardNumber: {
            minlength: "Please enter at least 16 numbers",
            maxlength: "Please enter no more than 16 numbers."
        }
    },
    submitHandler: function(form) {

        var delivery_amount = document.getElementById('moo_delivery_amount').value;
        if(delivery_amount == 'ERROR')
        {
            swal({ title: "Verify your delivery details", text: 'please verify your delivery details by clicking on Calculate Delivery fee',   type: "error",   confirmButtonText: "Verify" });
        }
        else
        {
            jQuery('#moo_checkout_msg').remove();
            jQuery('#moo_btn_submit_order').hide();

            //Show loading Icon
            jQuery('#moo_checkout_loading').show();

            var DataArray = jQuery(form).serializeArray();
            var DataObject = {};

            for(i in DataArray) DataObject[DataArray[i]['name']] = DataArray[i]['value'] ;
            DataObject['cardEncrypted'] = cryptCardNumber(DataObject.cardNumber);

            //Send the Form to server
            jQuery
                .post(moo_params.ajaxurl,{'action':'moo_checkout','form':DataObject}, function (data) {
                if(typeof data == 'object')
                    if(data.status == 'APPROVED')
                        moo_order_approved(data.order);
                    else
                        if(data.status == 'REDIRECT')
                        {
                            window.location.href = data.url;
                        }
                        else
                            moo_order_notApproved(data.message);
                 else
                    if(data.indexOf('"status":"APPROVED"') != -1 )
                       moo_order_approved('');
                    else
                        moo_order_notApproved('');
                })
                .fail(function(data) {
                    console.log('FAIL');
                    if(data.responseText.indexOf('"status":"APPROVED"') != -1 )
                        moo_order_approved('');
                    else
                        moo_order_notApproved('')
                })
                .always(function(data) {
                  //alert the customer the order is procced or not
                });
        }

    }
});


function moo_OrderTypeChanged(obj)
{
    var OrderTypeID = jQuery(obj).val();
    var moo_delivery_areas = null;

    try {
        moo_delivery_areas  = JSON.parse(moo_delivery_zones);
    } catch (e) {
       // console.log("Parsing error: moo_delivery_areas");
    }
    if(!(typeof moo_OrderTypes === 'undefined'))
        for(i in moo_OrderTypes)
        {
            if(OrderTypeID == moo_OrderTypes[i].ot_uuid) {
                if( moo_OrderTypes[i].show_sa == "0"){
                    jQuery('#city').parent().hide();
                    jQuery('#address').parent().hide();
                    jQuery('#state').parent().hide();
                    jQuery('#country').parent().hide();
                    jQuery('#moo-delivery-details').hide();
                    jQuery('#moo-cart-delivery-fee').parent().hide();
                    document.getElementById('moo_delivery_amount').value = "";

                    if(moo_cash_in_store == 'on')
                    {
                        jQuery('#moo_paymentOptions_cash_div').show();
                        jQuery('#moo_paymentOptions_cash_label').text('Pay in Store');
                    }
                    else
                        jQuery('#moo_paymentOptions_cash_div').hide();

                    moo_update_totals();
                }
                else
                {
                    jQuery('#city').parent().show();
                    jQuery('#address').parent().show();
                    jQuery('#state').parent().show();
                    jQuery('#country').parent().show();
                    jQuery('#moo-cart-delivery-fee').parent().show();

                    if(moo_cash_upon_delivery == 'on')
                    {
                        jQuery('#moo_paymentOptions_cash_div').show();
                        jQuery('#moo_paymentOptions_cash_label').text('Pay upon Delivery');
                    }
                    else
                        jQuery('#moo_paymentOptions_cash_div').hide();


                    if(moo_delivery_areas != null && moo_delivery_areas.length >= 1)
                    {
                        document.getElementById('moo_delivery_amount').value = "ERROR";
                        jQuery('#moo-delivery-details').show();
                        moo_InitZones();
                        moo_calculate_delivery_fee();
                        moo_update_totals();
                    }

                }
                moo_update_totals();
            }
        }
}

function  moo_tips_select_changed()
{
    var tips_select_percent = jQuery('#moo_tips_select').val();
    if(tips_select_percent != "cash" && tips_select_percent != 'other')
        jQuery('#moo_tips').val((moo_Total.sub_total*tips_select_percent/100).toFixed(2));
    else
        if(tips_select_percent == "cash")
            jQuery('#moo_tips').val(0);
        else
            jQuery('#moo_tips').select();

    moo_change_total_with_tips();
}

function moo_tips_amount_changed()
{
    jQuery('#moo_tips').val((parseFloat(jQuery('#moo_tips').val())).toFixed(2));
    moo_change_total_with_tips();
}

function moo_change_total_with_tips()
{
    moo_update_totals();
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
function moo_verifyPhone(event)
{
    event.preventDefault();

    var phone_number=jQuery('#Moo_PhoneToVerify').val();
    jQuery('#moo_verifPhone_sending').hide();
    jQuery('#moo_verifPhone_verified').hide();
    jQuery('#Moo_VerificationCode').val('');
    jQuery('#moo_verifPhone_verificatonCode').show();
    jQuery.post(moo_params.ajaxurl,{'action':'moo_send_sms','phone':phone_number});
}
function moo_verifyCode(event)
{
    event.preventDefault();
    var code=jQuery('#Moo_VerificationCode').val();
    jQuery.post(moo_params.ajaxurl,{'action':'moo_check_verification_code','code':code}, function (data) {
        if(data.status == 'success')
        {
            jQuery('#moo_verifPhone_sending').hide();
            jQuery('#moo_verifPhone_verificatonCode').hide();
            jQuery('#moo_verifPhone_verified').css("display","inline-block");
            swal({ title: 'Phone verified', text: 'Please have your payment ready when picking up from the store and don\'t forget to finalize your order below',   type: "success",   confirmButtonText: "OK" });
        }
        else
            swal({ title: "Code invalid", text: 'this code is invalid please try again',   type: "error",   confirmButtonText: "Try again" });
    });
}
function moo_verifyCodeTryAgain(event)
{
    event.preventDefault();
    jQuery('#moo_verifPhone_sending').show();
    jQuery('#moo_verifPhone_verificatonCode').hide();
    jQuery('#moo_verifPhone_verified').hide();
}
function moo_cardNumberChanged()
{
    var card_number=jQuery('#Moo_cardNumber').val();
    var res = jQuery.payment.cardType(card_number);

}
function moo_changePaymentMethod(type)
{
    if(type=='cash')
    {
        jQuery('#moo_cashPanel').show();
        jQuery('#moo_creditCardPanel').hide();
    }
    else
    {
        jQuery('#moo_cashPanel').hide();
        jQuery('#moo_creditCardPanel').show();
    }
}
function moo_pickup_day_changed(element)
{
    var theDay = jQuery(element).val();
    var times = moo_pickup_time[theDay];
    var html  = '';

    if(!(typeof times === 'undefined'))
    {
        if(theDay =='Today')
            html += '<option value="asap">ASAP</option>';

        for(i in times)
            html += '<option value="'+times[i]+'">'+times[i]+'</option>'
    }
    else
        html = '';
   jQuery('#moo_pickup_hour').html(html);
}
function moo_order_approved(orderId)
{
    if(moo_thanks_page != '' && moo_thanks_page != null )
        window.location.href = moo_thanks_page;
    else
    {
        if(orderId == '')
            html = '<div align="center" class="alert alert-success" role="alert">Thank you for your order</div>';
        else
            html = '<div align="center" class="alert alert-success" role="alert">Thank you for your order<br> You can see your receipt <a href="https://www.clover.com/r/'+orderId+'" target="_blank">here</a></a> </div>';

        // console.log(html);
        jQuery("#moo_form_address").html('');
        jQuery("#moo_form_address").parent().prepend("<p style='font-size: 21px;'>Our Address : </p>"+moo_merchantAddress+"<br/><br/>");
        jQuery("#moo_form_address").parent().prepend(html);

        jQuery("#moo_merchantmap").show();
        moo_getLatLong();
        jQuery("html, body").animate({
            scrollTop: 0
        }, 600);
    }
}
function moo_order_notApproved(message)
{
    //Hide Loading Icon and Show the button if there is an error
    jQuery('#moo_checkout_loading').hide();
    jQuery('#moo_btn_submit_order').show();
    if(message != '')
        html = '<div class="alert alert-danger" role="alert" id="moo_checkout_msg"><strong>Error : </strong>'+message+'</div>';
    else
        html = '<div class="alert alert-danger" role="alert" id="moo_checkout_msg"><strong>Error : </strong>An error has occurred, please try again or contact us</div>';
    jQuery("#moo_form_address").prepend(html);
    jQuery("html, body").animate({
        scrollTop: 0
    }, 600);
}
moo_OrderTypeChanged(jQuery('#OrderType'));
moo_InitZones();


