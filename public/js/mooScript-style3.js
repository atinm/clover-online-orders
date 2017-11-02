/**
 * Created by Mohammed El banyaoui on 7/31/2017.
 */

jQuery(document).ready(function() {
    moo_displayLoading();
    mooGetCategories();
    mooUpdateCart();

});




//get all the categories of the store
function mooGetCategories()
{
    jQuery.get(moo_RestUrl+"moo-clover/v1/categories", function (data) {
        if(data!=null && data.length>0)
            moo_renderCategories(data);
        else
        {
            var element = document.getElementById("moo-onlineStore-categories");
            var html     = '<div class="moo-panel-group">You don\'t have any category please import your inventory';
            html    += "</div>";
            jQuery(element).html(html);
        }
    });
}
//get all the items of one category
function mooGetItems($cat_id)
{

}
//get the details of an item
function mooGetItem($item_id)
{

}
//Render all categories to html element and insert it into the page
function moo_renderCategories($cats)
{
    var element = document.getElementById("moo-onlineStore-categories");
    var html     = '<div class="moo-panel-group">';
    for(i in $cats){
        var category = $cats[i];
        html += moo_buildOneCategoryHtmlLine(category.name,category.uuid);
    }
    html    += "</div>";
    jQuery(element).html(html);
}

//Render items of the selected category to html element and insert it into the page
function moo_renderItems($items,$cat_id)
{
    var element = document.getElementById("moo_items_for_"+$cat_id);
    var html    = '<div class="moo-panel-group moo-items-panel">';
    for(i in $items){
        var item = $items[i];
        html += moo_buildOneItemHtmlLine(item);
    }
    html    += "</div>";
    jQuery(element).html(html);
    moo_ZoomOnImages();
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
// Show a loading window
function moo_displayLoading()
{
    jQuery("#moo-onlineStore-categories").html("Loading, please wait..")
}
// Fire when clicking on a category
function moo_ClickOnCategory(cat_id)
{
    if(jQuery("#moo_items_for_"+cat_id).is(':visible'))
    {
        jQuery("#moo_items_for_"+cat_id).slideUp();
        jQuery("#moo_category_"+cat_id).removeClass("moo-NoBgColor");
        jQuery("#moo_category_"+cat_id+"  .fa-minus").hide();
        jQuery("#moo_category_"+cat_id+"  .fa-plus").show();

    }
    else
    {
        //reset all categories
        jQuery("div[id^='moo_items_for_']").slideUp();
        jQuery("div[id^='moo_category_']").removeClass("moo-NoBgColor");
        jQuery("div[id^='moo_category_']  .fa-minus").hide();
        jQuery("div[id^='moo_category_']  .fa-plus").show();

        // Show the selected category
        jQuery("#moo_items_for_"+cat_id).slideDown();
        jQuery("#moo_category_"+cat_id).addClass("moo-NoBgColor");
        jQuery("#moo_category_"+cat_id+"  .fa-minus").show();
        jQuery("#moo_category_"+cat_id+"  .fa-plus").hide();

        //Scroll to the selected category
        //document.getElementById('moo_category_'+cat_id).scrollIntoView();

        var content = jQuery(document.getElementById("moo_items_for_"+cat_id)).html();

        if(content=="Loading Items")
            //get Items for this category
            jQuery.get(moo_RestUrl+"moo-clover/v1/categories/"+cat_id+"/items", function (data) {
                if(data.items != null)
                    moo_renderItems(data.items,cat_id);
                else
                    //TODO: Error message
                    console.log("Error :  No item found");
            });

    }
    /* End Showing items for selected category*/
}
// Fire when clicking on an item
function moo_ClickOnItem(item_id)
{
    console.log(item_id);

    if(jQuery("#moo_itemDetails_for_"+item_id).is(':visible'))
    {
        jQuery("#moo_itemDetails_for_"+item_id).slideUp();
        jQuery("#moo_item_"+item_id).removeClass("moo-NoBgColor");
    }
    else
    {
        jQuery("#moo_itemDetails_for_"+item_id).slideDown();
        jQuery("#moo_item_"+item_id).addClass("moo-NoBgColor");
    }
}
//Click on order button for items without modifiers
function moo_clickOnOrderBtn(event,item_id)
{
    event.preventDefault();
    var qty = parseInt(jQuery("#moo-itemQty-for-"+item_id).val());

    var body = {
        item_uuid:item_id,
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
                timer:3000,
                type:"success"
            });
            mooUpdateCart();
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
//Click on order button for an item with modifiers
function moo_clickOnOrderBtnFIWM(event,item_id)
{
    event.preventDefault();
    //Change button content to loading
    var target = event.target;
    jQuery(target).text(jQuery(target).attr("data-loading-text"));

    var qty = parseInt(jQuery("#moo-itemQty-for-"+item_id).val());


    jQuery.get(moo_RestUrl+"moo-clover/v1/items/"+item_id, function (data) {
        //Change butn text
        jQuery(target).text("Add to cart");

        if(data != null)
        {
            if(data.modifier_groups.length > 0)
            {
                if(typeof mooBuildModifiersPanel == "function")
                {
                    mooBuildModifiersPanel(data.modifier_groups,item_id,qty);
                    swal.close();
                }
                else
                {
                    swal('Try again','Please refresh the page, An error has occurred','error');
                }

            }
            else
                moo_clickOnOrderBtn(event,item_id);
        }
        else
        {
            //Change butn text
            jQuery(target).text("Add to cart");
            swal({ title: "Error", text: 'We cannot Load the options for this item, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
        }
    }).fail(function (data) {
        //Change butn text
        jQuery(target).text("Add to cart");
        swal({ title: "Error", text: 'We cannot Load the options for this item, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
    });

}
// Internal function used to build the html line for one category
function moo_buildOneCategoryHtmlLine(cat_name,cat_id)
{
    var html = '';
    html +='<div class="moo-panel moo-panel-default">';
    html +='<div class="moo-panel-heading" id="moo_category_'+cat_id+'" onclick="moo_ClickOnCategory(\''+cat_id+'\')">';
    html +='<div class="moo-row">';
    html +='<div class="moo-panel-title moo-col-md-9 moo-col-sm-9 moo-col-xs-8">'+cat_name+'</div>';
    html +='<div class="moo-col-md-3 moo-col-sm-3 moo-col-xs-4 moo-text-right">';
    html +='<i class="fa fa-minus"></i><i class="fa fa-plus"></i></div></div></div>';
    html +='<div class="moo-panel-body moo-collapse" id="moo_items_for_'+cat_id+'">';
    html +='Loading Items';
    html +='</div></div>';
    return html;
}
// Internal function used to build the html line for one item
function moo_buildOneItemHtmlLine(item)
{
    var html = '';
    html +='<div class="moo-panel moo-panel-default">';
    html +='<div class="moo-panel-heading-for-items" id="moo_item_'+item.uuid+'" onclick="moo_ClickOnItem(\''+item.uuid+'\')">';
    html +='<div class="moo-row">';
    html +='<div class="moo-panel-title moo-col-md-9 moo-col-sm-9 moo-col-xs-8 mooItem">'+item.name+'</div>';
    html +='<div class="moo-col-md-3 moo-col-sm-3 moo-col-xs-4 moo-text-right mooItemPrice">';

    if(item.price>0)
    {
        var price = parseFloat(item.price/100);
        if(item.price_type == "PER_UNIT")
            html +='$'+price.toFixed(2)+"/"+item.unit_name;
        else
            html +='$'+price.toFixed(2);
    }

    html +='</div></div></div>';
    html +='<div class="moo-panel-body moo-collapse" id="moo_itemDetails_for_'+item.uuid+'">';

    if(item.image!=null && item.description !="")
        html += mooBuildItemLine(item);
    else
        if(item.image!=null && item.description =="")
            html += mooBuildItemLineWithoutDescription(item);
        else
            if(item.image==null && item.description != "")
                html += mooBuildItemLineWithoutImage(item);
            else
                if(item.image==null && item.description == "")
                    html += mooBuildItemLineWithoutImageAndDescription(item);

    html +='</div></div>';
    return html;
}
// Build the html line for the item line when the item has a description and image
function mooBuildItemLine(item)
{
    var html = '';
    html +='   <div class="moo-row mooItemContent">';
    html +='       <div class="moo-col-lg-9 moo-col-md-9 moo-col-sm-12 col-xs-12">';
    html +='            <div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-12 moo-col-xs-12 moo-image-zoom">';
    html +='                <a href="'+item.image.url+'" data-effect="mfp-zoom-in"><img src="'+item.image.url+'" class="moo-img-responsive moo-image-zoom"></a>';
    html +='            </div><!-- /.col-lg-4 col-md-4 col-sm-4 col-xs-8 -->';
    html +='            <div class="moo-col-lg-8 moo-col-md-8 moo-col-sm-12 moo-col-xs-12">';
    html +='                <p>'+item.description+'</p>';
    html +='            </div><!-- /.col-lg-8 col-md-8 col-sm-8 col-xs-12 -->';
    html +='        </div><!-- /.col-lg-10 col-md-10 col-sm-12 col-xs-12 -->';
    html +='        <div class="moo-col-lg-3 moo-col-md-3 moo-col-sm-12 moo-col-xs-12">';

    if(item.stockCount == "out_of_stock")
        html +="OUT OF STOCK";
    else
    {
        html += mooBuildItemQty(item.stockCount,item.uuid);

        if(item.has_modifiers)
            html +='<a href="#" class="moo-btn moo-btn-default moo-btn-block moo-btn-block-margin" onclick="moo_clickOnOrderBtnFIWM(event,\''+item.uuid+'\')" data-loading-text="Loading options...">Add to cart</a>';
        else
            html +='<a href="#" class="moo-btn moo-btn-default moo-btn-block moo-btn-block-margin" onclick="moo_clickOnOrderBtn(event,\''+item.uuid+'\')" data-loading-text="Adding...">Add to cart</a>';
    }
    html +='      </div><!-- /.col-lg-2 col-md-2 col-sm-3 col-xs-4 -->';
    html +='   </div><!-- /.mooItemContent -->';
    return html;

}
// Build the html line for the item line when the item has only a description
function mooBuildItemLineWithoutImage(item)
{
    var html = '';
    html +='   <div class="moo-row mooItemContent">';
    html +='       <div class="moo-col-lg-9 moo-col-md-9 moo-col-sm-12 col-xs-12">';
    html +='            <p>'+item.description+'</p>';
    html +='        </div><!-- /.col-lg-10 col-md-10 col-sm-12 col-xs-12 -->';
    html +='        <div class="moo-col-lg-3 moo-col-md-3 moo-col-sm-12 moo-col-xs-12">';

    if(item.stockCount == "out_of_stock")
        html +="OUT OF STOCK";
    else
    {
        html += mooBuildItemQty(item.stockCount,item.uuid);
        if(item.has_modifiers)
            html +='<a href="#" class="moo-btn moo-btn-default moo-btn-block moo-btn-block-margin" onclick="moo_clickOnOrderBtnFIWM(event,\''+item.uuid+'\')" data-loading-text="Loading options...">Add to cart</a>';
        else
            html +='<a href="#" class="moo-btn moo-btn-default moo-btn-block moo-btn-block-margin" onclick="moo_clickOnOrderBtn(event,\''+item.uuid+'\')" data-loading-text="Adding...">Add to cart</a>';
    }
    html +='      </div><!-- /.col-lg-2 col-md-2 col-sm-3 col-xs-4 -->';
    html +='   </div><!-- /.mooItemContent -->';
    return html;
}
// Build the html line for the item line when the item hasn't a description
function mooBuildItemLineWithoutDescription(item)
{
    var html = '';
    html +='   <div class="moo-row mooItemContent">'+
    '       <div class="moo-col-lg-8 moo-col-md-8 moo-col-sm-12 col-xs-12">'+
    '            <div class="moo-col-lg-5 moo-col-md-5 moo-col-sm-12 moo-col-xs-12 moo-image-zoom">'+
    '                 <a href="'+item.image.url+'" data-effect="mfp-zoom-in"><img src="'+item.image.url+'" class="moo-img-responsive moo-image-zoom"></a>'+
    '            </div><!-- /.col-lg-4 col-md-4 col-sm-4 col-xs-8 -->'+
    '            <div class="moo-col-lg-7 moo-col-md-7 moo-col-sm-12 moo-col-xs-12 moo-marginTop-mobile">'+
    '                <p>'+mooBuildItemQty(item.stockCount,item.uuid)+'</p>'+
    '            </div><!-- /.col-lg-8 col-md-8 col-sm-8 col-xs-12 -->'+
    '        </div><!-- /.col-lg-10 col-md-10 col-sm-12 col-xs-12 -->'+
    '        <div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-12 moo-col-xs-12">';

    if(item.stockCount == "out_of_stock")
        html +="OUT OF STOCK";
    else
    {
        if(item.has_modifiers)
            html +='<a href="#" class="moo-btn moo-btn-default moo-btn-block moo-marginTop-mobile" onclick="moo_clickOnOrderBtnFIWM(event,\''+item.uuid+'\')" data-loading-text="Loading options...">Add to cart</a>';
        else
            html +='<a href="#" class="moo-btn moo-btn-default moo-btn-block moo-marginTop-mobile" onclick="moo_clickOnOrderBtn(event,\''+item.uuid+'\')" data-loading-text="Adding...">Add to cart</a>';
    }

    html +='      </div><!-- /.col-lg-2 col-md-2 col-sm-3 col-xs-4 -->';
    html +='   </div><!-- /.mooItemContent -->';
    return html;
}
// Build the html line for the item line when the item hasn't any of description or image
function mooBuildItemLineWithoutImageAndDescription(item)
{
    var html = '';
    html ='   <div class="moo-row mooItemContent">'+
          '       <div class="moo-col-lg-8 moo-col-md-8 moo-col-sm-12 col-xs-12 moo-col-lg-offset-2 moo-col-md-offset-2">'+
          '            <div class="moo-col-lg-6 moo-col-md-6 moo-col-sm-12 moo-col-xs-12">'+
          '                <p>'+mooBuildItemQty(item.stockCount,item.uuid)+'</p>'+
          '            </div><!-- /.col-lg-4 col-md-4 col-sm-4 col-xs-8 -->'+
          '            <div class="moo-col-lg-6 moo-col-md-6 moo-col-sm-12 moo-col-xs-12">';

    if(item.stockCount == "out_of_stock")
        html +="OUT OF STOCK";
    else
    {
        if(item.has_modifiers)
            html +='<a href="#" class="moo-btn moo-btn-default moo-btn-block moo-marginTop-mobile" onclick="moo_clickOnOrderBtnFIWM(event,\''+item.uuid+'\')" data-loading-text="Loading options...">Add to cart</a>';
        else
            html +='<a href="#" class="moo-btn moo-btn-default moo-btn-block moo-marginTop-mobile" onclick="moo_clickOnOrderBtn(event,\''+item.uuid+'\')" data-loading-text="Adding...">Add to cart</a>';
    }
    html +='            </div><!-- /.col-lg-8 col-md-8 col-sm-8 col-xs-12 -->'+
           '        </div><!-- /.col-lg-10 col-md-10 col-sm-12 col-xs-12 -->'+
           '   </div><!-- /.mooItemContent -->';
    return html;
}
// Build the Quantity Html DIV
function mooBuildItemQty(stockCount,item_uuid)
{
    var html = '';
    if(stockCount == "not_tracking_stock" ||  stockCount == "tracking_stock" )
    {
        var QtyMax = 10;
    }
    else
    {
        var QtyMax = parseInt(stockCount);
    }

    html +='        <select id="moo-itemQty-for-'+item_uuid+'" class="moo-form-control">';
    for(var i=1;i<=QtyMax;i++)
        html +='        <option value="'+i+'">'+i+'</option>';
    html +='        </select>';
    return html;
}

/* Cart functions */
function mooUpdateCart()
{
    var element = jQuery("#moo-onlineStore-cart");
    var cart_element =jQuery("#moo-onlineStore-cart>.moo-cart-container") ;

    //Display loading icon
    //TODO::Loading icon
    cart_element.append('<div id="moo-overlay"><img src="'+moo_params['plugin_img']+'/eclipse.gif" id="moo-img-load" width="50"/></div>');

    var cart_html = '<div class="moo-row moo-cart-heading">'+
        '<div class="moo-col-lg-6 moo-col-md-6 moo-col-sm-6 moo-col-xs-6 moo-cart-line-itemName">ITEM</div>'+
    '<div class="moo-col-lg-2 moo-col-md-2 moo-col-sm-2 moo-col-xs-2 moo-cart-line-itemQty">QTY</div>'+
    '<div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-4 moo-col-xs-4 moo-cart-line-itemPrice">SUB-TOTAL</div>'+
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

                  cart_html+='<div class="moo-row moo-cart-line" onmouseenter="mooMouseEnterToCartLine(this)" onmouseleave="mooMouseLeaveToCartLine(this)">'+
                      '<div class="moo-col-lg-6 moo-col-md-6 moo-col-sm-6 moo-col-xs-6 moo-cart-line-itemName">';
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
                  cart_html+= '<div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-4 moo-col-xs-4  moo-cart-line-itemPrice">$'+line_price.toFixed(2)+'</div>';
                  cart_html+=  '<div class="moo-cart-line-EditPanel"><div><i class="fa fa-pencil-square-o" aria-hidden="true" onclick="mooUpdateSpecialInsinCart(\''+line_id+'\',\''+line.special_ins+'\')"></i></div>'+
                      '<div><i class="fa fa-trash" aria-hidden="true" onclick="mooRemoveLineFromCart(\''+line_id+'\')"></i></div></div></div>';
              });
              cart_html += '</div>';
              //Set teh cart total
              if(data.total != null && data.total != false)
                  cart_html +=' <div class="moo-row moo-cart-totals">'+
                      '<div class="moo-row moo-cart-total moo-cart-total-subtotal">'+
                      '<div class="moo-col-lg-9 moo-col-md-9 moo-col-sm-7 moo-col-xs-7 moo-cart-total-label">SUBTOTAL</div>'+
                      '<div class="moo-col-lg-3 moo-col-md-3 moo-col-sm-5 moo-col-xs-5  moo-cart-total-price">$'+data.total.sub_total+'</div>'+
                      '</div>'+
                      '<div class="moo-row moo-cart-total moo-cart-total-tax">'+
                      '<div class="moo-col-lg-9 moo-col-md-9 moo-col-sm-7 moo-col-xs-7 moo-cart-total-label">TAX</div>'+
                      '<div class="moo-col-lg-3 moo-col-md-3 moo-col-sm-5 moo-col-xs-5  moo-cart-total-price">$'+data.total.total_of_taxes+'</div>'+
                      '</div>'+
                      '<div class="moo-row moo-cart-total moo-cart-total-grandtotal">'+
                      '<div class="moo-col-lg-8 moo-col-md-8 moo-col-sm-6 moo-col-xs-6 moo-cart-total-label">TOTAL</div>'+
                      '<div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-66 moo-col-xs-6 moo-cart-total-price">$'+data.total.total+'</div>'+
                      '</div>'+
                      '</div>';
              //Set checkout btn
              cart_html +='<div class="moo-row moo-cart-btns">'+
                  '<a href="'+moo_CheckoutPage+'" class="moo-btn moo-btn-danger BtnCheckout">CHECKOUT</a>'+
                  '</div></div></div>';
              element.html(cart_html);
          }
          else
          {

              cart_html +='<div class="moo-cart-empty">Your cart is empty</div> '+
                  '<div class="moo-cart-btns">'+
                  '<a href="#" class="moo-btn moo-btn-danger BtnCheckout">CHECKOUT</a>'+
                  '</div></div>';
              cart_html += '</div></div>';
              element.html(cart_html);
          }
      }
      else
      {

          cart_html += '<div class="moo-row moo-cart-content">';
          cart_html +='<div class="moo-cart-empty">Your cart is empty</div> '+
              '<div class="moo-cart-btns">'+
              '<a href="" class="moo-btn moo-btn-danger BtnCheckout">CHECKOUT</a>'+
              '</div></div>';
          cart_html += '</div></div>';
          element.html(cart_html);

      }
    }).fail(function(data){
        console.log('Fail to get the cart');
        cart_html +='<div class="moo-cart-empty">Error in loading your cart, please refresh the page</div> '+
            '<div class="moo-cart-btns">'+
            '<a href="" class="moo-btn moo-btn-danger BtnCheckout">CHECKOUT</a>'+
            '</div></div>';
        element.html(cart_html);
    });
}

function mooMouseEnterToCartLine(elem) {
    jQuery(".moo-cart-line-EditPanel",elem).show();
}
function mooMouseLeaveToCartLine(elem) {
    jQuery(".moo-cart-line-EditPanel",elem).hide();
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
                        mooUpdateCart();
                        resolve(true);
                    }
                    else
                    {
                        resolve(false);
                    }
                }).fail(function ( data ) {
                    resolve(false);
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
            console.log(special_ins);
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
                    resolve(true)
                }
            })
        },
        allowOutsideClick: false
    }).then(function (data) {
        if(data)
            swal({
                type: 'success',
                title: 'Done',
                html: 'Special instructions submitted'
            })
        else
            swal({
                type: 'error',
                title: 'Not added',
                html: 'Special instructions not submitted try again'
            })
    });

}