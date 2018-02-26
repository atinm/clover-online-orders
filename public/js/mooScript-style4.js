/**
 * Created by Mohammed EL BANYAOUI on 9/11/2017.
 */
jQuery(document).ready(function()
{
    window.moo_theme_setings = moo_themeSettings;
    window.moo_mg_setings = {};
    window.nb_items_in_cart = 0;
    window.header_height = window.moo_theme_setings.onePage_categoriesTopMargin;
    var container_top = jQuery('#moo_OnlineStoreContainer').offset().top;
    window.height = (jQuery(window).width()>768)?(container_top>0)?(window.header_height):'':'';
    window.width  = 267;

    /* Load the modifiers settings and save them on a window's variable */
    jQuery.get(moo_RestUrl+"moo-clover/v1/mg_settings", function (data) {
        if(data != null && data.settings != null)
        {
            window.moo_mg_setings = data.settings;
        }
    });

    /* a listener when scrolling to fix the tha category section */
    jQuery(window).scroll(function(){
        var container_top = jQuery('#moo_OnlineStoreContainer').offset().top;
        if (jQuery(window).scrollTop() > (container_top-header_height))
        {
            //console.log(navigator.userAgent);
            if( window.innerWidth < 768 ) {
                jQuery(".moo-stick-to-content").addClass('moo-fixed').width('100%').css("top",window.height+'px')
            }
            else
            {
                jQuery(".moo-stick-to-content").addClass('moo-fixed').width(window.width).css("top",window.height+'px');
            }


        }
        else
        {
            jQuery(".moo-stick-to-content").removeClass('moo-fixed');
        }
    });
    var hash = window.location.hash;
    if (hash = "") {
        var top = (jQuery(hash).offset() != null)?jQuery(hash).offset().top:""; //Getting Y of target element
        window.scrollTo(0, top);
    }
    // Custome css or theme customisation applicatio
    if(window.moo_theme_setings !== null && typeof window.moo_theme_setings !== "undefined")
    {
        //Change the categories background color
        if(window.moo_theme_setings.onePage_categoriesBackgroundColor !== null)
        {
            jQuery(".moo-bg-dark").css("background-color",window.moo_theme_setings.onePage_categoriesBackgroundColor );
            jQuery(".moo-menu-category .moo-menu-category-title").css("background-color",window.moo_theme_setings.onePage_categoriesBackgroundColor );
            //Change the cart icon colors
            jQuery(".moo-new-icon").css("background-color",window.moo_theme_setings.onePage_categoriesBackgroundColor );
            jQuery(".moo-new-icon").css("border-color",window.moo_theme_setings.onePage_categoriesBackgroundColor );
        }
        //Change the categories font color
        if(window.moo_theme_setings.onePage_categoriesFontColor !== null)
        {
            jQuery(".moo-nav-menu li a").css("color",window.moo_theme_setings.onePage_categoriesFontColor );
            jQuery(".moo-menu-category .moo-menu-category-title .moo-title").css("color",window.moo_theme_setings.onePage_categoriesFontColor );

            //Change the cart icon colors
            jQuery(".moo-new-icon").css("color",window.moo_theme_setings.onePage_categoriesFontColor );
            jQuery(".moo-new-icon__cart-panier").css("color",window.moo_theme_setings.onePage_categoriesFontColor );
            jQuery(".moo-new-icon__group").css("fill",window.moo_theme_setings.onePage_categoriesFontColor );
        }
        //Change the categories font-family
        if(window.moo_theme_setings.onePage_fontFamily !== null)
        {
            jQuery(".moo-nav-menu li a").css("font-family",window.moo_theme_setings.onePage_fontFamily );
            jQuery(".moo-menu-category .moo-menu-category-title .moo-title").css("font-family",window.moo_theme_setings.onePage_fontFamily );
        }
    }

    moo_ZoomOnImages();
});

function MooCLickOnCategory(event,elm) {
    event.preventDefault();
    var page = jQuery(elm).attr('href');
    var speed = 750;
    jQuery('html, body').animate( { scrollTop: jQuery(page).offset().top }, speed ); // Go
    return false;
}

