(function( $ ) {
	'use strict';
    swal.setDefaults({ customClass: 'moo-custom-dialog-class' });
    jQuery( document ).ready(function($) {
        jQuery('#moo_OnlineStoreContainer').removeClass('moo_loading');
        if(document.getElementById("moo_OnlineStoreContainer") !== null && document.getElementById("moo-checkout")  === null && document.getElementById("moo-my-account")  === null) {
           if(moo_params.custom_sa_title !== "") {
               swal({
                   title:moo_params.custom_sa_title,
                   text:moo_params.custom_sa_content
               });
           }
        } else {
            if(document.getElementById("moo-checkout") !== null){
                if(moo_params.custom_sa_title !== "" && moo_params.custom_sa_onCheckoutPage === "on") {
                    swal({
                        title:moo_params.custom_sa_title,
                        text:moo_params.custom_sa_content
                    });
                }
            }
        }
    });

})(jQuery);


function moo_btn_addToCartFIWM(event,item_uuid,qty) {

    event.preventDefault();
    //Change button content to loading
    var target = event.target;
    jQuery(target).text('Loading options...');

    jQuery.get(moo_RestUrl+"moo-clover/v1/items/"+item_uuid, function (data) {
        //Change button text
        jQuery(target).text("ADD TO CART");

        if(data != null) {
                mooBuildModifiersPanel(data.modifier_groups,item_uuid,qty);
        } else {
            //Change butn text
            jQuery(target).text("ADD TO CART");
            swal({ title: "Error", text: 'We cannot Load the options for this item, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
        }
    }).fail(function (data) {
        //Change butn text
        jQuery(target).text("ADD TO CART");
        swal({ title: "Error", text: 'We cannot Load the options for this item, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
    });
}
function moo_btn_addToCart(event,item_uuid,qty)
{
    event.preventDefault();
    //var qty = parseInt(jQuery("#moo-itemQty-for-"+item_id).val());

    var body = {
        item_uuid:item_uuid,
        item_qty:qty,
        item_modifiers:{}
    };
    swal({
        html:
        '<div class="moo-msgPopup">Adding the item to your cart</div>' +
        '<img src="'+ moo_params['plugin_img']+'/loading.gif" class="moo-imgPopup"/>',
        showConfirmButton: false
    });

    /* Add to cart the item */
    jQuery.post(moo_RestUrl+"moo-clover/v1/cart", body,function (data) {
        if(data != null)
        {
            swal({
                title:"Item added",
                showCancelButton: true,
                cancelButtonText: 'Close',
                confirmButtonText: 'Cart page',
                type:"success"
            }).then(function () {
                window.location.replace(moo_CartPage)
            });
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
    }).done(function ( data ) {
        console.log(data);
    });
}

function moo_openQty_Window(event,item_uuid,callback)
{
    event.preventDefault();
    var inputOptions = new Promise(function (resolve) {
        resolve({
        "1":"1","2":"2","3":"3","4":"4","5":"5","6":"6","7":"7","8":"8","9":"9","10":"10","custom":"Custom Quantity"
        });
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
                {
                    moo_OpenCustomQtyWindow(event,item_uuid,callback);
                }
                else
                {
                    callback(event,item_uuid,value);
                    swal.close();
                }

            });
        }
    }).then(function () {},function (dismiss) {});
}

function moo_OpenCustomQtyWindow(event,item_id,callback)
{
    swal({
        title: 'Enter the quantity',
        input: 'text',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        inputValidator: function (value) {
            return new Promise(function (resolve, reject) {
                if (value != "" && parseInt(value)>0) {
                    callback(event,item_id,parseInt(value));
                    swal.close();
                } else {
                    reject('You need to write a number')
                }
            })
        }
    }).then(function () {},function () {})
}