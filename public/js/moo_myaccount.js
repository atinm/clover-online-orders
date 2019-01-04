jQuery(document).ready(function() {
    if(moo_customer_logged==='yes') {
        moo_my_account_myorders_perPage(1);
    }
});
console.log(typeof moo_fb_app_id);
if(typeof moo_fb_app_id != 'undefined')
{
    if(moo_fb_app_id != "")
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

function moo_verifyPhone(event) {
    event.preventDefault();
    var phone_number=jQuery('#Moo_PhoneToVerify').val();
    jQuery('#moo_verifPhone_sending').hide();
    jQuery('#moo_verifPhone_verified').hide();
    jQuery('#Moo_VerificationCode').val('');
    jQuery('#moo_verifPhone_verificatonCode').show();
    jQuery.post(moo_params.ajaxurl,{'action':'moo_send_sms','phone':phone_number});
}

function moo_verifyCode(event) {
    event.preventDefault();
    var code=jQuery('#Moo_VerificationCode').val();
    jQuery.post(moo_params.ajaxurl,{'action':'moo_check_verification_code','code':code}, function (data) {
        if(data.status == 'success')
        {
            jQuery('#moo_verifPhone_sending').hide();
            jQuery('#moo_verifPhone_verificatonCode').hide();
            jQuery('#moo_verifPhone_verified').css("display","inline-block");
            swal({ title: 'Phone verified', text: 'Please have your payment ready when picking up from the store and don\'t forget to finalize your order below',   type: "success",timer:5000,   confirmButtonText: "OK" });
            if(MooCustomer != null)
            {
                MooCustomer[0].phone_verified = '1';
            }
            MooPhoneIsVerified = true;
            jQuery('#MooContactPhone').prop("readonly",true);
        }
        else
            swal({ title: "Code invalid", text: 'this code is invalid please try again',   type: "error",timer:5000,   confirmButtonText: "Try again" });
    });
}

function moo_verifyCodeTryAgain(event) {
    event.preventDefault();
    jQuery('#moo_verifPhone_sending').show();
    jQuery('#moo_verifPhone_verificatonCode').hide();
    jQuery('#moo_verifPhone_verified').hide();
}

function moo_show_sigupform(e)
{
    if(e !== undefined)
        e.preventDefault();
    jQuery('#moo-login-form').hide();
    jQuery('#moo-signing-form').show();
    jQuery('#moo-forgotpassword-form').hide();
    jQuery('#moo-customerPanel').hide();
}

function moo_show_loginform()
{
   // e.preventDefault();
    jQuery('#moo-login-form').show();
    jQuery('#moo-signing-form').hide();
    jQuery('#moo-forgotpassword-form').hide();
    jQuery('#moo-customerPanel').hide();
    jQuery('#moo-addaddress-form').hide();

}
function moo_show_forgotpasswordform(e)
{
    if(e !== undefined)
        e.preventDefault();

    jQuery('#moo-login-form').hide();
    jQuery('#moo-signing-form').hide();
    jQuery('#moo-forgotpassword-form').show();
    jQuery('#moo-customerPanel').hide();
    jQuery('#moo-addaddress-form').hide();
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

    jQuery('#moo-login-form').hide();
    jQuery('#moo-signing-form').hide();
    jQuery('#moo-forgotpassword-form').hide();
    jQuery('#moo-customerPanel').hide();
    jQuery('#moo-addaddress-form').show();
}

function moo_show_chooseaddressform(e) {
    if(typeof e !== "undefined")
        e.preventDefault();

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
        jQuery('#moo-customerPanelContent').html('<p style="text-align:center">Please wait ...</p>');

        jQuery('#moo-login-form').hide();
        jQuery('#moo-signing-form').hide();
        jQuery('#moo-forgotpassword-form').hide();
        jQuery('#moo-customerPanel').show();
        jQuery('#moo-addaddress-form').hide();

        jQuery
            .post(moo_params.ajaxurl,{'action':'moo_customer_getAddresses'}, function (data) {
                if(data.status == 'success')
                {
                    addresses =  data.addresses;
                    cards = data.cards;
                    MooCustomerAddress = addresses;
                    MooCustomer = data.customer;

                    if(MooCustomer[0].phone_verified == "1")
                        MooPhoneIsVerified = true;

                    if(addresses.length>0)
                    {
                        var html="";
                        if(addresses.length==1)
                        {
                            var OneAddress = addresses[0];
                            html +='<div class="moo-col-md-4 moo-col-md-offset-4">';
                            html +='<div class="moo-address-block">';
                            html +='<span title="delete this address" onclick="moo_delete_address(event,'+OneAddress.id+')">X</span>';
                            html +=OneAddress.address+'<br />';
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
                                html +=OneAddress.city+', '+OneAddress.state+' '+OneAddress.zipcode+'<br />';
                                html +='  <a class="MooSimplButon MooUseAddressButton" href="#" onclick="moo_useAddress(event,'+OneAddress.id+')">USE THIS ADDRESS</a>';
                                html +='</div></div>';
                            }
                        }
                        //Display addresses
                        jQuery('#moo-customerPanelContent').html(html);
                    }
                    else
                        moo_show_form_adding_address();

                    if(cards!= null && cards.length > 0)
                        mooShowSavedCards(cards);
                }
                else
                if(data.status = 'expired')
                {
                    MooCustomerAddress = null;
                    MooCustomer = null;
                    swal({ title: "Your session is expired", type: "error",timer:5000,   confirmButtonText: "Login again" });
                    moo_show_loginform();
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

function moo_login(e) {
    e.preventDefault();
    jQuery(e.target).html('<i class="fas fa-circle-notch fa-spin"></i>').attr('onclick','eventPrevent(event)');

    var email    =  jQuery('#inputEmail').val();
    var password =  jQuery('#inputPassword').val();
    if(email === '') {
        swal({ title: "Please enter your email",text:"",  timer:5000, type: "error" });
        return;
    } else {
        if(password === '') {
            swal({ title: "Please enter your password",text:"",  timer:5000, type: "error"});
            return;
        } else {
            jQuery
                .post(moo_params.ajaxurl,{'action':'moo_customer_login','email':email,"password":password}, function (data) {
                    jQuery(e.target).html('Log In').attr('onclick','moo_login(event)');
                    if(data.status === 'success') {
                        moo_showCustomerPanel(e);
                    } else {
                        swal({ title: "Invalid User Name or Password",text:"Please click on forgot password or Please register as new user.",   type: "error",timer:5000,   confirmButtonText: "Try again" });
                    }
                })
                .fail(function(data) {
                    console.log(data.responseText);
                    swal({ title: "Invalid User Name or Password",text:"Please click on forgot password or Please register as new user.",   type: "error",timer:5000,   confirmButtonText: "Try again" });
                    jQuery(e.target).html('Log In').attr('onclick','moo_login(event)');

                });
        }
    }

}

function moo_loginViaFacebook(e) {
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
                            moo_showCustomerPanel();
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

function moo_signin(e) {
    e.preventDefault();
    var title     = "";
    var full_name = jQuery('#inputMooFullName').val();
    var email     = jQuery('#inputMooEmail').val();
    var phone     = jQuery('#inputMooPhone').val();
    var password  = jQuery('#inputMooPassword').val();
    var  regex_email =  /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(email=='') {
        swal("Please enter your email");
        return;
    }
    if(! regex_email.test(email)) {
        swal("Please enter a valid email");
        return;
    }

    if(password=='') {
        swal("Please enter your password");
        return;
    }
    if(phone=='') {
        swal("Please enter your phone");
        return;
    }
    jQuery(e.target).html('<i class="fas fa-circle-notch fa-spin"></i>').attr('onclick','eventPrevent(event)');
    jQuery
        .post(moo_params.ajaxurl,{'action':'moo_customer_signup','title':title,'full_name':full_name,'phone':phone,'email':email,"password":password}, function (data) {
            if(data.status == 'success')
            {
                jQuery(e.target).html('Register').attr('onclick','moo_signin(event)');
                moo_showCustomerPanel(e);
            }
            else
            {
                jQuery(e.target).html('Submit').attr('onclick','moo_signin(event)');
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
        jQuery(e.target).html('<i class="fas fa-circle-notch fa-spin"></i>').attr('onclick','eventPrevent(event)');

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
    if( address.address == '' || address.city == '')
    {
        swal({ title: "Address missing",text:"Please enter your address",   type: "error",   confirmButtonText: "OK" });
        return;
    }
    var address_string = Object.keys(address).map(function(k){return address[k]}).join(" ");
    jQuery.get('https://maps.googleapis.com/maps/api/geocode/json?&address='+address_string+'&key=AIzaSyBv1TkdxvWkbFaDz2r0Yx7xvlNKe-2uyRc',function (data) {
        if(data.results.length>0)
        {
            var location = data.results[0].geometry.location;
            document.getElementById("inputMooLat").value = location.lat;
            document.getElementById("inputMooLng").value = location.lng;
            moo_initMapAddress();
            jQuery('#MooMapAddingAddress').show();
            jQuery('#mooButonAddAddress').show();
        }
        else
        {
            swal({ title: "We weren't able to locate this address,try again",text:"",   type: "error",   confirmButtonText: "OK" });
        }
    });

}

function moo_getAddressFromForm()
{
    var address = {};
    address.address =  jQuery('#inputMooAddress').val();
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
    jQuery(e.target).html('<i class="fas fa-circle-o-notch fa-spin"></i>').attr('onclick','');
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
                .post(moo_params.ajaxurl,{'action':'moo_customer_addAddress','address':address.address,'city':address.city,'state':address.state,'zipcode':address.zipcode,"lat":address.lat,"lng":address.lng}, function (data) {
                    if(data.status == 'failure' || data.status == 'expired')
                    {
                        swal({ title: "Your session has been expired",text:"Please login again",   type: "error",   confirmButtonText: "Login again" });
                        moo_show_loginform();
                        jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
                    }
                    else
                        if(data.status == 'success')
                        {
                            moo_show_chooseaddressform(e);
                            jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
                        }
                        else
                        {
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

function moo_showCustomerPanel()
{
    //moo_filling_CustomerInformation();
    jQuery('#moo-login-form').hide();
    jQuery('#moo-signing-form').hide();
    jQuery('#moo-forgotpassword-form').hide();
    jQuery('#moo-customerPanel').show();
    jQuery('#moo-addaddress-form').hide();

    moo_my_account_myorders_perPage(1);

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
        },
        function(isConfirm){
            if (isConfirm) {
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

function moo_displayLoadingSection() {
    var cpContent = jQuery("#moo_cp_content");
    cpContent.html('<div class="mooLoadingSection"><i class="fas fa-spinner fa-spin"></i></div>');
}
function moo_my_account_render_pagination(page,total_orders) {
    var html = '';
    var currentPage = page;
    var total = Math.ceil(total_orders/10);
    html += '<div class="moo_cp_content_filter">';
    html += '<div class="moo_pagination">';
    if((currentPage-1) >0) {
        html += '<a href="#" onclick="moo_my_account_myorders_selectPage(event,'+(currentPage-1)+')">&laquo;</a>';
    }

    for(var i=1;i<=total;i++){
        if(i==currentPage) {
            html += '<a href="#"  class="active">'+i+'</a>';
        } else {
            html += '<a href="#" onclick="moo_my_account_myorders_selectPage(event,'+i+')">'+i+'</a>';
        }
    }
    if((currentPage+1) <= total)
    {
        html += '<a href="#" onclick="moo_my_account_myorders_selectPage(event,'+(currentPage+1)+')">&raquo;</a>';
    }
    html += '</div>';
    html += '</div>';
    return html;
}
function  moo_my_account_myorders(e) {
    if(e !== undefined) {
        e.preventDefault();
    }
    moo_my_account_myorders_perPage(1);
}
function  moo_my_account_myfavorits(e) {
    if(e !== undefined) {
        e.preventDefault();
    }
    moo_nav_cpanel_setactive('moo_nav_favorits');
    var cpContent = jQuery("#moo_cp_content");
    moo_displayLoadingSection();
    var globalHtml  = '<div class="moo_cp_content_header"><h1>Most Purchased</h1></div>';
    globalHtml += '<div class="moo_cp_content_body">';
    jQuery
        .get(moo_RestUrl+"moo-clover/v1/customers/favorites", function (data) {
             mooRenderItemsForFavorits(data.items);
        })
        .fail(function(data) {
            console.log(data.responseText);
            swal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
        });
}
function  moo_my_account_addresses(e) {
    if(e !== undefined) {
        e.preventDefault();
    }
    moo_nav_cpanel_setactive('moo_nav_addresses');
    var cpContent = jQuery("#moo_cp_content");
    moo_displayLoadingSection();
    var globalHtml  = '<div class="moo_cp_content_header"><h1>Addresses</h1></div>';
    globalHtml += '<div class="moo_cp_content_body">';
    jQuery
        .get(moo_RestUrl+"moo-clover/v1/customers/addresses", function (data) {
            if(data.status ==='success') {
                mooRenderAddresses(data.addresses);
            } else {
                wal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
            }
        })
        .fail(function(data) {
            console.log(data.responseText);
            swal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
        });
}
function  moo_my_account_profil(e) {
    if(e !== undefined) {
        e.preventDefault();
    }
    moo_nav_cpanel_setactive('moo_nav_profil');
    var cpContent = jQuery("#moo_cp_content");
    moo_displayLoadingSection();
    jQuery
        .get(moo_RestUrl+"moo-clover/v1/customers", function (data) {
            console.log(data);
            if(data.status ==='success') {
                mooRenderProfil(data.customer);
            } else {
                wal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
            }
        })
        .fail(function(data) {
            console.log(data.responseText);
            swal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
        });
}
function  moo_my_account_myorders_selectPage(e,page) {
    if(e !== undefined) {
        e.preventDefault();
    }
    moo_my_account_myorders_perPage(page);
}

function  moo_my_account_myorders_perPage(page) {
    var cpContent = jQuery("#moo_cp_content");
    moo_displayLoadingSection();
    moo_nav_cpanel_setactive('moo_nav_orders');
    //load all orders
    var globalHtml  = '<div class="moo_cp_content_header"><h1>List of orders</h1></div>';
        globalHtml += '<div class="moo_cp_content_body">';
    jQuery
        .get(moo_RestUrl+"moo-clover/v1/customers/orders?page="+page, function (data) {
            if(data.status === 'success') {
                if(data.orders.length > 0) {
                    for(i in data.orders){
                        var order = data.orders[i];
                        if(typeof order !== 'object' || order.uuid_order === undefined || order.uuid_order === null) {
                            console.log('An Order Element is not valid');
                            console.log(order);
                            if(i == (data.orders.length-1)){
                                globalHtml += '</div>'; //fin  moo_cp_content_body
                                //add pagination
                                globalHtml += moo_my_account_render_pagination(data.current_page,data.total_orders);;
                                cpContent.html(globalHtml);
                            }
                            continue;
                        }
                        var html = '<div class="moo-row moo_cp_content_oneOrderLigne">'; // start principal div

                        html +='<div class="moo-row moo_border moo_cp_content_oneOrder moo_order_info_'+order.uuid_order+'">'; // start order content
                        html +='<div class="moo-col-md-5 moo-col-xs-12 moo_right_border moo_cp_content_oneOrderCol moo_cp_content_oneOrderItems"  onclick="moo_show_oderInfo(\''+order.uuid_order+'\')">';
                        html += '<span class="moo_cp_orders_ordernumber">ORDER NO: '+order.uuid_order+'</span>';
                        html += '<span class="moo_cp_orders_orderdate">'+order.date_order+'</span> <br/> Click to View Order / Re-Order';
                        html +='</div>';
                        html +='<div class="moo-col-md-2 moo-col-xs-6 moo_right_border moo_cp_content_oneOrderCol moo_center_text moo_cp_content_oneOrderTotal">$'+order.amount_order.toFixed(2)+'</div>';
                        html +='<div class="moo-col-md-2 moo-col-xs-6 moo_right_border moo_cp_content_oneOrderCol moo_center_text moo_cp_content_oneOrderStatus">'+order.status.toUpperCase()+'</div>';
                        html +='<div class="moo-col-md-3 moo-col-xs-12 moo_cp_content_oneOrderCol moo_center_text  moo_center_text moo_cp_content_oneOrderButton"><a class="osh-btn" target="_blank" href="https://www.clover.com/r/'+order.uuid_order+'">VIEW RECEIPT</a></div>';

                        html +='</div>'; // fin order content
                        html +='<div class="moo-row moo_order_info moo_order_info_'+order.uuid_order+'" id="moo_order_info_'+order.uuid_order+'">';// start order content
                        html += "<ul>";
                        for(j in order.items){
                            var item = order.items[j];
                            if(typeof item !== 'object') {
                                console.log('An Item Element is not an object');
                                console.log(item);
                                continue;
                            }
                            if(item.modifers != '') {
                                html += "<li>"+item.quantity+' x '+item.name+"<br />";
                                for(k in item.list_modifiers) {
                                    modifier = item.list_modifiers[k];
                                    if(typeof modifier == 'object') {
                                        html += " - "+modifier.name+"<br />";
                                    }
                                }
                                html += '</li>';
                                html += '</li>';
                            } else {
                                html += "<li>"+item.quantity+' x '+item.name+"</li>";
                            }
                        }
                        if(order.items.length === 0 || order.items.length === undefined) {
                            html += "<div>This order cannot be viewed, please try selecting \"VIEW RECEIPT\". You may need to manually re-order the items again. This usually happens because the previous version of the software didn't have Order History feature. Future orders will be saved as long as Order History feature is not deleted.</div>";
                        } else {
                            html +='<div class="moo-col-md-12"><button id="mooButonLogin" class="moo-btn moo_pull_right" onclick="moo_reOrder(\''+order.uuid_order+'\')">Re-order</button></div>';
                        }
                        html += "</ul>";
                        html +='</div>';// fin order content

                        html +='</div>'; // Fin principal div
                        globalHtml += html;
                        if(i == (data.orders.length-1)){
                            globalHtml += '</div>'; //fin  moo_cp_content_body
                            //add pagination
                            globalHtml += moo_my_account_render_pagination(data.current_page,data.total_orders);
                            cpContent.html(globalHtml);
                        }
                    }
                } else {
                    cpContent.html("You don't have any order yet")
                }
            } else {
                swal({ title: "Error",text:"your session has expired",   type: "error",timer:5000 });
                moo_refresh_page();
            }
        })
        .fail(function(data) {
            console.log(data.responseText);
            swal({ title: "Error",text:"An error has occurred please try again",   type: "error",timer:5000,   confirmButtonText: "Try again" });
        });
}
function moo_reOrder(order_uuid) {
    //display loading
    swal({
        title: 'Please wait',
        text: 'We are checking the items',
        allowOutsideClick: false,
        showConfirmButton: false,
        showCancelButton: false
    }).then(function () {},function (dismiss) {});

    //window.location.href = data.url;
    //check the cart
    jQuery.get(moo_RestUrl+"moo-clover/v1/cart",function(response){
        if(Object.keys(response.items).length>0) {
            Swal({
                title: 'You already have some item(s) in your cart',
                type: 'warning',
                text: 'Would you like to add this order to the current item(s) already in the cart?',
                showCloseButton: true,
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then(function (result) {
                swal({
                    title: 'Please wait',
                    text: 'We are creating your order',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    showCancelButton: false
                }).then(function () {},function (dismiss) {});
                if (result.value) {
                    jQuery.post(moo_RestUrl+"moo-clover/v1/customers/orders/"+order_uuid+"/reorder",{'cart':'keep'},function(response){
                        if(response.status === 'success'){
                            swal({ title: "Items added to your cart",text:'we are redirecting you to cart page, please wait',   type: "success" });
                            window.location.href = response.cart_url;
                        } else {
                            swal({ title: "Error",text:response.message,   type: "error" });
                        }
                    }).fail(function(data) {
                        swal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
                    });
                } else {
                    if(result.dismiss === 'cancel') {
                        jQuery.post(moo_RestUrl+"moo-clover/v1/customers/orders/"+order_uuid+"/reorder",{'cart':'empty'},function(response){
                            swal({ title: "Items added to your cart",text:'we are redirecting you to cart page, please wait',   type: "success" });
                            window.location.href = response.cart_url;
                        }).fail(function(data) {
                            swal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
                        });
                    } else {
                        swal.close();
                    }
                }
        })
        } else {
            //send the request
            jQuery.post(moo_RestUrl+"moo-clover/v1/customers/orders/"+order_uuid+"/reorder",{'cart':'keep'},function(response){
                if(response.status === 'success'){
                    swal({ title: "Items added to your cart",text:'we are redirecting you to cart page, please wait',   type: "success" });
                    window.location.href = response.cart_url;
                } else {
                    swal({ title: "Error",text:response.message,   type: "error" });
                }
            }).fail(function(data) {
                swal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
            });
        }
    }).fail(function(data) {
        swal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
    });

}
function mooRenderItemsForFavorits(items){
    window.moo_nbItemsPerLine = 3;
    var html  = '<div class="moo_cp_content_header"><h1>Favorites items</h1></div>';
    html += '<div class="moo_cp_content_body">';
    for(var i in items){
        var item = items[i];
        if(typeof item  !== 'object') {
            continue;
        }
        var item_price = parseFloat(item.price);
        item_price = item_price/100;
        item_price = item_price.toFixed(2);

        if(item.price > 0 && item.price_type === "PER_UNIT")
            item_price += '/'+item.unit_name;
        if( i % window.moo_nbItemsPerLine === 0) {
            html +='<div class="moo-col-md-'+(12/window.moo_nbItemsPerLine)+'" style="clear: both">';
        } else {
            html +='<div class="moo-col-md-'+(12/window.moo_nbItemsPerLine)+'">';

        }
        html +='<a title="'+item.description+'" class="link" href="#item-'+item.uuid.toLowerCase()+'" data-item-id="'+item.uuid.toLowerCase()+'" >';

        if(item.image !== null && item.image.url !== null && item.image.url !== "")
        {
            html += '<div class="image-wrapper" style="background: url('+item.image.url+') no-repeat center;background-size:100%;"></div>';
        }
        else
        {
            html +='<div class="image-wrapper">'+
                '<img class="moo-image" alt="" data-image-vertical="1" width="100%" height="100%" src="'+moo_params['plugin_img']+'/noImg.png" />'+
                '</div>';
        }

        html +=   '<h2 class="title"><span class="brand "></span>'+
            '<span class="name">'+item.name+'</span>'+
            '</h2>'+
            '<div class="price-container clearfix">'+
            '<div class="price-box">'+
            '<span class="price">';
        // '<span>799</span>'+
        if(parseFloat(item.price) === 0)
        {
            html += '<span></span>';
        }
        else
        {
            html += '<span>$'+item_price+'</span>';
        }
        html += '</span>'+
            '</div>'+
            '</div>'+
            '<div class="btn-wrapper"><span class="moo-category-name">';
        if(item.stockCount === "out_of_stock")
        {
            html += '<button class="osh-btn"><span class="label">OUT OF STOCK</span></button>';
        }
        else
        {
            //Checking the Qty window show/hide and add add to cart button
            if(true)
            {
                if(item.has_modifiers)
                {
                    //check qty window for modifiers
                    if(true)
                        html += '<button class="osh-btn" onclick="mooOpenQtyWindow(event,\''+item.uuid+'\',\''+item.stockCount+'\',moo_clickOnOrderBtnFIWM)"><span class="label">Choose Qty & Options</span></button>';
                    else
                        html += '<button class="osh-btn" onclick="moo_clickOnOrderBtnFIWM(event,\''+item.uuid+'\',1)"><span class="label">Choose Options & Qty</span></button>';
                }
                else
                    html += '<button class="osh-btn" onclick="mooOpenQtyWindow(event,\''+item.uuid+'\',\''+item.stockCount+'\',moo_clickOnOrderBtn)"><span class="label">Add to cart</span></button>';

            }
            else
            {
                if(item.has_modifiers)
                    html += '<button class="osh-btn" onclick="moo_clickOnOrderBtnFIWM(event,\''+item.uuid+'\',1)"><span class="label"> Choose Options & Qty </span></button>';
                else
                    html += '<button class="osh-btn" onclick="moo_clickOnOrderBtn(event,\''+item.uuid+'\',1)"><span class="label">Add to cart</span> </button>';

            }

        }

        html +='</span></div></a></div>';
    }
    html    += "</div>";

    // Then add theme to dom and do some changes after finsihed the rendriign
    jQuery("#moo_cp_content").html(html).promise().done(function() {
        console.log("Favorits items loaded")
    });
}
function mooRenderAddresses(addresses){
    var html  = '<div class="moo_cp_content_header"><h1>My Addresses</h1><span><a class="osh-btn moo_pull_right" href="#" onclick="moo_add_new_address()">ADD NEW</a></span></div>';
    html += '<div class="moo_cp_content_body">';
    if(addresses.length > 0) {
        for(i in addresses){
            var address = addresses[i];
            if(typeof address  !== 'object') {
                continue;
            }
            html += '<div class="moo-row moo_cp_content_oneAddressLigne">'; // start principal div

            html +='<div class="moo-col-md-12 moo_border moo_cp_content_oneOrder">'; // start address line
            html +='<div class="moo-col-md-6 moo_right_border moo_cp_content_oneOrderCol moo_cp_content_oneOrderItems">';
            html += '<span class="moo_cp_orders_ordernumber">'+address.address+'</span>';
            html += '<span class="moo_cp_orders_orderdate">'+address.city+', '+address.zipcode+'</span>';
            html +='</div>';
            html +='<div class="moo-col-md-4 moo_right_border moo_cp_content_oneOrderCol moo_cp_content_oneOrderTotal">'+address.state+'</div>';
            html +='<div class="moo-col-md-2 moo_cp_content_oneOrderCol moo_center_text  moo_cp_content_oneOrderButton"><a class="osh-btn" href="#" onclick="moo_delete_address(event,\''+address.id+'\')">REMOVE</a></div>';
            html +='</div>'; // fin address line
            html +='</div>'; // Fin principal div
        }
    } else {
        html += "You don't have any order yet";
    }
    html    += "</div>";

    // Then add theme to dom and do some changes after finsihed the rendriign
    jQuery("#moo_cp_content").html(html).promise().done(function() {
        console.log("Address loaded")
    });
}
function moo_add_new_address(){
    var html  ='<div class="moo_cp_content_header"><h1>Add new addresss</h1></div>';
        html +='<div class="moo_cp_content_body">';
        html +='<div class="moo-row">'; // start principal div
        html +='<div class="moo-col-md-8 moo-col-md-offset-2">';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooAddress">Address</label>';
        html +='<input type="text" class="moo-form-control" id="cp_MooAddress">';
        html +='</div>';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooAddress">Suite / Apt #</label>';
        html +='<input type="text" class="moo-form-control" id="cp_MooAddress2">';
        html +='</div>';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooCity">City</label>';
        html +='<input type="text" class="moo-form-control" id="cp_MooCity">';
        html +='</div>';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooState">State</label>';
        html +='<input type="text" class="moo-form-control" id="cp_MooState">';
        html +='</div>';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooZipcode">Zip code</label>';
        html +='<input type="text" class="moo-form-control" id="cp_MooZipcode">';
        html +='</div>';
        html +='<p class="moo-centred">';
        html +='<a href="#" class="moo-btn moo-btn-warning" onclick="moo_ConfirmAddressOnMap(event)">Next</a> <a href="#" class="moo-btn" onclick="moo_my_account_addresses(event)">Back to addresses</a>';
        html +='</p>';
        html +='<div id="MooMapAddingAddress">';
        html +='<p style="margin-top: 150px;">Loading the MAP...</p>';
        html +='</div>';
        html +='<input type="hidden" class="moo-form-control" id="cp_MooLat">';
        html +='<input type="hidden" class="moo-form-control" id="cp_MooLng">';
        html +='<div class="form-group">';
        html +='<a id="mooButonAddAddress" onclick="moo_addAddress(event)">Confirm and add address</a>';
        html +='</div>';
        html +='</div>'; // Fin principal div
        html += "</div>";

    // Then add theme to dom and do some changes after finsihed the rendriign
    jQuery("#moo_cp_content").html(html).promise().done(function() {
        console.log("Adding new address")
    });
}
function mooRenderProfil(user){
    var html  ='<div class="moo_cp_content_header"></div>';
        html +='<div class="moo_cp_content_body">';// start principal div
        html +='<div class="moo-row">';
        html +='<h2>Personal information</h2>';
        html +='<div class="moo-col-md-8 moo-col-md-offset-2">';
        html +='<form onsubmit="moo_updateProfil(event)" method="post">';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooAddress">Full Name</label>';
        html +='<input type="text" class="moo-form-control" id="cp_MooName" value="'+user.fullname+'">';
        html +='</div>';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooAddress">Email</label>';
        html +='<input type="text" class="moo-form-control" id="cp_MooEmail" value="'+user.email+'">';
        html +='</div>';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooCity">Phone</label>';
        html +='<input type="text" class="moo-form-control" id="cp_MooPhone" value="'+user.phone+'">';
        html +='</div>';
        html +='<p class="moo-centred">';
        html +='<button href="#" class="osh-btn" onclick="moo_updateProfil(event)">Update profile</button>';
        html +='</p>';
        html +='</form>';
        html +='</div>';
        html +='</div>';
        html +='<div class="moo-row">';
        html +='<h2>Change Password</h2>';
        html +='<div class="moo-col-md-8 moo-col-md-offset-2">';
        html +='<form onsubmit="moo_changePassword(event)" method="post">';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooAddress">Current password</label>';
        html +='<input type="password" class="moo-form-control" id="cp_MooCurrentPassword" autocomplete="current-password">';
        html +='</div>';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooAddress">New Password</label>';
        html +='<input type="password" class="moo-form-control" id="cp_MooNewPassword" autocomplete="new-passwrod">';
        html +='</div>';
        html +='<div class="moo-form-group">';
        html +='<label for="inputMooCity">Repeat Password</label>';
        html +='<input type="password" class="moo-form-control" id="cp_MooRepeatNewPassword" autocomplete="new-password">';
        html +='</div>';
        html +='<p class="moo-centred">';
        html +='<button href="#" class="osh-btn" onclick="moo_changePassword(event)">Change Password</button>';
        html +='</p>';
        html +='</form>';
        html +='</div>';
        html +='</div>';
        html +='</div>'; // Fin principal div
        html += "</div>";

    // Then add theme to dom and do some changes after finsihed the rendriign
    jQuery("#moo_cp_content").html(html).promise().done(function() {
        console.log("Adding new address")
    });
}

function moo_nav_cpanel_setactive(element) {
    jQuery('.moo_nav_cpanel').each(function( index, ele ) {
        jQuery(ele).removeClass('moo_nav_active');
    });
    jQuery('#'+element).addClass('moo_nav_active');
}
function eventPrevent(event) {
    event.preventDefault();
}

function mooOpenQtyWindow(event,item_id,stockCount,callback) {
    event.preventDefault();
    var inputOptions = new Promise(function (resolve) {
        if(stockCount == "not_tracking_stock" ||  stockCount == "tracking_stock" ) {
            resolve({
                "1":"1","2":"2","3":"3","4":"4","5":"5","6":"6","7":"7","8":"8","9":"9","10":"10","custom":"Custom Quantity"
            })
        } else {
            var options = {};
            var QtyMax = (parseInt(stockCount)>10)?10:parseInt(stockCount);
            var count = QtyMax;
            for(var $i = 1;$i<=QtyMax;$i++)
            {
                options[$i.toString()] = $i.toString();
                if(!--count)
                {
                    options["custom"] = "Custom Quantity";
                    resolve(options)
                }
            }
        }
    });
    swal({
        title: 'Select the quantity',
        showLoaderOnConfirm: true,
        confirmButtonText: "Add",
        input: 'select',
        inputClass: 'moo-form-control',
        inputOptions: inputOptions,
        showCancelButton: true,
        preConfirm: function (value) {
            return new Promise(function (resolve, reject) {
                if(value=="custom")
                    mooOpenCustomQtyWindow(event,item_id,callback);
                else
                    callback(event,item_id,value);

            });
        }
    }).then(function () {},function (dismiss) {});
}
function mooOpenCustomQtyWindow(event,item_id,callback) {
    swal({
        title: 'Enter the quantity',
        input: 'text',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        inputValidator: function (value) {
            return new Promise(function (resolve, reject) {
                if (value != "" && parseInt(value)>0) {
                    callback(event,item_id,parseInt(value));
                } else {
                    reject('You need to write a number')
                }
            })
        }
    }).then(function () {},function () {})
}
//Click on order button for items without modifiers
function moo_clickOnOrderBtn(event,item_id,qty) {
    var body = {
        item_uuid:item_id,
        item_qty:qty,
        item_modifiers:{}
    };
    /* Add to cart the item */
    jQuery.post(moo_RestUrl+"moo-clover/v1/cart", body,function (data) {
        if(data != null)
        {
            if(data.status == "error")
            {
                swal({
                    title:data.message,
                    type:"error"
                });
            }
            else
            {
                swal({
                    title:data.name,
                    text:"Added to cart",
                    timer:3000,
                    type:"success"
                });
            }
        }
        else
        {
            swal({
                title:"Item not added, try again",
                type:"error"
            });
        }
    }).fail(function ( data ) {
        swal({
            title:"Item not added, try again",
            text:"Check your internet connection or contact us",
            type:"error"
        });
        console.log(data);
    }).done(function ( data ) {
        if(typeof data.nb_items != "undefined")
            jQuery("#moo-cartNbItems").text(data.nb_items)
    });

}
//Click on order button for an item with modifiers
function moo_clickOnOrderBtnFIWM(event,item_id,qty) {
    event.preventDefault();
    //Change button content to loading
    var target = event.target;
    var old_text = jQuery(target).text();
    jQuery(target).text("Loading options");

    jQuery.get(moo_RestUrl+"moo-clover/v1/items/"+item_id, function (data) {
        //Change butn text
        jQuery(target).text(old_text);

        if(data != null)
        {
            if(data.modifier_groups.length > 0)
            {
                if(typeof mooBuildModifiersPanel == "function")
                {
                    //get modifier settings Object.keys(window.moo_mg_setings).length > 0
                    if(false) {
                        mooBuildModifiersPanel(data.modifier_groups,item_id,qty,window.moo_mg_setings);
                    } else {
                        mooBuildModifiersPanel(data.modifier_groups,item_id,qty);
                    }
                    swal.close();
                }
                else
                {
                    swal('Try again','Please refresh the page, An error has occurred','error');
                }

            }
            else
                moo_clickOnOrderBtn(event,item_id,qty);
        }
        else
        {
            //Change button text
            jQuery(target).text(old_text);
            swal({ title: "Error", text: 'We cannot Load the options for this item, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
        }
    }).fail(function (data) {
        //Change button text
        jQuery(target).text(old_text);
        swal({ title: "Error", text: 'We cannot Load the options for this item, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
    });

}
//show order info block
function moo_show_oderInfo(uuid) {
    var id = "moo_order_info_"+uuid;
   // jQuery("#moo_order_info_"+uuid).toggleClass('moo_opened');
    jQuery('div[id^="moo_order_info_"]').each(function( index, element ) {

        if(element.id === id){
            if(jQuery(element).hasClass('moo_opened')) {
                jQuery(element).slideUp();
                jQuery(element).removeClass('moo_opened');
            } else {
                jQuery(element).slideDown();
                jQuery(element).addClass('moo_opened');
            }
        } else {
            jQuery(element).slideUp();
            jQuery(element).removeClass('moo_opened');
        }

    });
}
function moo_getAddressFromForm()
{
    var address = {};
    address.address =  jQuery('#cp_MooAddress').val();
    address.line2 =  jQuery('#cp_MooAddress2').val();
    address.city =  jQuery('#cp_MooCity').val();
    address.state =  jQuery('#cp_MooState').val();
    address.zipcode =  jQuery('#cp_MooZipcode').val();
    address.lat =  jQuery('#cp_MooLat').val();
    address.lng =  jQuery('#cp_MooLng').val();
    address.country =  "";
    return address;
}
function moo_getprofilInfoFromForm()
{
    var user = {};
    user.name =  jQuery('#cp_MooName').val();
    user.email =  jQuery('#cp_MooEmail').val();
    user.phone =  jQuery('#cp_MooPhone').val();
    return user;
}
function moo_initMapAddress()
{
    var location = {};
    location.lat = parseFloat(document.getElementById("inputMooLat").value);
    location.lng = parseFloat( document.getElementById("inputMooLng").value);
    var map = new google.maps.Map(document.getElementById('MooMapAddingAddress'), {
        zoom: 16,
        center: location
    });

    var marker = new google.maps.Marker({
        position: location,
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
        jQuery
            .post(moo_params.ajaxurl,{'action':'moo_customer_addAddress','address':address.address,'line2':address.line2,'city':address.city,'state':address.state,'zipcode':address.zipcode,"lat":address.lat,"lng":address.lng}, function (data) {
                if(data.status == 'failure' || data.status == 'expired')
                {
                    swal({ title: "Your session has been expired",text:"Please login again",   type: "error",   confirmButtonText: "Login again" });
                    jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
                    moo_refresh_page()
                } else {
                    if(data.status == 'success') {
                        jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
                        moo_my_account_addresses(e);
                    } else {
                        swal({ title: "Address not added to your account",text:"Please try again or contact us",   type: "error",   confirmButtonText: "Try again" });
                        jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
                    }
                }

            })
            .fail(function(data) {
                console.log(data.responseText);
                jQuery(e.target).html('Confirm and add address').attr('onclick','moo_addAddress(event)');
                swal({ title: "Connection lost",text:"Please try again",   type: "error",   confirmButtonText: "Try again" });
            });

    }

}
function moo_updateProfil(e) {
    e.preventDefault();
    var user = moo_getprofilInfoFromForm();
    if(user.name === '' || user.email === '' || user.phone === '') {
        swal({ title: "Fill all information",text:"Please fill all information (name, email and phone)",   type: "error",   confirmButtonText: "Try again" });
    } else {
        swal({
            title: 'Please wait',
            text: 'We are updating your personal information',
            allowOutsideClick: false,
            showConfirmButton: false,
            showCancelButton: false
        }).then(function () {},function (dismiss) {});

        jQuery.post(moo_RestUrl+"moo-clover/v1/customers",user,function(response){
            if(response.status=='success'){
                swal({ title: "Updated",text:'your personal information were updated',   type: "success" });
            } else {
                swal({ title: "Connection lost",text:"Please try again",   type: "error",   confirmButtonText: "Try again" });
            }
        }).fail(function(data) {
            swal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
        });
    }
}
function moo_changePassword(e) {
    e.preventDefault();
    var currentPassword = jQuery("#cp_MooCurrentPassword").val();
    var newPassword = jQuery("#cp_MooNewPassword").val();
    var repeatnewPassword = jQuery("#cp_MooRepeatNewPassword").val();

    if(currentPassword === '' || newPassword === '' || repeatnewPassword === '') {
        swal({ title: "Fill all fields",text:"Please fill the current and new password",   type: "error",   confirmButtonText: "OK" });
        return;
    } else {
        if(newPassword !== repeatnewPassword) {
            swal({ title: "Attention !",text:"Password does not match the confirm password",   type: "error",   confirmButtonText: "OK" });
            return;
        } else {
            jQuery.post(moo_RestUrl+"moo-clover/v1/customers/password",{"current_password":currentPassword,"new_password":newPassword},function(response){
                if(response.status=='success'){
                    swal({ title: "Updated",text:'Your password were updated',   type: "success" });
                } else {
                    swal({ title: "Not updated",text:response.message,   type: "error",   confirmButtonText: "Try again" });
                }
            }).fail(function(data) {
                swal({ title: "Error",text:"An error has occurred please try again",   type: "error",   confirmButtonText: "Try again" });
            });
        }
    }
}
function moo_ConfirmAddressOnMap(e)
{

    e.preventDefault();
    var address = moo_getAddressFromForm();
    if( address.address == '' || address.city == '')
    {
        swal({ title: "Address missing",text:"Please enter your address",   type: "error",   confirmButtonText: "OK" });
        return;
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
        }
        else
        {
            swal({ title: "We weren't able to locate this address,try again",text:"",   type: "error",   confirmButtonText: "OK" });
        }
    });

}
function moo_delete_address(event,address_id) {
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
                        if(data.status == 'failure' || data.status == 'expired') {
                            swal({ title: "Your session has been expired",text:"Please login again",   type: "error",   confirmButtonText: "Login again" });
                            moo_refresh_page();
                        } else {
                            if(data.status == 'success') {
                                swal("Deleted!", "Your address has been deleted.", "success");
                                moo_my_account_addresses(event);
                            } else {
                                swal({ title: "Address not deleted",text:"Please try again or contact us",   type: "error",   confirmButtonText: "Try again" });
                            }
                        }
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
function moo_refresh_page() {
    location.reload();
}