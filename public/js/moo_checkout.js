var MooCustomer = null;
var MooCustomerAddress = null;
var MooCustomerChoosenAddress = null;
var MooDeliveryfees = null;
var MooServicefees = null; // Payment using saved creditcard fees
var MooIsGuest = false;
var MooIsDisabled;
var MooPhoneIsVerified = false;
var MooOrderTypeIsTaxable = true;
var MooOrderTypeMinAmount = 0;
var MooIsDeliveryError = true;
var MooIsDeliveryOrder = false;
var MooPhoneVerificationActivated = true;

if(moo_use_sms_verification === 'disabled') {
     MooPhoneVerificationActivated = false;
}


if(typeof moo_checkout_login !== undefined)
{
    MooIsDisabled =(moo_checkout_login === "disabled");
} else {
    MooIsDisabled = true;
}

if(typeof moo_save_cards !== undefined)
{
    MooSaveCards =(moo_save_cards === "enabled");
} else {
    MooSaveCards = false;
}

if(typeof moo_save_cards_fees !== undefined)
{
    MooSaveCardsFees =(moo_save_cards_fees === "enabled");
} else {
    MooSaveCardsFees = false;
}

if(!MooIsDisabled && MooSaveCards && !MooIsGuest)
{
    SpreedlyExpress.init("8NzLQzLF0w60ZmZ1upQaMyh3y6A",{
        "company_name": "Smart Online Order",
        "sidebar_top_description": "Payment secured via Spreedly"
    });
}

if(typeof moo_fb_app_id !== undefined && moo_fb_app_id !== null)
{
    if(moo_fb_app_id !== "")
    {
        window.fbAsyncInit = function() {
            FB.init({
                appId      : moo_fb_app_id,
                xfbml      : true,
                version    : 'v2.8'
            });
            FB.AppEvents.logPageView();
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    }
}

try {

    jQuery('.moo-checkout-form-ordertypes-input').iCheck({
        checkboxClass: 'icheckbox_square',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });
    jQuery('.moo-checkout-form-payments-input').iCheck({
        checkboxClass: 'icheckbox_square',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });
    jQuery('.moo-checkout-form-savecard-input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });

    jQuery('.moo-checkout-form-ordertypes-input').on('ifClicked', function (event) {
        var OrderTypeID = jQuery(event.target).val();
        moo_OrderTypeChanged(OrderTypeID);
    });

    jQuery('.moo-checkout-form-payments-input').on('ifClicked', function (event) {
        var paymentType = jQuery(event.target).val();
        moo_changePaymentMethod(paymentType)
    });
}
catch (e) {
    jQuery('.moo-checkout-form-ordertypes-input').on('click', function (event) {
        var OrderTypeID = jQuery(event.target).val();
        moo_OrderTypeChanged(OrderTypeID);
    });

    jQuery('.moo-checkout-form-payments-input').on('click', function (event) {
        var paymentType = jQuery(event.target).val();
        moo_changePaymentMethod(paymentType)
    });
}

var hash = window.location.hash;
if (hash != "") {
   // console.log(hash);
    switch (hash) {
        case "#register":
            moo_show_sigupform();
            break;
        case "#forget-password":
            moo_show_forgotpasswordform();
            break;
        case "#login":
            moo_show_loginform();
            break;
    }
}

if(!MooPhoneVerificationActivated) {
    MooPhoneIsVerified = true;
}

function moo_OrderTypeChanged(OrderTypeID)
{
    if(!(typeof moo_OrderTypes === 'undefined')) {
        for(i in moo_OrderTypes)
        {
            if(OrderTypeID == moo_OrderTypes[i].ot_uuid)
            {
                var selectedOrderType = moo_OrderTypes[i];

                if(selectedOrderType.show_sa == "1") //The order type is delivery type
                {
                    MooIsDeliveryOrder = true;
                    //Change the order date
                    moo_ChangeOrderDate('delivery');

                    jQuery('#MooDeliveryfeesInTotalsSection').show();
                    if(MooCustomerChoosenAddress != null)
                    {

                        var html ='<strong>Delivery to:</strong><br />';
                        var address_string="";

                        if(MooCustomerChoosenAddress.address != '')
                            address_string += MooCustomerChoosenAddress.address+' ';
                        if(MooCustomerChoosenAddress.line2 != '')
                            address_string += MooCustomerChoosenAddress.line2+' ';
                        if(MooCustomerChoosenAddress.city != '')
                            address_string += MooCustomerChoosenAddress.city+', ';
                        if(MooCustomerChoosenAddress.state != '')
                            address_string += MooCustomerChoosenAddress.state+' ';
                        if(MooCustomerChoosenAddress.zipcode != '')
                            address_string += MooCustomerChoosenAddress.zipcode;
                        html += address_string;

                        html += '<br/>';
                        html += '<div id="mooDeliveryAmountInformation"></div>';
                        html += '<br/>';
                        html += '<a class="MooSimplButon" href="#" onclick="moo_show_chooseaddressform(event)">Edit address</a>';


                        jQuery('#moo-checkout-form-ordertypes>.moo-checkout-bloc-message').html(html);
                        jQuery('#moo-checkout-form-ordertypes>.moo-checkout-bloc-message').show();
                        moo_calculate_delivery_fee(MooCustomerChoosenAddress.lat,MooCustomerChoosenAddress.lng,moo_update_delivery_amount);
                    }
                    else
                    {
                        var html ='<strong>No address selected</strong><br /><br />';
                        html += '<a href="#" role="button" tabindex="0" class="MooSimplButon" onclick="moo_show_chooseaddressform(event)">Add/Edit address</a>';
                        jQuery('#moo-checkout-form-ordertypes>.moo-checkout-bloc-message').html(html);
                        jQuery('#moo-checkout-form-ordertypes>.moo-checkout-bloc-message').show();
                        MooDeliveryfees = 0.00;
                    }

                    if(moo_cash_upon_delivery!="on")
                    {
                        jQuery("#moo-checkout-form-payments-cash").parent().parent().hide();
                        jQuery('input[name="payments"]:checked').val('creditcard');
                    }
                    else
                    {
                        jQuery("#moo-checkout-form-payments-cash").parent().parent().show();
                        jQuery("#moo-checkout-form-payincash-label").text('Pay upon Delivery');
                    }
                }
                else
                {
                    MooIsDeliveryOrder = false;
                    //Change the order date
                    moo_ChangeOrderDate('pickup');

                    jQuery('#moo-checkout-form-ordertypes>.moo-checkout-bloc-message').hide();
                    jQuery('#MooDeliveryfeesInTotalsSection').hide();
                    MooDeliveryfees = 0;

                    if(moo_cash_in_store != "on")
                    {
                        jQuery("#moo-checkout-form-payments-cash").parent().parent().hide();
                        jQuery('input[name="payments"]:checked').val('creditcard');
                    }
                    else
                    {
                        jQuery("#moo-checkout-form-payments-cash").parent().parent().show();
                        jQuery("#moo-checkout-form-payincash-label").text('Pay at location');
                    }
                }

                if(selectedOrderType.taxable == "1") {
                    MooOrderTypeIsTaxable = true;
                } else {
                    MooOrderTypeIsTaxable = false;
                }

                if(selectedOrderType.minAmount != "0") {
                    MooOrderTypeMinAmount = selectedOrderType.minAmount;
                } else {
                    MooOrderTypeMinAmount = 0;
                }
                if(selectedOrderType.use_coupons == "0") {
                    // MooOrderTypeMinAmount = selectedOrderType.minAmount;
                    jQuery("#moo-checkout-form-coupon").hide();
                    jQuery
                        .post(moo_params.ajaxurl,{'action':'moo_coupon_remove'}, function (data) {
                            if(data.status=="success")
                            {
                                moo_Total = data.total;
                                jQuery("#moo_remove_coupon_code").html("");
                                jQuery('#moo_coupon').val('');
                                jQuery("#moo_enter_coupon").show();
                                jQuery("#moo_remove_coupon").hide();
                                moo_update_totals();
                            }
                        })
                } else {
                    jQuery("#moo-checkout-form-coupon").show();
                }


                moo_update_totals();
            }
        }
    }
}

function  moo_tips_select_changed() {
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
function firstSix(ccn)
{
    var cardNumber = ccn.split(' ').join('').trim();
    return cardNumber.substr(0,6);
}
function lastFour(ccn)
{
    var cardNumber = ccn.split(' ').join('').trim();
    return cardNumber.substr(-4);
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
    jQuery('#Moo_VerificationCode').focus();
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
            swal({ title: 'Phone verified', text: 'Please have your payment ready when picking up from the store and don\'t forget to finalize your order below',   type: "success",timer:5000,   confirmButtonText: "OK" });
            if(MooCustomer != null) {
                MooCustomer[0].phone_verified = '1';
            }
            MooPhoneIsVerified = true;
            jQuery('#MooContactPhone').prop("readonly",true);
        } else {
            swal({ title: "Code invalid", text: 'this code is invalid please try again',   type: "error",timer:5000,   confirmButtonText: "Try again" });
            setTimeout(function () {
                jQuery('#Moo_VerificationCode').focus();
            },500)
        }
    });
}

