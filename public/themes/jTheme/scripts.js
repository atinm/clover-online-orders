/**
* Created by Mohammed EL BANYAOUI on 12/13/2017.
*/
jQuery(document).ready(function() {
    window.moo_theme_setings = [];
    window.moo_mg_setings = {};
    window.nb_items_in_cart = 0;

    /* Load the them settings then draw tha layout an get the categories with the first five items */
    jQuery.get(moo_RestUrl+"moo-clover/v1/theme_settings/jTheme", function (data) {
        if(data != null && data.settings != null)
        {
            window.moo_theme_setings = data.settings;
            window.nb_items_in_cart  = data.nb_items;
        }
    }).done(function () {
        //Check the hash to see if we will load one category
        var hash = window.location.hash;
        if (hash !== "") {
            var params = hash.split("-");
            if(params.length === 2) {
                switch (params[0]) {
                    case '#cat' :
                        MooLoadBaseStructure('#moo_OnlineStoreContainer',function () {
                            jQuery.get(moo_RestUrl+"moo-clover/v1/categories/"+params[1]+"/items", function (data) {
                                if(data.items !== null) {
                                    moo_renderItems(data);
                                } else {
                                    //TODO: Error message
                                    console.log("Error :  No item found");
                                }

                            }).fail(function () {
                                MooHideLoading();
                                var t ='<button class="osh-btn" onclick="moo_backToCategories(\''+params[1]+'\')">' +
                                    '<span class="label">Back to Categories</span>'+
                                    '</button>';
                                jQuery("#moo-onlineStore-items").html('<h3>No item found</h3>'+t);
                            });
                        });
                        MooSetLoading();
                        break;
                }
            } else {
                MooLoadBaseStructure('#moo_OnlineStoreContainer',mooGetCategories);
                MooSetLoading();
            }

            console.log(params);

        } else {
            MooLoadBaseStructure('#moo_OnlineStoreContainer',mooGetCategories);
            MooSetLoading();
        }
    });

    /* Load the modifiers settings and save them on a window's variable */
    jQuery.get(moo_RestUrl+"moo-clover/v1/mg_settings", function (data) {
        if(data != null && data.settings != null)
        {
            window.moo_mg_setings = data.settings;
        }
    });

});
function MooLoadBaseStructure(elm_id,callback) {
    var html = '<div class="moo-row">'+
        '<div  class="moo-is-sticky moo-new-icon" onclick="mooShowCart(event)">' +
        '<div class="moo-new-icon__count" id="moo-cartNbItems">'+((window.nb_items_in_cart>0)?window.nb_items_in_cart:'')+'</div>' +
        '<div class="moo-new-icon__cart">' +
        '</div></div>'+
        '<div id="MooLoadingSection"></div>'+
        '</div>'+
        '<div class="moo-row">'+
        '<div id="moo-onlineStore-categories">'+
        '</div>'+
        '<div id="moo-onlineStore-items">'+
        '</div>'+
        '</div>';
    /* Adding the structure the the html page */
    jQuery(elm_id).html(html);
    callback();
}
function MooSetLoading() {
    jQuery('#MooLoadingSection').show();
}
function MooHideLoading() {
    jQuery('#MooLoadingSection').hide();
}