function mooClickOnLoadMoreItems(event,cat_id,cat_name)
{
    event.preventDefault();
    var html = '';
    swal({
        html:
        '<div class="moo-msgPopup">Loading '+cat_name+'\'s items</div>' +
        '<img src="'+ moo_params['plugin_img']+'/loading.gif" class="moo-imgPopup"/>',
        showConfirmButton: false
    });
    jQuery.get(moo_RestUrl+"moo-clover/v1/categories/"+cat_id+"/items", function (data) {
        if(data != null && data.items != null && data.items.length > 0)
        {
            var count = data.items.length;
            var html ='';
            for(var i in data.items){
                var item = data.items[i];

                var item_price = parseFloat(item.price);
                item_price = item_price/100;
                item_price = item_price.toFixed(2);

                if(item.price > 0 && item.price_type == "PER_UNIT")
                    item_price += '/'+item.unit_name;

                html += '<div class="moo-menu-item moo-menu-list-item" >'+
                    ' <div class="moo-row">';

                if(item.image != null && item.image.url != null && item.image.url != "")
                {
                    html += '    <div class="moo-col-lg-2 moo-col-md-2 moo-col-sm-12 moo-col-xs-12 moo-image-zoom">'+
                        '<a href="'+item.image.url+'" data-effect="mfp-zoom-in"><img src="'+item.image.url+'" class="moo-img-responsive moo-image-zoom"></a>'+
                        '    </div>'+
                        '    <div class="moo-col-lg-6 moo-col-md-6 moo-col-sm-12 moo-col-xs-12">'+
                        '         <div class="moo-item-name">'+item.name+'</div>'+
                        '         <span class="moo-text-muted moo-text-sm">'+item.description+'</span>'+
                        '    </div>';
                }
                else
                {
                    html += '    <div class="moo-col-lg-8 moo-col-md-8 moo-col-sm-12 moo-col-xs-12">'+
                        '         <div class="moo-item-name">'+item.name+'</div>'+
                        '         <span class="moo-text-muted moo-text-sm">'+item.description+'</span>'+
                        '    </div>';
                }
                if(parseFloat(item.price) == 0)
                {
                    html +=     '    <div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-12 moo-col-xs-12 moo-text-sm-right">'+
                                '    <span></span>';
                }
                else
                {
                    html +=     '    <div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-12 moo-col-xs-12 moo-text-sm-right">'+
                        '    <span class="moo-price">$'+item_price+'</span>';
                }

                if(item.stockCount == "out_of_stock")
                {
                    html += '<button class="moo-btn-sm moo-hvr-sweep-to-top">Out Of Stock</button>';
                }
                else
                {
                    //Checking the Qty window show/hide and add add to cart button
                    if(window.moo_theme_setings.onePage_qtyWindow != null && window.moo_theme_setings.onePage_qtyWindow == "on")
                    {
                        if(item.has_modifiers)
                        {
                            if(window.moo_theme_setings.onePage_qtyWindowForModifiers != null && window.moo_theme_setings.onePage_qtyWindowForModifiers == "on")
                                html += '<button class="moo-btn-sm moo-hvr-sweep-to-top" onclick="mooOpenQtyWindow(event,\''+item.uuid+'\',\''+item.stockCount+'\',moo_clickOnOrderBtnFIWM)">Choose Qty & Options</button>';
                            else
                                html += '<button class="moo-btn-sm moo-hvr-sweep-to-top" onclick="moo_clickOnOrderBtnFIWM(event,\''+item.uuid+'\',1)">Choose Options & Qty</button>';
                        }
                        else
                            html += '<button class="moo-btn-sm moo-hvr-sweep-to-top" onclick="mooOpenQtyWindow(event,\''+item.uuid+'\',\''+item.stockCount+'\',moo_clickOnOrderBtn)">Add to cart</button>';

                    }
                    else
                    {
                        if(item.has_modifiers)
                            html += '<button class="moo-btn-sm moo-hvr-sweep-to-top" onclick="moo_clickOnOrderBtnFIWM(event,\''+item.uuid+'\',1)">Choose Options & Qty </button>';
                        else
                            html += '<button class="moo-btn-sm moo-hvr-sweep-to-top" onclick="moo_clickOnOrderBtn(event,\''+item.uuid+'\',1)">Add to cart</button>';

                    }

                }

                html += '</div>';
                if(item.has_modifiers)
                    html += '<div class="moo-col-lg-12 moo-col-md-12 moo-col-sm-12 moo-col-xs-12" id="moo-modifiersContainer-for-'+item.uuid+'"></div>';
                html += '</div>'+
                    '</div>';

                if(!--count) {
                    jQuery("#moo-items-for-"+cat_id.toLowerCase()).html(html).promise().then(function () {
                        moo_ZoomOnImages();
                    });
                    swal.close();
                }
            }
        }
        else
        {
            swal.close();
            var html     = 'You don\'t have any item in this category';
            jQuery("#moo-items-for-"+cat_id.toLowerCase()).html(html);
        }
    });
}
function mooOpenQtyWindow(event,item_id,stockCount,callback)
{
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
function mooOpenCustomQtyWindow(event,item_id,callback)
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
                } else {
                    reject('You need to write a number')
                }
            })
        }
    }).then(function () {},function () {})
}
//Click on order button for items without modifiers
function moo_clickOnOrderBtn(event,item_id,qty)
{
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
function moo_clickOnOrderBtnFIWM(event,item_id,qty)
{
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


/* Cart functions */
function mooShowCart(event)
{
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
                    cart_html+=  '<i style="cursor: pointer;margin-right: 10px;margin-left: 10px" class="fa fa-pencil-square-o" aria-hidden="true" onclick="mooUpdateSpecialInsinCart(\''+line_id+'\',\''+line.special_ins+'\')"></i>'+
                        '<i style="cursor: pointer;margin-right: 10px;margin-left: 10px" class="fa fa-trash" aria-hidden="true" onclick="mooRemoveLineFromCart(\''+line_id+'\')"></i></div></div>';
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
function mooRemoveLineFromCart(line_id)
{
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
function mooUpdateSpecialInsinCart(line_id,current_special_ins)
{
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

function moo_ZoomOnImages()
{
    // Image popups
    jQuery('.moo-image-zoom').magnificPopup({
        delegate: 'a',
        type: 'image',
        removalDelay: 500, //delay removal by X to allow out-animation
        callbacks: {
            beforeOpen: function() {
                // just a hack that adds mfp-anim class to markup
                this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
                this.st.mainClass = this.st.el.attr('data-effect');
            }
        },
        closeOnContentClick: true,
        midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
    });

}
function formatPrice (p) {
    return p.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}