function moo_verifyCodeTryAgain(event)
{
    event.preventDefault();
    jQuery('#moo_verifPhone_sending').show();
    jQuery('#moo_verifPhone_verificatonCode').hide();
    jQuery('#moo_verifPhone_verified').hide();
    jQuery('#Moo_PhoneToVerify').focus();
}


function moo_changePaymentMethod(type)
{
    if(type == 'cash')
    {
        //Hide the tips
        jQuery('#moo-checkout-form-tips').hide();
        jQuery('#MooTipsInTotalsSection').hide();
        jQuery('#moo-checkout-form-savecard').hide();
        if(document.getElementById('moo_tips') != null)
        {
            jQuery('#moo_tips_select').val('cash');
            jQuery('#moo_tips').val('0');
        }
        if(MooCustomer != null)
        {
            if(MooCustomer[0].phone_verified == '0')
            {
                if(MooCustomer != null)
                    jQuery('#Moo_PhoneToVerify').val(MooCustomer[0].phone);
                if(MooPhoneVerificationActivated)
                {
                    jQuery('#moo_cashPanel').show();
                }
            }
        } else {
            if(MooPhoneVerificationActivated) {
                jQuery('#moo_cashPanel').show();
            }
        }
        jQuery('#moo_creditCardPanel').hide();
    }
    else
    {
        jQuery('#moo-checkout-form-tips').show();
        jQuery('#MooTipsInTotalsSection').show();

        jQuery('#moo_cashPanel').hide();

        if(!(!MooIsDisabled && MooSaveCards && !MooIsGuest))
        {
            jQuery('#moo_creditCardPanel').show();
            jQuery('#moo-checkout-form-savecard').hide();

        }
        else {

            jQuery('#moo-checkout-form-savecard').show();
        }

    }
    MooServicefees = 0;
    moo_update_totals();
}

function moo_pickup_day_changed(element)
{
    var theDay = jQuery(element).val();

    if(MooIsDeliveryOrder)
        var times = moo_pickup_time_for_delivery[theDay];
    else
        var times = moo_pickup_time[theDay];

    var html  = '';

    if(!(typeof times === 'undefined'))
    {
        for(i in times)
            html += '<option value="'+times[i]+'">'+times[i]+'</option>'
    }
    else
        html = '';
   jQuery('#moo_pickup_hour').html(html);
}
function moo_ChangeOrderDate(type)
{
    var dayInput      = jQuery('#moo_pickup_day');
    var hoursInput    = jQuery('#moo_pickup_hour');
    var theDay        = '';
    var html_days = '';
    var html_hours  = '';

    if(type == 'pickup' )
    {
        if(!moo_pickup_time){
            return;
        }
        var first = true;
        for(var i in moo_pickup_time)
        {
            if(first) {
                theDay = i;
                first = false;
            }
            html_days += '<option value="'+i+'">'+i+'</option>';
        }
        var times = moo_pickup_time[theDay];

    } else {
        if(!moo_pickup_time_for_delivery){
            return;
        }
        var first = true;
        for(var i in moo_pickup_time_for_delivery)
        {
            if(first)
            {
                theDay = i;
                first = false;
            }
            html_days += '<option value="'+i+'">'+i+'</option>';
        }
        var times = moo_pickup_time_for_delivery[theDay];

    }

    if(!(typeof times === 'undefined'))
    {
        for(i in times)
            html_hours += '<option value="'+times[i]+'">'+times[i]+'</option>'
    }
    else
        html_hours = '';

   hoursInput.html(html_hours);
   dayInput.html(html_days);
}