//get all the categories of the store
function mooGetCategories() {
    jQuery.get(moo_RestUrl+"moo-clover/v1/categories", function (data) {
        if(data!=null && data.length>0)
            moo_renderCategories(data);
        else
        {
            var element = document.getElementById("moo-onlineStore-categories");
            var html     = 'You don\'t have any category please import your inventory';
            jQuery(element).html(html);
        }
    });

}
function  moo_renderCategories($cats) {
    // the categories section in the DOM
    var element = document.getElementById("moo-onlineStore-categories");

    // Here your code that will convert the data to html
    var html = '<div class="moo-row">';
    for(var i in $cats){
        var category = $cats[i];
        if(category.uuid !== undefined ) {
            if(category.image_url !== null && category.image_url !== "") {
                var imageCatUrl = category.image_url;
            } else {
                var imageCatUrl = moo_params['plugin_img']+'/noImg2.png';
            }

            html +='<div class="moo-col-md-3">'+
                '<a class="link" href="#cat-'+ category.uuid.toLowerCase() +'" id="cat-'+category.uuid.toLowerCase()+'" data-cat-id="'+category.uuid.toLowerCase()+'" onclick="MooClickOnCategory(event,this)">';
            html += '<div class="image-wrapper" style="background: url('+imageCatUrl+') no-repeat center;background-size:100%;"></div>';


            html +='<div class="cat-name-wrapper"><span class="moo-category-name">'+
                category.name+
                '</span></div></a></div>';
        }

    }
    html    += "</div>";

    // Then add theme to dom and do some changes after finsihed the rendriign
    jQuery(element).html(html).promise().done(function() {
        MooHideLoading();
        console.log("Categories loaded")

    });
}

