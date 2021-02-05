/*
* Search feature started by Mohammed El BANYAOUI, 12/16/2017
* Fully based on AJAX and
 */

jQuery(document).ready(function() {
    window.moo_RestUrl = moo_params.moo_RestUrl;
    window.moo_theme_setings = [];
    window.moo_mg_setings = {};
    window.moo_nbItemsPerLine = 4;

    /* Load the them settings then draw tha layout an get the categories with the first five items */
    jQuery.get(moo_RestUrl+"moo-clover/v1/theme_settings/default", function (data) {
        if(data !== null && data.settings !== null)
        {
            window.moo_theme_setings = data.settings;
            window.nb_items_in_cart  = data.nb_items;
        }
    });

    /* Load the modifiers settings and save them on a window's variable */
    jQuery.get(moo_RestUrl+"moo-clover/v1/mg_settings", function (data) {
        if(data !== null && data.settings !== null)
        {
            window.moo_mg_setings = data.settings;
        }
    });

});

function mooClickonSearchButton(e) {
    e.preventDefault();
    var keyword = jQuery(".moo-search-field").val();
    if(keyword.length>0){
        mooShowLoadingForSearch();
        jQuery.get(moo_RestUrl+"moo-clover/v1/search/"+keyword, function (data) {
            if(data.items !== null) {
                mooRenderItemsForSearch(data.items);
            } else {
                jQuery(".moo-search-result").html("Sorry, we don't find any item");
            }

        }).fail(function () {
            jQuery(".moo-search-result").html("Sorry, we don't find any item");
        });
    }

}

function  mooShowSearchResultContainer() {
    jQuery(".moo-search-result").slideDown();
}
function  mooHideSearchResultContainer() {
    jQuery(".moo-search-result").slideUp();

}
function mooShowLoadingForSearch() {
    jQuery(".moo-search-result").html("<div id='MooLoading'></div>");
    mooShowSearchResultContainer();
}
function moohideLoadingForSearch() {
    mooHideSearchResultContainer();
    jQuery(".moo-search-result").html("");
}
function mooRenderItemsForSearch(items){
    var html = '<div class="moo-row">';
    for(var i in items){
        var item = items[i];
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
        html +='<a title="'+item.description+'" class="link" href="#item-'+item.uuid.toLowerCase()+'" data-item-id="'+item.uuid.toLowerCase()+'" onclick="MooClickOnItem(event,this)">';

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
    html    += "</div>";
    html    += '<div class="moo-back-to-categories"><button class="osh-btn" onclick="mooHideSearchResultContainer()">' +
        '<span class="label">Close Search</span>'+
        '</button></div>';

    // Then add theme to dom and do some changes after finsihed the rendriign
    jQuery(".moo-search-result").html(html).promise().done(function() {
        console.log("Search done")
    });
}


function MooClickOnItem(event,elm) {
    event.preventDefault();
    var item_id = jQuery(elm).attr('data-item-id');
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