function moo_order_approved(orderId)
{
    if(moo_thanks_page != '' && moo_thanks_page != null ) {
        window.location.href = moo_thanks_page+'?order_id='+orderId;
    } else {
        if(orderId == '')
            html = '<div align="center" class="moo-alert moo-alert-success" role="alert" style="font-size: 20px;">Thank you for your order<br/>Your order is being prepared</div>';
        else
            html = '<div align="center" class="moo-alert moo-alert-success" role="alert"  style="font-size: 20px;" >Thank you for your order<br/>Your order is being prepared<br> You can see your receipt <a href="https://www.clover.com/r/'+orderId+'" target="_blank">here</a></a> </div>';

        // console.log(html);
        jQuery("#moo-checkout").html('');
        jQuery("#moo-checkout").parent().prepend("<p style='font-size: 21px;'>Our Address : </p>"+moo_merchantAddress+"<br/><br/>");
        jQuery("#moo-checkout").parent().prepend(html);
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
    if(message != '' && message != "undefined")
        html = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg"><strong>Error : </strong>'+message+'</div>';
    else
        html = '<div class="moo-alert moo-alert-danger" role="alert" id="moo_checkout_msg"><strong>Error : </strong>An error has occurred, please try again or contact us</div>';
    jQuery("#moo-checkout").parent().prepend(html);
    jQuery("html, body").animate({
        scrollTop: 0
    }, 600);
}

//moo_InitZones();

function moo_show_sigupform(e)
{
    if(e !== undefined)
        e.preventDefault();
    jQuery('#moo-login-form').hide();
    jQuery('#moo-signing-form').show();
    jQuery('#moo-forgotpassword-form').hide();
    jQuery('#moo-chooseaddress-form').hide();
}

function moo_show_loginform()
{
   // e.preventDefault();
    jQuery('#moo-login-form').show();
    jQuery('#moo-signing-form').hide();
    jQuery('#moo-forgotpassword-form').hide();
    jQuery('#moo-chooseaddress-form').hide();
    jQuery('#moo-addaddress-form').hide();
    jQuery('#moo-checkout-form').hide();

}
function moo_show_forgotpasswordform(e)
{
    if(e !== undefined)
        e.preventDefault();

    jQuery('#moo-login-form').hide();
    jQuery('#moo-signing-form').hide();
    jQuery('#moo-forgotpassword-form').show();
    jQuery('#moo-chooseaddress-form').hide();
    jQuery('#moo-addaddress-form').hide();
    jQuery('#moo-checkout-form').hide();
}
function moo_show_form_adding_address()
{
    jQuery('#inputMooAddress').val('');
    jQuery('#inputMooCity').val('');
    jQuery('#inputMooState').val('');
    jQuery('#inputMooZipcode').val('');
    jQuery('#inputMooLat').val('');
    jQuery('#inputMooLng').val('');
    jQuery('#MooMapAddingAddress').hide();
    jQuery('#mooButonAddAddress').hide();
    jQuery('#mooButonChangeAddress').hide();

    jQuery('#moo-login-form').hide();
    jQuery('#moo-signing-form').hide();
    jQuery('#moo-forgotpassword-form').hide();
    jQuery('#moo-chooseaddress-form').hide();
    jQuery('#moo-addaddress-form').show();
    jQuery('#moo-checkout-form').hide();

    jQuery(".mooFormAddingAddress").show();

    setTimeout(function () {
        jQuery('#inputMooAddress').focus();
    },500);

}

function moo_show_chooseaddressform(e)
{
    if(typeof e !== "undefined")
    {
        e.preventDefault();
        e.stopPropagation();
    }

    var addresses = null;
    var cards = null;
    if(MooIsGuest || MooIsDisabled)
    {
        MooCustomerAddress = null;
        MooCustomer        = null;
        moo_show_form_adding_address();
    }
    else
    {
        jQuery('#moo-chooseaddress-formContent').html('<p style="text-align:center">Loading your addresses</p>');
        jQuery('#moo-login-form').hide();
        jQuery('#moo-signing-form').hide();
        jQuery('#moo-forgotpassword-form').hide();
        jQuery('#moo-chooseaddress-form').show();
        jQuery('#moo-addaddress-form').hide();
        jQuery('#moo-checkout-form').hide();


        jQuery
            .post(moo_params.ajaxurl,{'action':'moo_customer_getAddresses'}, function (data) {
                if(data.status == 'success')
                {
                    addresses =  data.addresses;
                    cards = data.cards;
                    MooCustomerAddress = addresses;
                    MooCustomer = data.customer;

                    if(MooCustomer[0]!== undefined && MooCustomer[0].phone_verified == "1")
                        MooPhoneIsVerified = true;

                    if(addresses.length>0) {
                        var html="";
                        if(addresses.length == 1)
                        {
                            var OneAddress = addresses[0];
                            html +='<div class="moo-col-md-4 moo-col-md-offset-4">';
                            html +='<div class="moo-address-block">';
                            html +='<span title="delete this address" onclick="moo_delete_address(event,'+OneAddress.id+')">X</span>';
                            html +=OneAddress.address+'<br />';
                            html +=OneAddress.line2;
                            html +=OneAddress.city+', '+OneAddress.state+' '+OneAddress.zipcode+'<br />';
                            html +='<a class="MooSimplButon MooUseAddressButton" href="#" onclick="moo_useAddress(event,'+OneAddress.id+')">USE THIS ADDRESS</a>';
                            html +='</div></div>';
                        }
                        else
                        {
                            for(i in addresses)
                            {
                                var OneAddress = addresses[i];
                                html +='<div class="moo-col-md-4 ">';
                                html +='<div class="moo-address-block">';
                                html +='<span title="delete this address" onclick="moo_delete_address(event,'+OneAddress.id+')">X</span>';
                                html +=OneAddress.address+'<br />';
                                html +=OneAddress.line2;
                                html +=OneAddress.city+', '+OneAddress.state+' '+OneAddress.zipcode+'<br />';
                                html +='  <a class="MooSimplButon MooUseAddressButton" href="#" onclick="moo_useAddress(event,'+OneAddress.id+')">USE THIS ADDRESS</a>';
                                html +='</div></div>';
                            }
                        }
                        //Display addresses
                        jQuery('#moo-chooseaddress-formContent').html(html);
                    }
                    else
                        moo_show_form_adding_address();

                    if(cards!= null && cards.length > 0)
                        mooShowSavedCards(cards);
                } else {
                    if(data.status == 'expired') {
                        MooCustomerAddress = null;
                        MooCustomer = null;
                        swal({ title: "Your session is expired", type: "error",timer:5000,   confirmButtonText: "Login again" });
                        moo_show_loginform();
                    }
                }
            })
            .fail(function(data) {
                MooCustomerAddress = null;
                MooCustomer        = null;
                swal({ title: "Your session is expired", type: "error",timer:5000,   confirmButtonText: "Login again" });
                moo_show_loginform();
            });
    }

}

function moo_login(e)
{
    e.preventDefault();
    jQuery(e.target).html('<i class="fas fa-circle-notch fa-spin"></i>').attr('onclick','');

    MooIsGuest = false;
    var email    =  jQuery('#inputEmail').val();
    var password =  jQuery('#inputPassword').val();
    if(email == '') {
        swal({ title: "Please enter your email",text:"",  timer:5000, type: "error" });
        jQuery(e.target).html('Login In').attr('onclick','moo_login(event)');
        return;
    }
    if(password == '') {
        swal({ title: "Please enter your password",text:"",  timer:5000, type: "error"});
        jQuery(e.target).html('Login In').attr('onclick','moo_login(event)');
        return;
    }
    jQuery
        .post(moo_params.ajaxurl,{'action':'moo_customer_login','email':email,"password":password}, function (data) {
            jQuery(e.target).html('Log In').attr('onclick','moo_login(event)');
            if(data.status == 'success')
            {
                moo_show_chooseaddressform(e);
            }
            else
                swal({ title: "Invalid User Name or Password",text:"Please click on forgot password or Please register as new user.",   type: "error",timer:5000,   confirmButtonText: "Try again" });
        })
        .fail(function(data) {
            console.log(data.responseText);
            swal({ title: "Invalid User Name or Password",text:"Please click on forgot password or Please register as new user.",   type: "error",timer:5000,   confirmButtonText: "Try again" });
            jQuery(e.target).html('Login In').attr('onclick','moo_login(event)');

        });
}

function moo_loginAsguest(e)
{
    MooIsGuest = true;
    e.preventDefault();
    moo_checkout_form();
}

function moo_loginViaFacebook(e)
{
    e.preventDefault();
    FB.login(function(response) {

        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            FB.api('/me',{fields: 'email,name,gender'}, function(response) {
                if(typeof response.email ==='undefined')
                {
                    swal("You didn't authorised to get your email",'Your email is mandatory, we use it to send you the receipt','error');
                    return;
                }
                jQuery
                    .post(moo_params.ajaxurl,{'action':'moo_customer_fblogin','email':response.email,"name":response.name,"fbid":response.id,"gender":response.gender}, function (data) {
                        if(data.status == 'success')
                        {
                            MooIsGuest = false;
                            moo_show_chooseaddressform(e);
                        }
                        else
                            swal({ title: "An error has occurred, Please try again",text:"",   type: "error",   confirmButtonText: "Try again" });
                    })
                    .fail(function(data) {
                        console.log(data.responseText);
                        swal({ title: "An error has occurred, Please try again",text:"",   type: "error",   confirmButtonText: "Try again" });
                    });
            });

        } else if (response.status === 'not_authorized') {
            // The person is logged into Facebook, but not your app.
            console.log(response);
        } else {
            // The person is not logged into Facebook, so we're not sure if
            // they are logged into this app or not.
            console.log(response);
        }
    }, {scope: 'public_profile,email'});
}