function MooClickOnCategory(event,elm)
{
    //event.preventDefault();
    var cat_id = jQuery(elm).attr('data-cat-id');
    MooSetLoading();
    jQuery("#moo-onlineStore-categories").hide();
    jQuery.get(moo_RestUrl+"moo-clover/v1/categories/"+cat_id+"/items", function (data) {
        if(data.items !== null) {
            moo_renderItems(data);
        } else {
            //TODO: Error message
            console.log("Error :  No item found");
        }

    });

}
function moo_renderItems(data) {
    // the categories section in the DOM
    var element = document.getElementById("moo-onlineStore-items");
    // Here your code that will convert the data to html
    var html = '<div class="moo-row">';
    html +='<h3>'+data.name+'</h3>';
    for(var i in data.items){
        var item = data.items[i];
        if(typeof item =='object') {
            var item_price = parseFloat(item.price);
            item_price = item_price/100;
            item_price = item_price.toFixed(2);

            if(item.price > 0 && item.price_type === "PER_UNIT")
                item_price += '/'+item.unit_name;
            if(item.image !== null && item.image.url !== null && item.image.url !== "")
            {
                var itemimgUrl = item.image.url;
            }
            else
            {
                var itemimgUrl = moo_params['plugin_img']+'/noImg.png';
            }

            if( i % 4 === 0) {
                html +='<div class="moo-col-md-3" style="clear: both">';
            } else {
                html +='<div class="moo-col-md-3">';

            }
                html +='<a title="'+item.description+'" class="link" href="#item-'+item.uuid.toLowerCase()+'" data-item-id="'+item.uuid.toLowerCase()+'" onclick="MooClickOnItem(event,this)">';
                html += '<div class="image-wrapper" style="background: url('+itemimgUrl+') no-repeat center;background-size:100%;"></div>';


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
                if(window.moo_theme_setings.onePage_qtyWindow !== null && window.moo_theme_setings.jTheme_qtyWindow === "on")
                {
                    if(item.has_modifiers)
                    {
                        if(window.moo_theme_setings.jTheme_qtyWindowForModifiers !== null && window.moo_theme_setings.jTheme_qtyWindowForModifiers === "on")
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
    }
    html    += "</div>";
    if(data.uuid) {
        html    += '<div class="moo-back-to-categories"><button class="osh-btn" onclick="moo_backToCategories(\''+data.uuid.toLowerCase()+'\')">' +
                '<span class="label">Back to Categories</span>'+
                '</button></div>';
    }


    // Then add theme to dom and do some changes after finsihed the rendriign
    jQuery(element).html(html).promise().done(function() {
        jQuery("#moo-onlineStore-items").show();
        MooHideLoading();
        console.log("Items loaded")

    });
}

function MooClickOnItem(event,elm) {
    event.preventDefault();
    var item_id = jQuery(elm).attr('data-item-id');
}
function moo_backToCategories(cat_id) {
    window.location.hash = '';
    jQuery("#moo-onlineStore-items").hide();
    MooSetLoading();
    if(jQuery("#moo-onlineStore-categories").html()===''){
        mooGetCategories();
    } else {
        jQuery("#moo-onlineStore-categories").show();
        MooHideLoading();
    }
    var top = (jQuery("#cat-"+cat_id).offset() != null)?jQuery("#cat-"+cat_id).offset().top:""; //Getting Y of target element
    window.scrollTo(0, top);
}
function mooOpenQtyWindow(event,item_id,stockCount,callback) {
    event.preventDefault();
    var inputOptions = new Promise(function (resolve) {
        if(stockCount == "not_tracking_stock" ||  stockCount == "tracking_stock" )
        {
            resolve({
                "1":"1","2":"2","3":"3","4":"4","5":"5","6":"6","7":"7","8":"8","9":"9","10":"10","custom":"Custom Quantity"
            })
        }
        else
        {
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
                    if(Object.keys(window.moo_mg_setings).length > 0)
                    {
                        if(window.moo_mg_setings.inlineDisplay && window.moo_mg_setings.inlineDisplay == true ) {
                            var h = '<div class="moo-col-lg-12 moo-col-md-12 moo-col-sm-12 moo-col-xs-12" id="moo-modifiersContainer-for-'+item_id+'"></div>';
                            jQuery("#moo-onlineStore-items").append(h);
                            jQuery("html, body").animate({
                                scrollTop: jQuery("#moo-modifiersContainer-for-"+item_id).offset().top
                            }, 600);
                        }
                        mooBuildModifiersPanel(data.modifier_groups,item_id,qty,window.moo_mg_setings);
                    }
                    else
                    {
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
function formatPrice (p) {
    return p.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}
/* Cart functions */
function mooShowCart(event) {
    if(typeof event != "undefined")
        event.preventDefault();

    var element = jQuery("#moo-panel-cart>.moo-panel-cart-container>.moo-panel-cart-content");
    var cart_element =jQuery("#moo-panel-cart>.moo-panel-cart-container>.moo-panel-cart-content") ;

    swal({
        html:
        '<div class="moo-msgPopup">Loading your cart</div>' +
        '<img src="'+ moo_params['plugin_img']+'/loading.gif" class="moo-imgPopup"/>',
        showConfirmButton: false
    });

    var cart_html = '<div class="moo-row moo-cart-heading">'+
        '<div class="moo-col-lg-6 moo-col-md-6 moo-col-sm-5 moo-col-xs-5 moo-cart-line-itemName">ITEM</div>'+
        '<div class="moo-col-lg-2 moo-col-md-2 moo-col-sm-2 moo-col-xs-2 moo-cart-line-itemQty">QTY</div>'+
        '<div class="moo-col-lg-2 moo-col-md-2 moo-col-sm-3 moo-col-xs-3 moo-cart-line-itemPrice">SUB-TOTAL</div>'+
        '<div class="moo-col-lg-2 moo-col-md-2 moo-col-sm-2 moo-col-xs-2 moo-cart-line-itemActions">EDIT</div>'+
        '</div>'+
        '<div class="moo-cart-container">';

    jQuery.get(moo_RestUrl+"moo-clover/v1/cart", function (data) {
        if(typeof data != 'undefined' && data != null)
        {
            cart_html += '<div class="moo-row moo-cart-content">';
            if(data.items != null && Object.keys(data.items).length>0)
            {
                jQuery.each(data.items,function(line_id,line)

                {
                    var price = parseFloat(line.item.price)/100;
                    var line_price = price * line.qty;

                    cart_html+='<div class="moo-row moo-cart-line" >'+
                        '<div class="moo-col-lg-6 moo-col-md-6 moo-col-sm-5 moo-col-xs-5 moo-cart-line-itemName">';
                    //check if cart line contain modifiers
                    if(line.modifiers.length > 0)
                    {
                        cart_html += line.item.name;
                        cart_html += '<div class="moo-cart-line-modifiers">';
                        for(var $j=0;$j<line.modifiers.length;$j++)
                        {
                            cart_html += ''+line.modifiers[$j].qty;
                            cart_html += 'x '+line.modifiers[$j].name;
                            if(line.modifiers[$j].price>0)
                            {
                                line_price += ((parseFloat(line.modifiers[$j].price)/100)*(parseInt(line.modifiers[$j].qty)))*line.qty;
                                cart_html += ' <span style="color: #484848;">$'+(parseFloat(line.modifiers[$j].price)/100).toFixed(2)+"</span>";
                            }
                            cart_html += '<br/>';
                        }
                        cart_html += '</div>';

                    }
                    else
                    {
                        cart_html += line.item.name;
                    }
                    cart_html+='</div>';
                    cart_html+='<div class="moo-col-lg-2 moo-col-md-2 moo-col-sm-2 moo-col-xs-2  moo-cart-line-itemQty">'+line.qty+'</div>';
                    cart_html+= '<div class="moo-col-lg-2 moo-col-md-2 moo-col-sm-3 moo-col-xs-3  moo-cart-line-itemPrice">$'+formatPrice(line_price.toFixed(2))+'</div>';
                    cart_html+= '<div class="moo-col-lg-2 moo-col-md-2 moo-col-sm-2 moo-col-xs-2  moo-cart-line-itemActions">';
                    cart_html+=  '<i style="cursor: pointer;margin-right: 10px;margin-left: 10px" class="fas fa-pen-square" aria-hidden="true" onclick="mooUpdateSpecialInsinCart(\''+line_id+'\',\''+line.special_ins+'\')"></i>'+
                        '<i style="cursor: pointer;margin-right: 10px;margin-left: 10px" class="fas fa-trash" aria-hidden="true" onclick="mooRemoveLineFromCart(\''+line_id+'\')"></i></div></div>';
                });
                cart_html += '</div>';
                //Set teh cart total
                if(data.total != null && data.total != false)
                    cart_html +=' <div class="moo-row moo-cart-totals">'+
                        '<div class="moo-row moo-cart-total moo-cart-total-subtotal">'+
                        '<div class="moo-col-lg-9 moo-col-md-9 moo-col-sm-7 moo-col-xs-7 moo-cart-total-label">SUBTOTAL</div>'+
                        '<div class="moo-col-lg-3 moo-col-md-3 moo-col-sm-5 moo-col-xs-5  moo-cart-total-price">$'+formatPrice(data.total.sub_total)+'</div>'+
                        '</div>'+
                        '<div class="moo-row moo-cart-total moo-cart-total-tax">'+
                        '<div class="moo-col-lg-9 moo-col-md-9 moo-col-sm-7 moo-col-xs-7 moo-cart-total-label">TAX</div>'+
                        '<div class="moo-col-lg-3 moo-col-md-3 moo-col-sm-5 moo-col-xs-5  moo-cart-total-price">$'+formatPrice(data.total.total_of_taxes)+'</div>'+
                        '</div>'+
                        '<div class="moo-row moo-cart-total moo-cart-total-grandtotal">'+
                        '<div class="moo-col-lg-8 moo-col-md-8 moo-col-sm-6 moo-col-xs-6 moo-cart-total-label">TOTAL</div>'+
                        '<div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-6 moo-col-xs-6 moo-cart-total-price">$'+formatPrice(data.total.total)+'</div>'+
                        '</div>'+
                        '</div>'+
                        '<div class="moo-row" style="font-size: 11px;text-align: center;">*Quantity can be updated during checkout*</div>';
                //Set checkout btn
                //cart_html +='<div class="moo-row moo-cart-btns">'+
                // '<a href="'+moo_CheckoutPage+'" class="moo-btn moo-btn-danger BtnCheckout">CHECKOUT</a>'+
                '</div></div>';
                //element.html(cart_html);
                swal({
                    html:cart_html,
                    width: 700,
                    showCancelButton: true,
                    cancelButtonText : 'Close',
                    confirmButtonText : '<a href="'+moo_CheckoutPage+'" style="color:#ffffff">CHECKOUT</a>'
                }).then(function () {
                    console.log("conformed");
                    window.location.href = moo_CheckoutPage;
                },function () {

                });
            }
            else
            {

                cart_html +='<div class="moo-cart-empty">Your cart is empty</div> '+
                    '</div>';
                cart_html += '</div></div>';
                // element.html(cart_html);
                swal({
                    html:cart_html,
                    width: 700,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText : 'Close'
                });
            }
        }
        else
        {

            cart_html += '<div class="moo-row moo-cart-content">';
            cart_html +='<div class="moo-cart-empty">Your cart is empty</div>'+
                '</div>';
            cart_html += '</div></div>';
            // element.html(cart_html);
            swal({
                html:cart_html,
                width: 700,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText : 'Close'
            });

        }
    }).fail(function(data){
        console.log('Fail to get the cart');
        cart_html +='<div class="moo-cart-empty">Error in loading your cart, please refresh the page</div> '+
            '</div>';
        // element.html(cart_html);
        swal({
            html:cart_html,
            width: 700,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText : 'Close'
        });
    });
}
function mooRemoveLineFromCart(line_id) {
    swal({
        title: 'Are you sure you want to delete this item',
        type: 'warning',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        preConfirm: function () {
            return new Promise(function (resolve) {

                var body = {
                    line_id:line_id
                };
                /* Add to cart the item */
                jQuery.post(moo_RestUrl+"moo-clover/v1/cart/remove", body,function (data) {
                    if(data != null && data.status == 'success')
                    {
                        resolve(true);
                    }
                    else
                    {
                        resolve(false);
                    }
                }).fail(function ( data ) {
                    resolve(false);
                }).done(function ( data ) {
                    if(typeof data.nb_items != "undefined")
                        jQuery("#moo-cartNbItems").text(data.nb_items)
                });
            })
        },
    }).then(function (data) {
        if(data)
            swal({
                title:"Deleted!",
                type:'success'

            });
        else
            swal({
                title:"Item not deleted, try again",
                type:'error'

            });

    }, function (dismiss) {
        // dismiss can be 'cancel', 'overlay',
        // 'close', and 'timer'
        //  if (dismiss === 'cancel') {
        // }
    })
}
function mooUpdateSpecialInsinCart(line_id,current_special_ins) {
    swal({
        title: 'Add special Instructions',
        input: 'textarea',
        inputValue: current_special_ins,
        inputPlaceholder: 'Type your instructions here, additional charges may apply and not all changes are possible',
        showCancelButton: true,
        confirmButtonText: 'Add',
        showLoaderOnConfirm: true,
        preConfirm: function (special_ins) {
            return new Promise(function (resolve, reject) {
                if(special_ins.length>255)
                {
                    reject('Text too long, You cannot add more than 250 char')
                }
                else
                {
                    var body = {
                        line_id:line_id,
                        special_ins : special_ins
                    };

                    jQuery.post(moo_RestUrl+"moo-clover/v1/cart/update", body,function (data) {
                        if(data != null && data.status == 'success')
                        {
                            resolve(true);
                        }
                        else
                        {
                            resolve(false);
                        }
                    }).fail(function ( data ) {
                        resolve(false);
                    });
                }
            })
        },
        allowOutsideClick: false
    }).then(function (data) {
        if(data)
        /* swal({
             type: 'success',
             title: 'Done',
             html: 'Special instructions submitted'
         })*/
            mooShowCart();
        else
            swal({
                type: 'error',
                title: 'Not added',
                html: 'Special instructions not submitted try again'
            })
    }, function (dismiss) {
        // dismiss can be 'cancel', 'overlay',
        // 'close', and 'timer'
        if (dismiss === 'cancel') {
            mooShowCart();
        }
    });

}