function moo_signin(e)
{
    e.preventDefault();
    var title     = "";
    var full_name = jQuery('#inputMooFullName').val();
    var email     = jQuery('#inputMooEmail').val();
    var phone     = jQuery('#inputMooPhone').val();
    var password  = jQuery('#inputMooPassword').val();
    var  regex_email =  /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(email=='')
    {
        swal("Please enter your email");
        return;
    }
    if(! regex_email.test(email))
    {
        swal("Please enter a valid email");
        return;
    }

    if(password=='')
    {
        swal("Please enter your password");
        return;
    }
    if(phone=='')
    {
        swal("Please enter your phone");
        return;
    }
    jQuery(e.target).html('<i class="fas fa-circle-notch fa-spin"></i>').attr('onclick','');
    jQuery
        .post(moo_params.ajaxurl,{'action':'moo_customer_signup','title':title,'full_name':full_name,'phone':phone,'email':email,"password":password}, function (data) {
            if(data.status == 'success')
            {
                jQuery(e.target).html('Register').attr('onclick','moo_signin(event)');
                moo_show_chooseaddressform(e);
            }
            else
            {
                jQuery(e.target).html('Register').attr('onclick','moo_signin(event)');

                swal({ title: "Invalid Email",text:"Please click on forgot password or enter a new email",   type: "error",   confirmButtonText: "Try again" });
            }
        })
        .fail(function(data) {
            console.log(data.responseText);
            jQuery(e.target).html('Register').attr('onclick','moo_signin(event)');
            swal({ title: "Invalid User Name or Password",text:"Please click on forgot password or Please register as new user.",   type: "error",   confirmButtonText: "Try again" });
        });
}

function moo_resetpassword(e)
{
    e.preventDefault();
    var email     = jQuery('#inputEmail4Reset').val();
    if(email=='')
        swal('Please enter your email');
    else
    {
        jQuery(e.target).html('<i class="fas fa-circle-notch fa-spin"></i>').attr('onclick','');
        jQuery
            .post(moo_params.ajaxurl,{'action':'moo_customer_resetpassword','email':email}, function (data) {
                if(data.status == 'success')
                {
                    jQuery(e.target).html('Reset').attr('onclick','moo_resetpassword(event)');
                    swal("If the e-mail you specified exists in our system, then you will receive an e-mail shortly to reset your password.");
                    moo_show_loginform();
                }
                else
                {
                    jQuery(e.target).html('Reset').attr('onclick','moo_resetpassword(event)');
                    swal({ title: "could not reset your password",text:"Please try again or contact us",   type: "error",   confirmButtonText: "Try again" });
                }
            })
            .fail(function(data) {
                console.log(data.responseText);
                jQuery(e.target).html('Reset').attr('onclick','moo_resetpassword(event)');
                swal({ title: "could not reset your password",text:"Please try again or contact us",   type: "error",   confirmButtonText: "Try again" });
            });
    }
}
function moo_cancel_resetpassword(e)
{
    e.preventDefault();
    moo_show_loginform();
}

function moo_initMapAddress()
{
    var Merchantlocation = {};
    Merchantlocation.lat = parseFloat(document.getElementById("inputMooLat").value);
    Merchantlocation.lng = parseFloat( document.getElementById("inputMooLng").value);
    var map = new google.maps.Map(document.getElementById('MooMapAddingAddress'), {
        zoom: 16,
        center: Merchantlocation
    });

    var marker = new google.maps.Marker({
        position: Merchantlocation,
        map: map,
        icon:{
            url:moo_params['plugin_img']+'/moo_marker.png'
        },
        draggable:true
    });
    google.maps.event.addListener(marker, 'drag', function() {
        moo_updateMarkerPosition(marker.getPosition());
    });
    var infowindow = new google.maps.InfoWindow({
        content: "Drag&Drop to change the location"
    });
    infowindow.open(map,marker);
}

function moo_updateMarkerPosition(newPosition)
{
    jQuery('#inputMooLat').val(newPosition.lat());
    jQuery('#inputMooLng').val(newPosition.lng());
}

function moo_ConfirmAddressOnMap(e)
{

    e.preventDefault();
    var address = moo_getAddressFromForm();
    if( address.address == '')
    {
        swal({ title: "Address missing",text:"Please enter your address",   type: "error",   confirmButtonText: "OK" }).then(function() {
            setTimeout(function () {
                jQuery('#inputMooAddress').focus();
            },500);
        });
        return;
    } else {
        if( address.city == '')
        {
            swal({ title: "City missing",text:"Please enter your city",   type: "error",   confirmButtonText: "OK" }).then(function() {
                setTimeout(function () {
                    jQuery('#inputMooCity').focus();
                },500);
            });
            return;
        }
    }
    var address_string = Object.keys(address).map(function(k){return address[k]}).join(" ");
    jQuery.get('https://maps.googleapis.com/maps/api/geocode/json?&address='+encodeURIComponent(address_string)+'&key=AIzaSyBv1TkdxvWkbFaDz2r0Yx7xvlNKe-2uyRc',function (data) {
        if(data.results.length>0)
        {
            var location = data.results[0].geometry.location;
            document.getElementById("inputMooLat").value = location.lat;
            document.getElementById("inputMooLng").value = location.lng;
            moo_initMapAddress();
            jQuery('#MooMapAddingAddress').show();
            jQuery('#mooButonAddAddress').show();
            jQuery('#mooButonChangeAddress').show();
            jQuery(".mooFormAddingAddress").hide();
            jQuery(".mooFormConfirmingAddress").show();

        } else {
            swal({ title: "We weren't able to locate this address,try again",text:"",   type: "error",   confirmButtonText: "OK" });
        }
    });

}

function moo_getAddressFromForm()
{
    var address = {};
    address.address =  jQuery('#inputMooAddress').val();
    address.line2 =  jQuery('#inputMooAddress2').val();
    address.city =  jQuery('#inputMooCity').val();
    address.state =  jQuery('#inputMooState').val();
    address.zipcode =  jQuery('#inputMooZipcode').val();
    address.lat =  jQuery('#inputMooLat').val();
    address.lng =  jQuery('#inputMooLng').val();
    address.country =  "";
    return address;
}

function moo_addAddress(e)
{
    e.preventDefault();
    jQuery(e.target).html('<i class="fas fa-circle-notch fa-spin"></i>').attr('onclick','');
    var address = moo_getAddressFromForm();
    if(address.lat == "")
    {
        swal({ title: "Please confirm your address on the map",text:"By confirming  your address on the map you will help the driver to deliver your order faster, and you will help us to calculate your delivery fee better",   type: "error",   confirmButtonText: "Confirm"});
    }
    else {
        if(MooIsGuest || MooIsDisabled)
        {
            MooCustomerChoosenAddress = address;
            moo_checkout_form();
            jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
        }
        else
        {
            jQuery
                .post(moo_params.ajaxurl,{'action':'moo_customer_addAddress','address':address.address,'line2':address.line2,'city':address.city,'state':address.state,'zipcode':address.zipcode,"lat":address.lat,"lng":address.lng}, function (data) {
                    if(data.status == 'failure' || data.status == 'expired') {
                        swal({ title: "Your session has been expired",text:"Please login again",   type: "error",   confirmButtonText: "Login again" });
                        moo_show_loginform();
                        jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
                    }
                    else
                        if(data.status == 'success') {
                            moo_show_chooseaddressform(e);
                            jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
                        } else {
                            swal({ title: "Address not added to your account",text:"Please try again or contact us",   type: "error",   confirmButtonText: "Try again" });
                            jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
                        }
                })
                .fail(function(data) {
                    console.log(data.responseText);
                    jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
                    swal({ title: "Connection lost",text:"Please try again",   type: "error",   confirmButtonText: "Try again" });
                });
        }

    }

}
function moo_changeAddress(e)
{
    e.preventDefault();
    jQuery(".mooFormAddingAddress").show();
    jQuery(".mooFormConfirmingAddress").hide();
}

function moo_useAddress(e,address_id)
{
    e.preventDefault();
    for(i in MooCustomerAddress)
    {
        if(MooCustomerAddress[i].id==address_id)
            MooCustomerChoosenAddress = MooCustomerAddress[i]
    }
    moo_checkout_form();
}

function moo_filling_CustomerInformation()
{

    if(MooCustomer != null && MooCustomer[0] != null)
    {
        jQuery('#MooContactName').val(MooCustomer[0].fullname);
        jQuery('#MooContactPhone').val(MooCustomer[0].phone);
        jQuery('#MooContactEmail').val(MooCustomer[0].email).prop("readonly", true);
        jQuery('#moo-checkout-contact-content').html(MooCustomer[0].fullname+"<br/>"+MooCustomer[0].email+"<br/>"+MooCustomer[0].phone+"<br/>");
        if(MooCustomer[0].fullname!="" && MooCustomer[0].phone !="" && MooCustomer[0].email!="")
        {
            jQuery('#moo-checkout-contact-form').hide();
            jQuery('#moo-checkout-contact-content').show();
            jQuery('.moo-checkout-edit-icon').show();
        }
        else {
            jQuery('#moo-checkout-contact-form').show();
            jQuery('#moo-checkout-contact-content').hide();
            jQuery('.moo-checkout-edit-icon').hide();
        }

    }
    else
    {
        jQuery('#moo-checkout-contact-form').show();
        jQuery('#moo-checkout-contact-content').hide();
        jQuery('.moo-checkout-edit-icon').hide();
    }
}
function moo_checkout_form()
{
    moo_filling_CustomerInformation();
    var checkedOrderTypeID = jQuery('input[name="ordertype"]:checked').val();
    if(checkedOrderTypeID != '')
        moo_OrderTypeChanged(checkedOrderTypeID);

    jQuery('#moo-login-form').hide();
    jQuery('#moo-signing-form').hide();
    jQuery('#moo-forgotpassword-form').hide();
    jQuery('#moo-chooseaddress-form').hide();
    jQuery('#moo-addaddress-form').hide();
    jQuery('#moo-checkout-form').show();
    //update the total
}
function moo_pickup_the_order(e)
{
    MooCustomerChoosenAddress = null;
    MooDeliveryfees = 0.00;
    moo_checkout_form();
}

function moo_checkout_edit_contact()
{
    jQuery('#moo-checkout-contact-content').hide();
    jQuery('.moo-checkout-edit-icon').hide();
    jQuery('#moo-checkout-contact-form').show();
}

function moo_delete_address(event,address_id)
{
    swal({
            title: "Are you sure?",
            text: "You will not be able to recover this address",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            showLoaderOnConfirm: true,
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: false
    }).then(function(result){
        if (result.value) {
                    jQuery
                        .post(moo_params.ajaxurl,{'action':'moo_customer_deleteAddresses','address_id':address_id}, function (data) {
                            if(data.status == 'failure' || data.status == 'expired')
                            {
                                swal({ title: "Your session has been expired",text:"Please login again",   type: "error",   confirmButtonText: "Login again" });
                                moo_show_loginform();
                            }
                            else
                            if(data.status == 'success')
                            {
                                swal("Deleted!", "Your address has been deleted.", "success");
                                moo_show_chooseaddressform(event);
                            }
                            else
                                swal({ title: "Address not deleted",text:"Please try again or contact us",   type: "error",   confirmButtonText: "Try again" });
                        })
                        .fail(function(data) {
                            console.log(data.responseText);
                            swal({ title: "Connection lost",text:"Address not deleted, please try again",   type: "error",   confirmButtonText: "Try again" });
                        });

            } else {
                swal("Cancelled","","error");
            }
        });
}

function moo_verify_form(form)
{
    var regex_exp      = {};
    var message_errors = {};
    var selectedOrderType=null;
    regex_exp.email =  /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    regex_exp.credicard = /^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|(222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11}|62[0-9]{14})$/;
    regex_exp.cvv = /^[0-9]*$/;

    //gte the selected ordertype
    if(!(typeof moo_OrderTypes === 'undefined'))
        for(i in moo_OrderTypes)
        {
          if(form.ordertype == moo_OrderTypes[i].ot_uuid)
          {
              selectedOrderType = moo_OrderTypes[i];
          }

        }
    //check the name
    if(form.name == "")
    {
        swal('Please enter your name','','error').then(function() {
            setTimeout(function () {
                jQuery('#MooContactName').focus();
            },500)
        });

        return false;
    }
    //check the email
    if(form.email == "" || !regex_exp.email.test(form.email) )
    {
        swal('Please enter a valid email','We need a valid email to contact you and send to you the receipt','error').then(function() {
            setTimeout(function () {
                jQuery('#MooContactEmail').focus();
            },500);
        });
        return false;
    }
    //check the phone
    if(form.phone == "")
    {
        swal('Please enter your phone','We need your phone to contact you if we have any question about your order','error').then(function() {
            setTimeout(function () {
                jQuery('#MooContactPhone').focus();
            },500);
        });
        return false;
    }
    //Check the ordering method
    if(document.getElementById('moo-checkout-form-ordertypes'))
        if((typeof form.ordertype === 'undefined') || form.ordertype == "")
        {
            swal('Please choose the ordering method','How you want your order to be served ?','error').then(function() {
                setTimeout(function () {
                    jQuery('#moo-checkout-form-ordertypes').focus();
                },500);
            });
            return false;
        }

    //Check the delivery address and min amount per Order Type
    if(selectedOrderType != null)
    {
        if(selectedOrderType.minAmount !='0')
        {
            if(parseFloat(selectedOrderType.minAmount) > parseFloat(moo_Total.sub_total))
            {
               // swal('You did not meet the minimum purchase requirement',"this ordering method requires a subtotal greater than $"+selectedOrderType.minAmount ,'error');
                swal({
                    title: 'You did not meet the minimum purchase requirement',
                    text:"this ordering method requires a subtotal greater than $"+selectedOrderType.minAmount,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Continue shopping",
                    cancelButtonText: "Checkout",
                    closeOnConfirm: false },
                    function(){ window.history.back() });

                return false;
            }
        }

        if(selectedOrderType.show_sa =='1')
        {
            if(MooCustomerChoosenAddress!==null)
            {
                if(MooCustomerChoosenAddress.lat === '' || MooCustomerChoosenAddress.lng === '') {
                    swal('Please verify your address',"We can't found this address on the map, please choose an other address",'error').then(function() {
                        setTimeout(function () {
                            jQuery('#moo-checkout-form-ordertypes>.moo-checkout-bloc-message').focus();
                        },500);
                    });
                    return false;
                } else {
                    if(MooIsDeliveryError === true) {
                        moo_OrderTypeChanged(selectedOrderType.ot_uuid);
                        swal('Please verify your address',"",'error').then(function() {
                            setTimeout(function () {
                                jQuery('#moo-checkout-form-ordertypes').focus();
                            },500);
                        });
                        return false;
                    }
                }
            }
            else
            {
                moo_OrderTypeChanged(selectedOrderType.ot_uuid);
                swal('Please add the delivery address','You have chosen a delivery method, we need your address','error').then(function() {
                    setTimeout(function () {
                        jQuery('#moo-checkout-form-ordertypes>.moo-checkout-bloc-message .MooSimplButon').focus();
                    },500);
                });
                return false;
            }
        }
    }

    //check Pickup hour
    if(form.pickup_hour === "Select a time") {
        swal('Please choose a time','','error').then(function() {
            setTimeout(function () {
                jQuery('#moo-checkout-form-orderdate').focus();
            },500);
        });
        return false;
    }

    //check the payment info with the phone verification
    if(typeof form.payments === 'undefined')
    {
        swal('Please choose your payment method','','error').then(function() {
            setTimeout(function () {
                jQuery('#moo-checkout-form-payments').focus();
            },500);
        });
        return false;
    }
    else
    {
        if(form.payments === "cash")
        {
            if(MooCustomer !== null && MooCustomer[0].phone_verified === '0')
            {
                swal('Please verify your phone',"when you choose the cash payment you must verify your phone",'error').then(function() {
                    setTimeout(function () {
                        jQuery('#moo-checkout-form-payments').focus();
                    },500);
                });

                var paymentType = jQuery('input[name="payments"]:checked').val();
                if(paymentType != '') {
                    moo_changePaymentMethod(paymentType);
                }

                return false;
            } else {
                if(MooPhoneIsVerified === false)
                {
                    swal('Please verify your phone',"when you choose the cash payment you must verify your phone",'error').then(function() {
                        setTimeout(function () {
                            jQuery('#moo-checkout-form-payments').focus();
                        },500);
                    });

                    var paymentType = jQuery('input[name="payments"]:checked').val();
                    if(paymentType != '') {
                        moo_changePaymentMethod(paymentType);
                    }

                    return false;
                }
            }
            moo_SendForm(form);
        } else {
            if(form.payments !== "" && form.payments !=="creditcard")
            {
                form.token = form.payments;
                form.saveCard = false;
                moo_SendForm(form);
            } else {
                if(MooSaveCards && !MooIsGuest)
                {
                    // var displayOptions = {"amount":"$"+moo_Total.total};
                    SpreedlyExpress.setPaymentMethodParams({"email":form.email,"name":form.name,"phone_number":form.phone});
                    // SpreedlyExpress.setDisplayOptions(displayOptions);
                    SpreedlyExpress.openView();

                    SpreedlyExpress.onPaymentMethod(function(token, formData) {
                        // Send requisite payment method info to backend
                        form.token = token;
                        form.saveCard = jQuery('.moo-checkout-form-savecard-input').prop('checked');
                        moo_SendForm(form);
                    });
                } else {
                    if(moo_scp != "on")
                    {
                        if(form.cardNumber === '' || !regex_exp.credicard.test(form.cardNumber) )
                        {
                            swal('please enter a valid credit card number',"",'error');
                            return false;
                        }
                        if(form.cardcvv  === ''  )
                        {
                            swal('please enter a valid Card CVV',"",'error');
                            return false;
                        }
                        if(form.zipcode  === ''  )
                        {
                            swal('please enter a valid Zip Code',"",'error');
                            return false;
                        }
                        if(typeof form.cardNumber !== 'undefined')
                        {
                            form.cardNumber = form.cardNumber.replace(/\s/g, '');
                            form.cardNumber = form.cardNumber.replace(/-/g, '');
                        }
                        form.cardEncrypted = cryptCardNumber(form.cardNumber);
                        form.firstSix = firstSix(form.cardNumber);
                        form.lastFour = lastFour(form.cardNumber);
                        form.cardNumber = null;
                    }
                    moo_SendForm(form);
                }
            }
        }
    }
}

function moo_SendForm(form)
{
    jQuery('#moo_checkout_msg').remove();
    jQuery('#moo_btn_submit_order').hide();
    //Show loading Icon
    jQuery('#moo_checkout_loading').show();

    //Send the form to server
    jQuery
        .post(moo_params.ajaxurl,{'action':'moo_checkout','form':form}, function (data) {
            if(typeof data == 'object')
            {
                if(data.status == 'APPROVED')
                {
                    moo_order_approved(data.order);
                }
                else
                {
                    if(data.status == 'REDIRECT')
                    {
                        /*
                        var html = '<div align="center" class="moo-alert moo-alert-success" role="alert" style="font-size: 20px;">Thank you for your order<br/>You will be redirected to the payment page</div>';
                        // console.log(html);
                        jQuery("#moo-checkout").html('');
                        jQuery("#moo-checkout").parent().prepend(html);
                        jQuery("html, body").animate({
                            scrollTop: 0
                        }, 600);
                        window.location.href = data.url;
                        */
                        swal({
                            title: 'Thank you for your order',
                            text: 'You will be redirected to the payment page in moments',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            showCancelButton: false
                        }).then(function () {},function (dismiss) {});
                        window.location.href = data.url;
                    }
                    else
                        moo_order_notApproved(data.message);
                }
            }
            else
            {
                if(data.indexOf('"status":"APPROVED"') != -1 )
                    moo_order_approved('');
                else
                    moo_order_notApproved('');
            }
        })
        .fail(function(data) {
            console.log('FAIL');
            console.log(data.responseText);

            if(data.responseText.indexOf('"status":"APPROVED"') != -1 )
                moo_order_approved('');
            else
                moo_order_notApproved('')

        });
}
function moo_get_form(callback)
{
    var form={};
    form._wpnonce               =  jQuery('#_wpnonce').val();
    form.name                   =  jQuery('#MooContactName').val().trim();
    form.email                  =  jQuery('#MooContactEmail').val().trim();
    form.phone                  =  jQuery('#MooContactPhone').val().trim();
    form.cardNumber             =  jQuery('#Moo_cardNumber').val();
    form.expiredDateMonth       =  jQuery('#MooexpiredDateMonth').val();
    form.expiredDateYear        =  jQuery('#MooexpiredDateYear').val();
    form.cardcvv                =  jQuery('#moo_cardcvv').val();
    form.zipcode                =  jQuery('#moo_zipcode').val();
    form.tips                   =  jQuery('#moo_tips').val();
    form.instructions           =  jQuery('#Mooinstructions').val();
    form.pickup_day             =  jQuery('#moo_pickup_day').val();
    form.pickup_hour            =  jQuery('#moo_pickup_hour').val();

    if(document.getElementById('moo-checkout-form-ordertypes'))
        form.ordertype  =  jQuery('input[name="ordertype"]:checked').val();

    form.payments  =  jQuery('input[name="payments"]:checked').val();
    form.address = MooCustomerChoosenAddress;
    form.deliveryAmount = MooDeliveryfees;
    form.serviceCharges = MooServicefees;
    if(form.cardNumber !== undefined ){
        form.cardNumber = form.cardNumber.trim();
        form.cardNumber = form.cardNumber.replace(/\s+/g,"");
    }
    callback(form);
}
function moo_finalize_order(e)
{
    e.preventDefault();
    moo_get_form(moo_verify_form);
}
function moo_phone_changed()
{
    var phone  =  jQuery('#MooContactPhone').val();
    jQuery('#Moo_PhoneToVerify').val(phone);
}
function moo_phone_to_verif_changed()
{
    var phone  =  jQuery('#Moo_PhoneToVerify').val();
    jQuery('#MooContactPhone').val(phone);
    if(MooCustomer != null && MooCustomer[0] != null)
       MooCustomer[0].phone = phone;
    moo_filling_CustomerInformation();
}
function mooCouponApply(e)
{
    e.preventDefault();
    var coupon_code = jQuery('#moo_coupon').val();
    if(coupon_code == "") {
        swal({
            title:'Please enter your coupon code',
            timer:5000
        });
    } else {
        swal({
            title:'Checking your coupon...',
            showConfirmButton:false
        });
        jQuery
            .post(moo_params.ajaxurl,{'action':'moo_coupon_apply','moo_coupon_code':coupon_code}, function (data) {
                if(data!==null && data.status==="success") {
                    moo_Total = data.total;
                    if(data.type.toUpperCase() === "AMOUNT")
                        swal({ title: "Coupon applied", text: "Success! You have received a discount of $"+data.value,   type: "success",timer:5000, confirmButtonText: "Ok" });
                    else
                        swal({ title: "Coupon applied", text: "Success! You have received a discount of "+data.value+"%",   type: "success",timer:5000, confirmButtonText: "Ok" });

                    jQuery("#moo_remove_coupon_code").html(coupon_code);
                    jQuery("#moo_enter_coupon").hide();
                    jQuery("#moo_remove_coupon").show();

                    moo_update_totals();
                } else {
                    jQuery("#moo_remove_coupon").hide();
                    jQuery("#moo_enter_coupon").show();
                    swal({ title: "Error", text: data.message,   type: "error",timer:5000,   confirmButtonText: "Try again" });
                }

            })
            .fail(function(data) {
                console.log('FAIL');
                console.log(data.responseText);
                swal({ title: "Error", text:"verify your connection and try again",   type: "error",timer:5000,   confirmButtonText: "Try again" });
            });
    }
}
function mooCouponRemove(e)
{
    if(e){
        e.preventDefault();
    }

    swal({
        title:'Removing your coupon....',
        showConfirmButton:false
    });
    jQuery
        .post(moo_params.ajaxurl,{'action':'moo_coupon_remove'}, function (data) {
            if(data.status=="success")
            {
                moo_Total = data.total;
                jQuery("#moo_remove_coupon_code").html("");
                jQuery('#moo_coupon').val('');
                jQuery("#moo_enter_coupon").show();
                jQuery("#moo_remove_coupon").hide();
                moo_update_totals();
            }
            swal.close();
        })
        .fail(function(data) {
            console.log('FAIL');
            console.log(data.responseText);
            swal({ title: "Error", text:"verify your connection and try again",   type: "error",timer:5000,   confirmButtonText: "Try again" });
        });
}
function MooRemoveCard(event,token)
{
    swal({
            title: "Are you sure?",
            text: "You will not be able to recover this credit card",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            showLoaderOnConfirm: true,
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if (isConfirm) {
                jQuery
                    .post(moo_params.ajaxurl,{'action':'moo_customer_deleteCreditCard','token':token}, function (data) {
                        if(data.status == 'failure')
                        {
                            swal({ title: "Your session has been expired",text:"Please login again",   type: "error",   confirmButtonText: "Login again" });
                        }
                        else
                        if(data.status == 'success')
                        {
                            swal("Deleted!", "Your credit card has been deleted.", "success");
                            jQuery("#moo-checkout-form-payments-"+token).parent().parent().hide().html("");
                            MooServicefees = 0;
                            mooShowSavedCards(data.cards);
                            moo_update_total();
                        }
                        else
                            swal({ title: "Not deleted",text:"Please try again or contact us",   type: "error",   confirmButtonText: "Try again" });
                    })
                    .fail(function(data) {
                        console.log(data.responseText);
                        swal({ title: "Connection lost",text:"Not deleted, please try again",   type: "error",   confirmButtonText: "Try again" });
                    });
            } else {
                swal("Cancelled","","error");
            }
        });
}
function mooShowSavedCards(cards)
{
    //Not show saved cards until make this feature for public
    return false;
    for(i in cards)
    {
        var creditCrad = cards[i];

        html ='<div class="moo-checkout-form-payment-option" style="margin-bottom: 15px;">';
        html +=' <input class="moo-checkout-form-payment-input" type="radio" name="payments" value="'+creditCrad.token+'" id="moo-checkout-form-payments-'+creditCrad.token+'">';
        html +='<label for="moo-checkout-form-payments-'+creditCrad.token+'" style="display: inline;margin-left:15px">Pay with : '+creditCrad.number+' ( <a href="#" onclick="MooRemoveCard(event,\''+creditCrad.token+'\')" >X</a> )</label>';
        html +='</div>';
        jQuery("#moo-checkout-form-payments>.moo-checkout-bloc-content").prepend(html)
    }

    jQuery('.moo-checkout-form-payment-input').iCheck({
        checkboxClass: 'icheckbox_square',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });
    jQuery('.moo-checkout-form-payment-input').on('ifClicked', function (event) {
        var paymentType = jQuery(event.target).val();
        //moo_changePaymentMethod(paymentType)
        if(paymentType !="" && paymentType !="cash" && paymentType != "creditcard")
        {
            jQuery('#moo-checkout-form-savecard').hide();
            jQuery('#moo-checkout-form-tips').show();
            //If we will charge customers to pay using saved credir cards, add the service fees here
            if(MooSaveCardsFees)
                MooServicefees = 0.5;
            else
                MooServicefees = 0;
            moo_update_totals();
        }
        else
        {
            MooServicefees = 0;
            moo_update_totals();
        }
    });

}
