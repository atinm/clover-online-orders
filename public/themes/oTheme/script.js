window.modifiers_settings;
window.modifiersHasImage = false;
window.topPosition = 0;
window.topPositionMobile = 0;
window.showMore;
window.showCategoryIcon;
window.showSearch = false;
window.showTopPicks = false;
jQuery(document).ready(function () {
    
    jQuery.get(moo_RestUrl+"moo-clover/v1/theme_settings/oTheme", function (data) {

        if(data.settings.oTheme_show_more_button === 'on') window.showMore = true; else window.showMore = false;
        if(data.settings.oTheme_show_category_icon === 'on') window.showCategoryIcon = true; else window.showCategoryIcon = false;
        if(data.settings.oTheme_showSearchSection === 'on') window.showSearch = true; else window.showSearch = false;
        if(data.settings.oTheme_showTopPicksSection === 'on') window.showTopPicks = true; else window.showTopPicks = false;
        if(data.settings.oTheme_categoriesMenuTopMargin)window.topPosition = parseInt(data.settings.oTheme_categoriesMenuTopMargin);
        if(data.settings.oTheme_categoriesMenuTopMarginMobile)window.topPositionMobile = parseInt(data.settings.oTheme_categoriesMenuTopMarginMobile);
        
    }).done(function(){
        jQuery.get(moo_RestUrl+"moo-clover/v1/mg_settings", function (data) {
            if(data != null && data.settings != null)
            {
                window.modifiers_settings = data.settings; 
            }
        }).done(function(){
            osnOnlineStoreBase();

        });
    });
});

//scrolling

jQuery(window).resize(function() {
    osnMenuNavFix();
    osnSelectCategorieInMenuOnScrolling();
  });
var isScrolling;
window.onscroll = function() {
    osnMenuNavFix();
    // Clear our timeout throughout the scroll
     window.clearTimeout( isScrolling );
     isScrolling = setTimeout(function() {
        osnSelectCategorieInMenuOnScrolling();
        // Run the callback
    }, 66);
}
// closing modals window click
jQuery(window).click(function(e) {
    if(e.target.id === 'osnItemModalModifier' || e.target.id === 'osnCartModal' || e.target.id === 'osnItemModal'){
    jQuery('#'+e.target.id).remove();
    jQuery('body').removeClass('blurBackgroundWhenModalIsopen');
    jQuery('.btn-cart').removeClass('blurBackgroundWhenModalIsopen');
}
});


function osnScrollToCat(e) {
    var page = jQuery(e).data("id");
    var speed = 750;
    let topPosition = 0;
    if(jQuery(window).width()>768) topPosition = window.topPosition;else topPosition = window.topPositionMobile;
    if(jQuery('#osnMenu').css('height'))topPosition += parseInt(jQuery('#osnMenu').css("height"));
    jQuery("html, body").animate({
        scrollTop: jQuery(page).offset().top-topPosition+80
    }, speed);
    
}
function osnMenuCategorieScroll(element,op){
    a = setInterval(function() {
        return a+1;
    }, 6);
    if(op)
        document.getElementById('osnMenu').scrollBy({ 
            left: a, 
            behavior: 'smooth' 
          });
    else
    document.getElementById('osnMenu').scrollBy({ 
        left: -a, 
        behavior: 'smooth' 
      });
}
var scrollHandle = 0;
function osnOnMouseEnter(e,menu) {
  var data = jQuery(e).data("scrollModifier"), direction = parseInt(data, 10);
  
  startScrolling(direction,jQuery('#'+menu))
}
function osnOnMouseLeave() {
  stopScrolling();
};
function startScrolling(modifier,el) {
    el.css('scroll-behavior','unset');
  if (scrollHandle === 0) {
      scrollHandle = setInterval(function() {
          var newOffset = el.scrollLeft() + 3 * modifier;
          el.scrollLeft(newOffset)
      }, 10)
  }
}
function stopScrolling() {
    jQuery('.osnMenu').css('scroll-behavior','smooth');
  clearInterval(scrollHandle);
  scrollHandle = 0
}
function osnMenuNavFix() {
    
    menu = jQuery('#osnCategorieNavigation');
    sticky = jQuery('#moo_OnlineStoreContainer').offset().top;
    if (window.pageYOffset >= sticky) {
        if (menu){
            menu.addClass("sticky");
            jQuery('#osnSlideIndicator').addClass('active');
            let topPosition = 0;
            if(jQuery(window).width()>768) topPosition = window.topPosition;else topPosition = window.topPositionMobile;
            if(jQuery('#wpadminbar').css("height") &&  jQuery(window).width()>600)topPosition += parseInt(jQuery('#wpadminbar').css("height"));
            menu.css('top',topPosition);
            
        }
    } else {
        if (menu){
            menu.removeClass("sticky");
            jQuery('#osnSlideIndicator').removeClass('active');
            menu.css('top',0);
        }
    }
}
function osnSelectCategorieInMenuOnScrolling() {
    var currentTop = jQuery(window).scrollTop();
    var elems = jQuery('.scrollspy');
    let topPosition = 0;
    let aButton;
    osnMenu = jQuery('#osnMenu');
    if( jQuery(window).width()>768 ) {
        topPosition = window.topPosition;
    } else { 
        topPosition = window.topPositionMobile;
    }
    if( osnMenu.css('height') ) {
        topPosition += parseInt(osnMenu.css("height"));
        }
    
    elems.each(function(index){
      var elemTop 	= jQuery(this).offset().top-topPosition;
      var elemBottom 	= elemTop + jQuery(this).height();
      if((currentTop >= elemTop) &&(currentTop <= elemBottom)){
    aButton = jQuery('a[data-id="#'+elems[index].id+'"]');
    if(aButton.offset().left+parseInt(aButton.css("width")) > osnMenu.width()){
        scrolling = (aButton.offset().left+parseInt(aButton.css("width"))-osnMenu.width());
        scrolling += 150; 
        if(!aButton.hasClass('active')){
            newOffset = osnMenu.scrollLeft() + scrolling;
            osnMenu.scrollLeft( newOffset );
    }
    }else if(aButton.offset().left < osnMenu.offset().left){
        scrolling = (osnMenu.offset().left-aButton.offset().left);
        scrolling += 150;
        if( !aButton.hasClass('active') ) {
            newOffset = osnMenu.scrollLeft() - scrolling;
            osnMenu.scrollLeft( newOffset );
    }
    }
    jQuery('ul.osnMenuListCat').find('.active').removeClass('active');
    aButton.addClass('active');
      }
      if(aButton){

      }else{
        var elemTop 	= jQuery('#osnPanelDisplayShow').offset().top-topPosition;
        var elemBottom 	= elemTop + jQuery('#osnPanelDisplayShow').height();

        if ( currentTop < elemTop ) {
            jQuery('ul.osnMenuListCat').find('.active').removeClass('active');
            jQuery('a[data-id="#'+elems[0].id+'"]').addClass('active');
            if(jQuery('a[data-id="#'+elems[0].id+'"]').offset().left < osnMenu.offset().left){
                scrolling = (osnMenu.offset().left-jQuery('a[data-id="#'+elems[0].id+'"]').offset().left);
                newOffset = osnMenu.scrollLeft() - scrolling;
                osnMenu.scrollLeft( newOffset );
            }
        } else if ( currentTop > elemBottom ) {
            let buttona = jQuery('a[data-id="#'+elems[elems.length - 1].id+'"]');
            jQuery('ul.osnMenuListCat').find('.active').removeClass('active');
            buttona.addClass('active');
            if( buttona.offset().left + parseInt(buttona.css("width")) > osnMenu.width()){
                scrolling = (buttona.offset().left+parseInt(buttona.css("width"))-osnMenu.width());
                scrolling += 300;
                newOffset = osnMenu.scrollLeft() + scrolling;
                osnMenu.scrollLeft( newOffset );
            }
        }
      }
      
    })
}; 


osnItemQty = 1;
function osnLaunchLoading() {
    loading='<div id="osnLoading" class="osnLoading"><i></i><div>';
    jQuery('#osnPanelDisplayShow').append(loading);
    // jQuery('body').addClass('blurBackgroundWhenModalIsopen');
    jQuery('#osnCategorieNavigation').css('visibility','hidden');
    jQuery('.btn-cart').css('visibility','hidden');
}
function osnStopLaoding() { 
    jQuery('#osnLoading').remove();
    // jQuery('body').removeClass('blurBackgroundWhenModalIsopen');
    jQuery('#osnCategorieNavigation').css('visibility','visible');
    jQuery('.btn-cart').css('visibility','visible');
 }

function osnOnlineStoreBase() {
    cart = '<div class="btn-cart" onclick="osnShowHideCart()">';
    cart += '<i class="icon-cart"></i>';
    cart += '<div id="osnCartCountItems"><span>0</span></div>';
    cart += '</div>';
    jQuery('body').after(cart);
    content = '<div id="osnPanelDisplayShow" class="osnPanelDisplay">';
    content += '<div id="osnCategorieNavigation" class="osnCategorieNavigation"></div>';
    content += '<div id="osnShowItemsGroupByCategories"></div>';
    content += '</div>';
    jQuery('#moo_OnlineStoreContainer').append(content).promise().done(function(){
        osnLaunchLoading();
        osnLoadCatItemData();
        
    });
    
    
}
function osnSearchLoading() { 
    jQuery('#osnItemsSearchResult').html('<div class="osnLoading"><i></i></div>');
 }

function osnSearchCheck(e){
    keyword = jQuery('#osnSearch').val();
    if ((e.type === "click" || e.keyCode === 13) && keyword.trim() != "") {
        e.preventDefault();
        osnLoadCatItemDataSearch(keyword.trim());
      }else if((e.type === "click" || e.keyCode === 13) && keyword === ""){
        jQuery('#osnItemsSearchResult').html('');
      }
    
}
function osnLoadCatItemDataSearch(keyword) {
    contentItems = '';
    osnSearchLoading();
    jQuery.get(moo_RestUrl+"moo-clover/v1/search/"+keyword,
        function (data) {
                if(data.items.length != 0){
                    data.items.forEach(function (item){
                        console.log(item);
                        contentItems += '<div class="osnCardItem">'
                        +'<div class="osnImgItem" ';
                        if(data.available && (item.stockCount != "out_of_stock") )
                            contentItems += 'onclick="osnItemDescriptionModal(\''+item.uuid+'\')"';
                        contentItems += ' style="'+((item.image)?'background-image:url('+item.image.url+')':'')+'">';
                        contentItems +='</div>'
                        +'<div class="osnContentItem">'
                        +' <h4>'+item.name+'</h4>'
                        +'<div class="price-product"><p>$'+parseFloat(item.price/100).toFixed(2)+'</p><div class="price-menu '+((item.stockCount == "out_of_stock")?'unavailable':'')+'" '+((item.stockCount != "out_of_stock" )?'onclick="osnItemDescriptionModal(\''+item.uuid+'\')':'title="This item is out of stock"')+'"><i class="'+((item.has_modifiers)?'icon-cog':'icon-add_circle')+'"></i></div></div>'
                        +'</div>'
                        +'</div>';
                        
                    });  
            }else{
                contentItems = '<p class="nothingFound">Sorry, we didn\'t find any item</p>';
            }
        },
        "json"
    ).done(
        function() {
            jQuery('#osnItemsSearchResult').html(contentItems).promise().done(function(){});
        }
    );
}
function osnLoadCatItemData() { 
    osnUpdatePriceCart();
    contentItems = '<div id="osnGroupContainer" class="osnGroupContainer">';
    if(window.showSearch){
        contentItems += '<div>';
        contentItems += '<div class="osnCategorieTitle osnSearchSeparator">';
        contentItems += '<div><h2>Search</h2></div>'+
        '<div class="osnCategorieSeparator">'+
        '<i></i>'+
        '</div>';

        contentItems += '</div>';
        contentItems += '<div id="" class="osnCategorieSeparator osnSearchSeparator">'+
        '<input id="osnSearch" type="search" onkeyup="osnSearchCheck(event)" autocomplete="off">'+'<button onclick="osnSearchCheck(event)">search</button>'
        '</div>';
        contentItems += '</div>';
        contentItems += '<div id="osnItemsSearchResult" class="osnItemsContainers">';
        contentItems += '</div>';
        contentItems += '</div>';
    }
    
    content = '<div id="osnMenu" class="osnMenu">';
    content += '<ul class="osnMenuListCat">';
    if(window.showTopPicks){
        jQuery.get(moo_RestUrl+"moo-clover/v1/items/most_purchase",
            function (data) {
                if(data && data.items.length > 0){
                content += '<li class="osnMenuListCatItem"><a data-id="#topp" onclick="osnScrollToCat(this)" class="osnCatLink">'+((window.showCategoryIcon)?'<span><i class="icon-star"></i></span><span>':'<span style="margin:13px;">')+'Top picks</span></a></li>';
                contentItems += '<div id="topp" class="scrollspy">';
                contentItems += '<div class="osnCategorieTitle">';
                contentItems += '<div><h2>top picks</h2></div>'+
                '<div class="osnCategorieSeparator">'+
                '<i></i>'+
                '</div>';
                contentItems += '</div>';
                contentItems += '<div class="osnItemsContainers">';
                data.items.forEach(function (item){
                    contentItems += '<div class="osnCardItem">'
                        +'<div class="osnImgItem" ';
                        if(data.available && (item.stockCount != "out_of_stock") ) contentItems += 'onclick="osnItemDescriptionModal(\''+item.uuid+'\')"';
                        contentItems += ' style="'+((item.image)?'background-image:url('+item.image.url+')':'')+'">';
                        contentItems +='</div>'
                        +'<div class="osnContentItem">'
                        +' <h4>'+item.name+'</h4>'
                        +'<div class="price-product"><p>$'+parseFloat(item.price/100).toFixed(2)+'</p><div class="price-menu " '+((item.stockCount != "out_of_stock")?'onclick="osnItemDescriptionModal(\''+item.uuid+'\')':'title="This item is out of stock"')+'"><i class="'+((item.has_modifiers)?'icon-cog':'icon-add_circle')+'"></i></div></div>'
                        +'</div>'
                        +'</div>';
                                });
                contentItems += '</div>';
                contentItems += '</div>';
               }
            }).done(
                function () { 
                    osnRenderItems(contentItems,content);
                    
                 }
            );
    } else {
        osnRenderItems(contentItems,content);
    }
    
}

function osnRenderItems(contentItems,content){
    endPoint = moo_RestUrl+"moo-clover/v1/categories?expand=all_items";
    if(window.showMore) endPoint = moo_RestUrl+"moo-clover/v1/categories?expand=five_items";
    jQuery.get(endPoint,
        function (data) {
            if(data) {
                data.forEach(function(element) {
                    var category = element;
                    if(typeof category !== 'object')
                        return;
                    if(typeof attr_categories !== 'undefined' && attr_categories !== undefined && attr_categories !== null && typeof attr_categories === 'object') {
                        if(attr_categories.indexOf(category.uuid.toUpperCase()) === -1){
                            return;
                        }
                    }
                    if(element.items.length > 0){
                        content += '<li class="osnMenuListCatItem"><a data-id="#cat-'+element.uuid+'" onclick="osnScrollToCat(this)" class="osnCatLink">'+((window.showCategoryIcon)?'<span><i class="icon-food"></i></span><span>':'<span style="margin:13px;">')+''+element.name+'</span></a></li>';
                        contentItems += '<div id="cat-'+element.uuid+'" class="scrollspy">';
                        contentItems += '<div class="osnCategorieTitle">';
                        contentItems += '<div><h2>'+element.name+'</h2></div>'+
                            '<div class="osnCategorieSeparator">'+
                            '<i></i>'+
                            '</div>';
                        contentItems += '</div>';

                        if(element.description !== '' && element.description !== undefined) contentItems += '<div class="osnCatDescription"><p>' + element.description + '</p></div>';
                        contentItems += '<div class="osnItemsContainers">';

                        element.items.forEach(function (item){
                            contentItems += '<div class="osnCardItem">'
                                +'<div class="osnImgItem" ';
                            if(element.available && (item.stockCount != "out_of_stock") ) contentItems += 'onclick="osnItemDescriptionModal(\''+item.uuid+'\')"';
                            contentItems += ' style="'+((item.image)?'background-image:url('+item.image.url+')':'')+'">';
                            contentItems +='</div>'
                                +'<div class="osnContentItem">'
                                +' <h4>'+item.name+'</h4>'
                                +'<div class="price-product"><p>$'+parseFloat(item.price/100).toFixed(2)+'</p><div class="price-menu '+((!element.available || item.stockCount == "out_of_stock")?'unavailable':'')+'" '+((element.available && (item.stockCount != "out_of_stock") )?'onclick="osnItemDescriptionModal(\''+item.uuid+'\')':'title="This item is '+((!element.available)?'not available':'out of stock')+'"')+'"><i class="'+((item.has_modifiers)?'icon-cog':'icon-add_circle')+'"></i></div></div>'
                                +'</div>'
                                +'</div>';
                        });
                        if(window.showMore && element.items.length == 5) {
                            contentItems += '<div id="osnMoreItems'+element.uuid+'" class="osnCardItem osnMoreItems" onclick="osnGetMoreItems(\''+element.uuid+'\')" >'
                                +'<div><i class="icon-angle-right"></i></div>'
                                +'<div><h4>show more</h4></div>'
                                +'</div>';
                        }

                        contentItems += '</div>';
                        contentItems += '</div>';

                    }
                });
            }
        },
        "json"
    ).done(
        function(data) {
            content += '</ul>';
            content += '</div>'
            content += '<span id="OsnPanScrollLeft" onmouseleave="osnOnMouseLeave(this)" onmouseenter="osnOnMouseEnter(this,\'osnMenu\')" class="panner bounce-left" data-scroll-modifier="-1"><i class="icon-arrow-left-alt1"></i></span>';
            content += '<span id="OsnPanScrollRight" onmouseleave="osnOnMouseLeave(this)" onmouseenter="osnOnMouseEnter(this,\'osnMenu\')" class="panner bounce-right" data-scroll-modifier="1"><i class="icon-arrow-right-alt1"></i></span>';
            content += '<span id="osnSlideIndicator"><i class="icon-two-fingers-resize-out"></i></span>';
            
            if(data.length > 1){
                jQuery('#osnCategorieNavigation').html(content);
            }else{
                jQuery('#osnCategorieNavigation').remove();
            }
            
            contentItems += '</div>';
            jQuery('#osnShowItemsGroupByCategories').append(contentItems).promise().done(function(){
                if(window.showSearch) jQuery(jQuery('#osnShowItemsGroupByCategories').find('.osnCategorieTitle')[1]).css('padding-top',12);
                osnStopLaoding();
            });
            var hash = window.location.hash;
            if (hash != "") {
                 var top = (jQuery(hash).offset() != null)?jQuery(hash).offset().top:""; //Getting Y of target element
                 window.scrollTo(0, top);
             }
        }
    );
}
function osnLoadingMoreItem(uuid){
    jQuery('#cat-'+uuid).find('.osnMoreItems').html('<div class="modalLoading"><i></i></div>');
}
function osnGetMoreItems(categorieUuid){
    osnLoadingMoreItem(categorieUuid);
  jQuery.get(moo_RestUrl+"moo-clover/v1/categories/"+categorieUuid+"/items", function (data) {
    if(data != null && data.items != null && data.items.length > 0)
    {
        contentItems = '';
        data.items.forEach(function(item){
            contentItems += '<div class="osnCardItem">'
                        +'<div class="osnImgItem" ';
                        if(data.available && (item.stockCount != "out_of_stock") ) contentItems += 'onclick="osnItemDescriptionModal(\''+item.uuid+'\')"';
                        contentItems += ' style="'+((item.image)?'background-image:url('+item.image.url+')':'')+'">';
                        contentItems +='</div>'
                        +'<div class="osnContentItem">'
                        +' <h4>'+item.name+'</h4>'
                        +'<div class="price-product"><p>$'
                        + parseFloat(item.price/100).toFixed(2)
                        +'</p><div class="price-menu '+((!data.available || item.stockCount == "out_of_stock")?'unavailable':'')+'" '+((data.available && (item.stockCount != "out_of_stock") )?'onclick="osnItemDescriptionModal(\''+item.uuid+'\')':'title="This item is '+((!data.available)?'not available':'out of stock')+'"')+'"><i class="'+((item.has_modifiers)?'icon-cog':'icon-add_circle')+'"></i></div></div>'
                        +'</div>'
                        +'</div>';
        });
        }
    }).done(function() {
        jQuery('#cat-'+categorieUuid).find('.osnItemsContainers').html(contentItems);
    });
}
// adding selected item to cart
function osnAddItemToCart(uuid){
    osnItemDescriptionModalClose();
    jQuery(".btn-cart").removeClass('bounce');
    var body = {
        item_uuid:uuid,
        item_qty: 1,
        item_modifiers:[]
    }
    if(osnItemQty>0){
        body.item_qty = osnItemQty
    }
    osnItemQty = 1;
    let testModifiers = true;
    osn_Item_Modifers.forEach(function (group,index) {
        if(OsnQtyCheck(group.min_required,group.max_allowd,group.qty)){
            group.modifiers.forEach(function (modifier) {
                if(modifier.selected){
                    body.item_modifiers.push({uuid:modifier.uuid,qty:modifier.qty});
                }else{
                    
                }
        });
        }else{
            testModifiers = false;
            osnGetModifier(index);
        }
    });
    if(testModifiers){
        osnCloseModalModifier();
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
                jQuery(".btn-cart").addClass('bounce');
               
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
    }).done(function ( data ) {
        if(typeof data.nb_items != "undefined")
            jQuery("#osnCartCountItems").find('span').text(data.nb_items);
            GetItemsInCart();
    });
    } else {
        
    }
    
}

// cart
function osnShowHideCart() {
  
    if(jQuery('#osnCartModal').length){
        jQuery('#osnCartModal').remove();
        jQuery('body').removeClass('blurBackgroundWhenModalIsopen');
        osnCart = null;
    } else {
        osnLaunchLoading();  
      html = '<div id="osnCartModal" class="cartModal">';
      html += '<div id="osnCartModalContent" class="cartModalContent osnHide">';
      html += '<div id="osnCartHeader" class="cartHeader">';
      html += '<span>your cart</span><div><button class="cartModifierBtn" onclick="osnClickEditBtnCart()">edit</button><div class="osnCloseCart icon-close" onclick="osnShowHideCart()"></div></div>';
      html += '</div>';
      html += '<div id="osnCartItems" class="cartItems"><div class="modalLoading"><i></i></div></div>';
      html += '<div id="osnCartTotal" class="cartTotal">';
      html += '<div id="osnCartTotalSubTax"><div id="osnCartTotalPrice"><h4>Total</h4><span>$ 0</span></div></div>';
      html += '<a class="checkOutButton" href="'+moo_CheckoutPage+'">Checkout</a>';
      html += '</div>';
      html += '</div>';
      html += '</div>';
      jQuery('body').after(html).promise().done(function(){
        GetItemsInCart();
        jQuery('body').addClass('blurBackgroundWhenModalIsopen');
    });
      
    }
    }
osnCart = null;
function GetItemsInCart(){
    html='<p class="osnEmptyCart">Your cart is empty!</p>';
    jQuery.get(moo_RestUrl+"moo-clover/v1/cart", function (data) {
        osnCart = data;

          if(data != undefined && Object.keys(data.items).length > 0){
            html='';
            i=0;
            priceItem = 0;
            jQuery.each(data.items, function (index, item) {

                priceItem += parseFloat(item.item.price);

                html += '<div id="itemInCart'+index+'" class="itemInCart"  data-qty="'+item.qty+'" >';
                html += '<div><span class="title">'+item.item.name+' ($'+parseFloat(item.item.price/100).toFixed(2)+')</span>';
                if(item.modifiers.length > 0){
                    item.modifiers.forEach(function (modifier) {
                        html+='<p>'+modifier.name+' ($'+parseFloat(modifier.price/100).toFixed(2)+') x'+modifier.qty+'</p>';
                        priceItem += modifier.price * modifier.qty;
                    });
                }
                html += '<p id="osnPriceItem" data-price="'+priceItem+'">$'+(parseFloat((priceItem/100)*item.qty).toFixed(2))+'</p>';
            html += '</div>';
            html += '<div class="product-quantity-controls">';
            html +='<a href="#" data-op="decrement" onclick="osnQuantityItemCart(this,event)" data-qty="'+item.qty+'" data-item="'+index+'" class="product-quantity">-</a>';
            html +='<span class="product-quantity-wrapper"><span class="product-quantity-times">x</span>'+item.qty+'</span>';
            html +='<a href="#" onclick="osnQuantityItemCart(this,event)" data-op="increment" data-qty="'+item.qty+'" data-item="'+index+'" class="product-quantity">+</a>';
            html += '</div>';
            html += '<div class="osnCartEditButton osnCartEditBtn" onclick="osnUpdateSpecialInsinCart(\''+index+'\',\''+item.special_ins+'\')"><i class="icon-pencil"></i></div>';
            if(item.modifiers.length > 0) html += '<div class="osnCartEditButton osnCartModifiersBtn" onclick="osnUpdateModifier(\''+index+'\')"><i class="icon-cog"></i></div>';
            html += '<div class="osnCartEditButton osnCartDeleteBtn" data-item="'+index+'" onclick="osnOnDeletFromCart(this)"><i  class="icon-cancel"></i></div>';
            html += '</div>';
            priceItem = 0;
            });

          }
          html += '<div class="osnCartSubTotal"><h5>Subtotal</h5><span id="osnCartSubTotal"></span></div><div class="osnCartTax"><h5>Tax</h5><span id="osnCartTax"></span></div>';

            jQuery('#osnCartItems').html(html);
            jQuery('#osnCartTotalPrice').find('span').html("$ "+((data.total)?parseFloat(data.total.total).toFixed(2):0));
          jQuery('#osnCartSubTotal').html("$"+((data.total)?parseFloat(data.total.sub_total).toFixed(2):0));
          jQuery('#osnCartTax').html("$"+((data.total)?data.total.total_of_taxes:0));
        }).done(function (){
            osnStopLaoding();

        }).fail(function(){
            jQuery('#osnCartTotalPrice').find('span').html("$ "+((data.total)?parseFloat(data.total.total).toFixed(2):0));
            jQuery('#osnCartSubTotal').html("$"+((data.total)?parseFloat(data.total.sub_total).toFixed(2):0));
            jQuery('#osnCartTax').html("$"+((data.total)?data.total.total_of_taxes:0));
            jQuery('#osnCartItems').html(html);
        });
}
function osnUpdateModifier(index) {
    let uuids = index.split('_');
    osnItemModalModifier(uuids['0'],index);

}
function osnUpdatePriceCart(){
    jQuery.get(moo_RestUrl+"moo-clover/v1/cart", function (data) {
        osnCart = data;
        jQuery('#osnCartTotalPrice').find('span').html("$ "+((data.total)?parseFloat(data.total.total).toFixed(2):0));
        jQuery('#osnCartSubTotal').html("$"+((data.total)?parseFloat(data.total.sub_total).toFixed(2):0));
        jQuery('#total_of_taxes').html("$"+((data.total)?parseFloat(data.total.total).toFixed(2):0));
        jQuery("#osnCartCountItems").find('span').text(((data.total)?(data.total.nb_items):0));
        if(!data.total)jQuery('#osnCartItems').html('<p class="osnEmptyCart">Your cart is empty!</p>');
    });
}
function osnClickEditBtnCart(){
    osnItemsInCartEditButtons =jQuery('#osnCartItems').find('.osnCartEditButton');
    osnItemsInCart = jQuery('#osnCartItems').find('.itemInCart');
    if(osnItemsInCartEditButtons.hasClass('bounce')){
    osnItemsInCartEditButtons.removeClass('bounce');
    osnItemsInCart.removeClass('addSpacing');}
    else{
    osnItemsInCartEditButtons.addClass('bounce');
    osnItemsInCart.addClass('addSpacing');}
}
function osnOnDeletFromCart(el){
    //send delete query to server
    index = jQuery(el).data('item');
    jQuery('#itemInCart'+index).fadeOut().promise().done(
        function () {
            jQuery.post(moo_params.ajaxurl,{'action':'moo_deleteItemFromcart',"item":index}, function (data) {
                if(data.status != "success"){
                    jQuery('#itemInCart'+index).remove();
                };
            }).done(function (data){
                //update cart
                osnUpdatePriceCart();
            });
        }
    );




}
function osnQuantityItemCart(el,e){
    e.preventDefault();
var qty =   parseInt(jQuery('#itemInCart'+jQuery(el).data('item')).data('qty'));
if(jQuery(el).data('op') === 'increment'){
    qty+=1;
}else if(jQuery(el).data('op') === 'decrement'){
    if(qty>1)
        qty-=1;
    else if(qty==1){
        osnOnDeletFromCart(jQuery('#itemInCart'+jQuery(el).data('item')).find('.osnCartDeleteBtn'));
    }
}

jQuery('#itemInCart'+jQuery(el).data('item')).data('qty',qty);
price = jQuery('#itemInCart'+jQuery(el).data('item')).find('#osnPriceItem').data('price');


jQuery('#itemInCart'+jQuery(el).data('item')).find('#osnPriceItem').html('$'+parseFloat((price*qty)/100).toFixed(2));
if(qty!=0) {
    jQuery('#itemInCart'+jQuery(el).data('item')).find('.product-quantity-wrapper').html('<span class="product-quantity-times">x</span>'+qty);
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_qte','item':jQuery(el).data('item'),'qte':qty}, function (data) {
        if(data.status == "success"){
            osnUpdatePriceCart();
        }
    });
}

}
function osnUpdateSpecialInsinCart(line_id,current_special_ins) {
    html = '<div id="osnModal_special_ins">'
+'<div id="osnModal_special_ins_container">'
+'<p>Add special Instructions</p>'
+'<textarea class="special_ins-textarea" placeholder="Type your instructions here, additional charges may apply and not all changes are possible" style="display: flex;">'+current_special_ins+'</textarea>'
+'<div class="btnGroupAddCancel">'
+'<button class="osnBtnSave_special_ins" onclick="osnUpdateSpecialInsinCartSave(\''+line_id+'\')">Save</button><button onclick="osnUpdateSpecialInsinCartCancel()">Cancel</button>'
+'</div>'
+'</div>'
+'</div>';
osnShowHideCart();
jQuery('.btn-cart').addClass('blurBackgroundWhenModalIsopen');
jQuery('body').addClass('blurBackgroundWhenModalIsopen');
jQuery('body').after(html);
}
function osnUpdateSpecialInsinCartCancel(){
    jQuery('#osnModal_special_ins').remove();
    jQuery('.btn-cart').removeClass('blurBackgroundWhenModalIsopen');
    osnShowHideCart();
}
function osnUpdateSpecialInsinCartSave(line_id){
    special_ins = jQuery('#osnModal_special_ins').find('.special_ins-textarea').val();
    var body = {
        line_id: line_id,
        special_ins: special_ins
    };
    if(special_ins.length <= 255)
    jQuery.post(moo_RestUrl + "moo-clover/v1/cart/update", body, function (data) {
        if (data != null && data.status == 'success') {
            jQuery('#osnModal_special_ins').remove();
            osnShowHideCart();
            jQuery('.btn-cart').removeClass('blurBackgroundWhenModalIsopen');
        }
        else {
            alert('something went wrong! please check your internet connection or refresh the page');
            jQuery('.btn-cart').removeClass('blurBackgroundWhenModalIsopen');
        }
    }).fail(function (data) {
        alert('something went wrong! please check your internet connection or refresh the page');
        jQuery('.btn-cart').removeClass('blurBackgroundWhenModalIsopen');
    });
    else{
    alert('Text too long, You cannot add more than 250 char');
    jQuery('.btn-cart').removeClass('blurBackgroundWhenModalIsopen');
    }
}

//modifiers panel

osn_Item_Modifers = [];
osn_Item_Modifers_Pagination = 0;
function osnCloseModalModifier(){
    jQuery('#osnItemModalModifier').remove();
    jQuery('body').removeClass('blurBackgroundWhenModalIsopen');
    jQuery('.btn-cart').removeClass('blurBackgroundWhenModalIsopen');
    osn_Item_Modifers = [];
    osn_Item_Modifers_Pagination = 0;
    finalPrice = 0;
    osnItemQty = 1;
}
finalPrice = 0;

function osnItemDescriptionModalClose(){
    jQuery('#osnItemModal').remove();
    jQuery('body').removeClass('blurBackgroundWhenModalIsopen');
    jQuery('.btn-cart').removeClass('blurBackgroundWhenModalIsopen');
}
function osnItemDescriptionModal(item_uuid) {
    if(jQuery('#osnItemModal').length == 0){osnLaunchLoading();
    osnItemQty = 1;
    modal = '<div id="osnItemModal">';
    modal += '<div class="osnItemModelContainer">';
    modal += '<div class="modalLoading"><i></i></div>';
    modal += '</div>';
    modal += '</div>';
    jQuery('body').addClass('blurBackgroundWhenModalIsopen');
    jQuery('.btn-cart').addClass('blurBackgroundWhenModalIsopen');
    jQuery('body').after(modal);
    jQuery.get(moo_RestUrl + "moo-clover/v1/items/" + item_uuid, function (data) {

    }).done(function(item) {
        finalPrice = item.price;
        content = '<div class="osnItemImage" style="'+((item.images.length > 0)?'background-image:url('+item.images[0].image_url+')':'')+'">';
        content += '<span class="icon-cancel" onclick="osnItemDescriptionModalClose()"></span>';
        content += '</div>';
        content += '<div class="osnItemContent">';
        content += '<h4>'+item.name+' ($ '+parseFloat(item.price/100).toFixed(2)+')</h4>';
        content += '<hr><br>';
        if(item.description.length > 0){content += '<h6>DESCRIPTION</h6>';
        content += '<p>'+item.description+'</p>';}
        content += '</div>';
        content += '<div class="osnModifierPanelFooter">';
        content +='<div id="osnChooseQuantity">'
                +'<h5>quantity</h5>'
                + '<div class="modifier-quantity-controls">'
                + '<a data-op="decrement" onclick="osnSelectQuantity(this)" class="modifier-quantity"><i class="icon-minus"></i></a>'
                +'<span id="osnItemQuantity" class="product-quantity-wrapper"><span class="product-quantity-times">x</span>'+osnItemQty+'</span>'
                +'<a onclick="osnSelectQuantity(this)"  data-op="increment" class="modifier-quantity"><i class="icon-plus"></i></a>'
                +'</div>'
                +'</div>';

        content += '<div class="osnModifierPanelPriceDetails">';
        content += '<h5>Total</h5>';
        content += '<p>$ '+parseFloat((item.price/100)*osnItemQty).toFixed(2)+'</p>';
        content += '</div>';
        content += '<div id="osnModifierPanelPaginateBtn" class="osnModifierPanelBtn">';
        content += '<button class="osnModifierPanelBtnAddToCart" onclick="'+((item.modifier_groups.length > 0)?'osnItemModalModifier(\'' + item_uuid + '\',null)">Choose Options':'osnAddItemToCart(\'' + item_uuid + '\')">Add To Cart')+'</button>';
        content += '</div>';
        content += '</div>';
        osnStopLaoding();
        jQuery('.osnItemModelContainer').html(content);
    });}
}
function osnItemModalModifier(item_uuid,index) {
    osnLaunchLoading();
    osnItemDescriptionModalClose();
    modal = '<div id="osnItemModalModifier">';
    modal += '<div class="osnModifierPanel">';
    modal += '<div class="modalLoading"><i></i></div>';
    modal += '</div>';
    modal += '</div>';
    jQuery('body').addClass('blurBackgroundWhenModalIsopen');
    jQuery('.btn-cart').addClass('blurBackgroundWhenModalIsopen');
    jQuery('body').after(modal);
    jQuery.get(moo_RestUrl + "moo-clover/v1/items/" + item_uuid, function (data) {

        finalPrice = parseFloat(data.price);
        osn_Item_Modifers = data.modifier_groups;
        osn_Item_Modifers.sort(function (a, b) {
            return a["sort_order"] - b["sort_order"];
        });
        if(index != null){
            let uuids = index.split('_');
            osnItemQty = osnCart.items[index].qty;
            osn_Item_Modifers.forEach( group => {
                group.modifiers.forEach( modifier => {
                    if(uuids.includes(modifier.uuid)){
                        OsnSelectModifier(group.uuid,modifier.uuid);
                        let modifierQty = osnCart.items[index].modifiers.find(function  (value){ return value.uuid === modifier.uuid}).qty;
                        if(modifierQty > 1) {
                            modifier['qty'] = modifierQty;
                            if(group['qty'])
                            group['qty'] += modifierQty - 1;
                            else
                            group['qty'] = modifierQty - 1;
                        }
                    }
                } )
            } )

        }
    }).done(function (item) {
        content = '<div class="osnModifierPanelHeader">';
        content += '<div><h4>'+item.name+' ($ '+parseFloat(item.price/100).toFixed(2)+')</h4></div>';
        content += '<span class="icon-close" onclick="osnCloseModalModifier()"></span>';
        content += '</div>';
        content += '<div id="osnItemContent" class="osnItemContent">';
        content += '</div>';
        if (osn_Item_Modifers.length > 0){
            content += '<div class="osnModifierContent"'+((osn_Item_Modifers.length == 1)?' style=" grid-auto-rows: auto 57px;"':'')+'>';

        if (osn_Item_Modifers.length > 1) {
            content += '<div id="osnModifierGroupMenu">';
            content += '<div id="osnMenuMG" style="overflow-x: scroll;position: absolute;width: 100%;">'
            content += '<ul>';
            osn_Item_Modifers.forEach(function(element,index) {
                content += '<li><button id="'+element.uuid+'" onclick="osnGetModifier('+index+')">'+element.name+'</button>';
            });
            content += '</ul>';
            content += '</div>';
            content += '<span id="OsnPanScrollLeft" onmouseleave="osnOnMouseLeave(this)" onmouseenter="osnOnMouseEnter(this,\'osnMenuMG\')" class="panner bounce-left" data-scroll-modifier="-1"><div><i class="icon-arrow-left-alt1"></i></div></span>';
            content += '<span id="OsnPanScrollRight" onmouseleave="osnOnMouseLeave(this)" onmouseenter="osnOnMouseEnter(this,\'osnMenuMG\')" class="panner bounce-right" data-scroll-modifier="1"><div><i class="icon-arrow-right-alt1"></i></div></span>';
            content += '<span onclick="osnCloseModalModifier()" class="panner closeModal"><div><i class="icon-cancel"></i></div></span>';
            content += '<span id="osnSlideIndicator"><i></i></span>';
            content += '</div>';
        }
        content += '<div id="osnModifierPanelContent" class="osnModifierPanelContent">';
        content += '</div>';
        if (osn_Item_Modifers.length > 1){
            content += '<div id="osnModifierPanelPaginateBtn" class="osnModifierPanelBtn">';
            content += '<button class="osnModifierPanelBtnPrevious osnHide" data-op="previous" onclick="osnGetModifier(this)">Previous</button>';
            content += '<button class="osnModifierPanelBtnNext" data-op="next" onclick="osnGetModifier(this)">Next</button>';
            content += '</div>';
        }
        content += '</div>';}
        content += '<div class="osnModifierPanelFooter">';
        content +='<div id="osnChooseQuantity">'
                +'<h5>quantity</h5>'
                + '<div class="modifier-quantity-controls">'
                + '<a data-op="decrement" onclick="osnSelectQuantity(this)" class="modifier-quantity"><i class="icon-minus"></i></a>'
                +'<span id="osnItemQuantity" class="product-quantity-wrapper"><span class="product-quantity-times">x</span>'+osnItemQty+'</span>'
                +'<a onclick="osnSelectQuantity(this)"  data-op="increment" class="modifier-quantity"><i class="icon-plus"></i></a>'
                +'</div>'
                +'</div>';

        content += '<div class="osnModifierPanelPriceDetails">';
        content += '<h5>Total</h5>';
        content += '<p>$ '+parseFloat((finalPrice/100)*osnItemQty).toFixed(2)+'</p>';


        content += '</div>';
        content += '<div id="osnModifierPanelPaginateBtn" class="osnModifierPanelBtn">';
        content += '<button class="osnModifierPanelBtnAddToCart" onclick="'+((index == null)?'osnAddItemToCart(\'' + item_uuid + '\')':'osnUpdateItemInCart(\'' + item_uuid + '\',\'' + index + '\')')+'">Add To Cart</button>';
        content += '</div>';
        content += '</div>';
        jQuery('.osnModifierPanel').html(content);
        if (osn_Item_Modifers.length > 0) osnGetModifier(0);
        osnStopLaoding();
        jQuery('body').addClass('blurBackgroundWhenModalIsopen');
        jQuery('.btn-cart').addClass('blurBackgroundWhenModalIsopen');
        jQuery('#osnItemQuantity').html('<span class="product-quantity-times">x</span>'+osnItemQty);
        jQuery('.osnModifierPanelPriceDetails').find("p").html("$ "+parseFloat((finalPrice/100)*osnItemQty).toFixed(2));
    });
}
function osnUpdateItemInCart(uuid,index) {
    jQuery.post(moo_params.ajaxurl,{'action':'moo_deleteItemFromcart',"item":index}, function (data) {
        if(data.status != "success"){
            jQuery('#itemInCart'+index).remove();
        };
    }).done(function (data){
        //update cart
        osnAddItemToCart(uuid);
    });


}

function osnSelectQuantity(e){
    op = jQuery(e).data("op");
    if(op==='increment'){
        osnItemQty++;
    }else{
        if(osnItemQty>1)osnItemQty--;
    }
    jQuery('#osnItemQuantity').html('<span class="product-quantity-times">x</span>'+osnItemQty);
    jQuery('.osnModifierPanelPriceDetails').find("p").html("$ "+parseFloat((finalPrice/100)*osnItemQty).toFixed(2));
}
function osnGetModifier(page){

    if(isNaN(page)){
    op = jQuery(page).data('op');
    if(op==='next'){
        if(osn_Item_Modifers.length > osn_Item_Modifers_Pagination+1){
            group = osn_Item_Modifers[osn_Item_Modifers_Pagination];
            if(group.qty >= group.min_required || group.min_required === null){
            osn_Item_Modifers_Pagination +=1;
            page = osn_Item_Modifers_Pagination;
            jQuery('.osnModifierPanelBtnPrevious').removeClass('osnHide');
            if(osn_Item_Modifers.length == osn_Item_Modifers_Pagination+1){
                jQuery('.osnModifierPanelBtnNext').addClass('osnHide');
            }}else{
                spanMessage = jQuery('.osnModifierGroup').find('span');
                if(spanMessage.hasClass('error'))spanMessage.removeClass('error');else
                spanMessage.addClass('error');
            }
        }else if(osn_Item_Modifers.length == osn_Item_Modifers_Pagination+1){
            jQuery('.osnModifierPanelBtnNext').addClass('osnHide');
            jQuery('.osnModifierPanelBtnPrevious').removeClass('osnHide');
        }
     }else{
        if(osn_Item_Modifers.length >  1){
            osn_Item_Modifers_Pagination -=1;
            page = osn_Item_Modifers_Pagination;
            if(osn_Item_Modifers_Pagination == 0){
                jQuery('.osnModifierPanelBtnPrevious').addClass('osnHide');
            }
            jQuery('.osnModifierPanelBtnNext').removeClass('osnHide');
        }else if(osn_Item_Modifers.length == 1){
            jQuery('.osnModifierPanelBtnPrevious').addClass('osnHide');
        }
     }}
    if(page == 0){
        jQuery('.osnModifierPanelBtnPrevious').addClass('osnHide');
        jQuery('.osnModifierPanelBtnNext').removeClass('osnHide');
    }else if(osn_Item_Modifers.length == page+1){
        jQuery('.osnModifierPanelBtnNext').addClass('osnHide');
        jQuery('.osnModifierPanelBtnPrevious').removeClass('osnHide');
    }else if(osn_Item_Modifers.length > page && page > 0){
        jQuery('.osnModifierPanelBtnPrevious').removeClass('osnHide');
        jQuery('.osnModifierPanelBtnNext').removeClass('osnHide');
    }
    if(!isNaN(page)){
        jQuery('#osnModifierGroupMenu').find('button').removeClass('active');
        jQuery('#'+osn_Item_Modifers[page].uuid).addClass('active');
        content='<div class="osnModifierGroup"><span class="error">'+OsnQtyComment(osn_Item_Modifers[page].min_required,osn_Item_Modifers[page].max_allowd)+'</span>'+((osn_Item_Modifers.length == 1)?'<span onclick="osnCloseModalModifier()" class="closeModal" style="float: right;margin: 0 6px;font-size: 27px;"><div><i class="icon-cancel"></i></div></span>':'')+'</div>';
        content+='<div class="osnModifierContainer">';
        osn_Item_Modifers[page].modifiers.forEach(function (modifier) {
            content+='<div id="'+modifier.uuid+'" class="osnItemModifierCard '+((modifier.selected)?'selected':'')+'">';
            if(window.modifiersHasImage)content+='<div onclick="OsnSelectModifier(\''+osn_Item_Modifers[page].uuid+'\',\''+modifier.uuid+'\')" class="osnModifierImage"><i class="icon-spoon-knife"></i></div>';
            content+='<div onclick="OsnSelectModifier(\''+osn_Item_Modifers[page].uuid+'\',\''+modifier.uuid+'\')"><h5>'+modifier.name+'</h5>';
            content+='<p>$'+parseFloat(modifier.price/100).toFixed(2)+'</p></div>';
            if(window.modifiers_settings.qtyForAll){
            if(osn_Item_Modifers[page].max_allowd == 1 && osn_Item_Modifers[page].min_required == 1){

            }else if(modifier.price != 0) {content+= '<div class="modifier-quantity-controls '+((modifier.selected)?'':'osnHide')+'">';
            content+='<a data-op="decrement" onclick="osnQuantitySelectedModifier(this)" data-uuid-group-modifier="'+osn_Item_Modifers[page].uuid+'" data-uuid-modifier="'+modifier.uuid+'" class="modifier-quantity"><i class="icon-minus"></i></a>';
            content+='<span class="product-quantity-wrapper"><span class="product-quantity-times">x</span>'+((modifier.qty)?modifier.qty:'1')+'</span>';
            content+='<a onclick="osnQuantitySelectedModifier(this)" data-uuid-modifier="'+modifier.uuid+'" data-uuid-group-modifier="'+osn_Item_Modifers[page].uuid+'" data-op="increment" class="modifier-quantity"><i class="icon-plus"></i></a>';
            content+= '</div>';
        }else{
            if(window.modifiers_settings.qtyForZeroPrice){
                content+= '<div class="modifier-quantity-controls '+((modifier.selected)?'':'osnHide')+'">';
                content+='<a data-op="decrement" onclick="osnQuantitySelectedModifier(this)" data-uuid-group-modifier="'+osn_Item_Modifers[page].uuid+'" data-uuid-modifier="'+modifier.uuid+'" class="modifier-quantity"><i class="icon-minus"></i></a>';
                content+='<span id="osnModifierQuantity" class="product-quantity-wrapper"><span class="product-quantity-times">x</span>'+((modifier.qty)?modifier.qty:'1')+'</span>';
                content+='<a onclick="osnQuantitySelectedModifier(this)" data-uuid-modifier="'+modifier.uuid+'" data-uuid-group-modifier="'+osn_Item_Modifers[page].uuid+'" data-op="increment" class="modifier-quantity"><i class="icon-plus"></i></a>';
                content+= '</div>';
            }
        }
    }
            content+='</div>';
        });
        content+='</div>';
        jQuery('#osnModifierPanelContent').html(content);
        osn_Item_Modifers_Pagination = page;
    }
}
function OsnQtyComment(min_required,max_allowd){
    var html ='';
if(min_required != null && max_allowd != null && min_required == 1  && max_allowd == 1)
{
    html +='<i class="icon-notice"></i> required';
}
else
{
    if(min_required != null && max_allowd != null && max_allowd == min_required )
    {
        html +='<i class="icon-notice"></i> Must choose '+min_required +' options';
    }
    else
    {
        if(min_required != null && max_allowd != null && min_required >= 1 &&  max_allowd > 1)
        {
            html +='<i class="icon-notice"></i> Must choose between '+min_required +' & '+max_allowd+' options';
        }
        else
        {
            if(min_required != null && min_required == 1)
                html +='<i class="icon-notice"></i> Must choose at least 1 option';
            if(min_required != null && min_required > 1)
                html +='<i class="icon-notice"></i> Must choose at least '+min_required +' options';

            if(max_allowd != null && max_allowd > 1)
                html +='<i class="icon-notice"></i> Select up to '+max_allowd +' options';
            if(max_allowd != null && max_allowd == 1)
                html +='<i class="icon-notice"></i> Select one option';
        }
    }
}
return html;
}
function OsnQtyCheck(min,max,qty){
    if(min == 1 && max == 1)
        if(qty == 1) return true;
        else return false;
    else if(min >= 1 && max === null)
        if(qty >= min) return true;
        else return false;
    else if(min == 1 && max > 1)
        if(qty > 1) return true;
        else false;
    else if(min > 1 && max > 1)
        if(qty >= min && qty <= max) return true;
        else return false;
    else if(min === null && max > 1)
        if(qty <= max || !qty) return true;
        else return false;
    else if(min == max && min != null)
        if(qty == min)return true;
        else return false;
    else if(min === null && max === null)
        return true;
}

function OsnSelectModifier(Guuid,uuid){


    modifier_div = jQuery("#"+uuid);

    group = osn_Item_Modifers.filter(function (value){return value.uuid === Guuid})[0];
    modifier = group.modifiers.filter(function (value) { return value.uuid === uuid})[0];
    if(modifier["selected"] == true && modifier_div.hasClass("selected")){
        modifier["selected"] = false;
        jQuery("#"+uuid).find(".product-quantity-wrapper").html('<span class="product-quantity-times">x</span>'+1);
        jQuery("#"+uuid).find(".modifier-quantity-controls").addClass('osnHide');
        finalPrice -= (parseFloat(modifier["price"])*modifier["qty"]);
        jQuery('.osnModifierPanelPriceDetails').find("p").html("$ "+parseFloat((finalPrice/100)*osnItemQty).toFixed(2));
        group["qty"] -= modifier["qty"];
        modifier["qty"] = 1;
        modifier_div.removeClass("selected");
    }else{
    if(!group.qty || group.qty < group.max_allowd || group.max_allowd == null){
        if(group["qty"])
        group["qty"]+=1;
        else
        group["qty"]=1;
        modifier["selected"] = true;
        modifier["qty"] = 1;
        jQuery("#"+uuid).find(".product-quantity-wrapper").html('<span class="product-quantity-times">x</span>'+1);
        finalPrice += (parseFloat(modifier["price"]));
        if((group.max_allowd != 1 && group.min_required != 1 ) || (group.min_required != null && group.min_required == 1 && (group.max_allowd > 1 || group.max_allowd == null)  ) ){jQuery("#"+uuid).find(".modifier-quantity-controls").removeClass('osnHide');}
        jQuery('.osnModifierPanelPriceDetails').find("p").html("$ "+parseFloat((finalPrice/100)*osnItemQty).toFixed(2));
        modifier_div.addClass("selected");
    }else if(group.max_allowd == 1 && group.min_required == 1){
        group.modifiers.forEach(function (modif){
            if(jQuery('#'+modif.uuid).hasClass("selected")){
                modif["selected"] = false;
                jQuery("#"+modif.uuid).find(".product-quantity-wrapper").html('<span class="product-quantity-times">x</span>'+1);
                jQuery("#"+modif.uuid).find(".modifier-quantity-controls").addClass('osnHide');
                finalPrice -= (parseFloat(modif["price"])*modif["qty"]);
                jQuery('.osnModifierPanelPriceDetails').find("p").html("$ "+parseFloat((finalPrice/100)*osnItemQty).toFixed(2));
                group["qty"]-=modif["qty"];
                modif["qty"] = 1;
                jQuery("#"+modif.uuid).removeClass("selected");
            }
        });
        if(group["qty"])
        group["qty"]+=1;
        else
        group["qty"]=1;
        modifier["selected"] = true;
        modifier["qty"] = 1;
        finalPrice += (parseFloat(modifier["price"]));
        jQuery('.osnModifierPanelPriceDetails').find("p").html("$ "+parseFloat((finalPrice/100)*osnItemQty).toFixed(2));
        modifier_div.addClass("selected");
    }
}
}

function osnQuantitySelectedModifier(e) {

    modifier_div = jQuery("#"+jQuery(e).data('uuid-modifier'));
    group = osn_Item_Modifers.find(function (value){return value.uuid === jQuery(e).data('uuid-group-modifier')});
    modifier = group.modifiers.find(function (value){ return value.uuid === jQuery(e).data('uuid-modifier')});
    if(jQuery(e).data('op') === 'increment'){
        if(group.qty < group.max_allowd || group.max_allowd == null){
            group["qty"]+=1;
            modifier["qty"]+=1;
            jQuery("#"+jQuery(e).data('uuid-modifier')).find(".product-quantity-wrapper").html('<span class="product-quantity-times">x</span>'+modifier["qty"]);
            finalPrice += (parseFloat(modifier["price"]));
            jQuery('.osnModifierPanelPriceDetails').find("p").html("$ "+parseFloat((finalPrice/100)*osnItemQty).toFixed(2));
        }
    }else{
        if((modifier["qty"]-1)==0)OsnSelectModifier(jQuery(e).data('uuid-group-modifier'),jQuery(e).data('uuid-modifier'));
        else{
            group["qty"]-=1;
            modifier["qty"]-=1;
            jQuery("#"+jQuery(e).data('uuid-modifier')).find(".product-quantity-wrapper").html('<span class="product-quantity-times">x</span>'+modifier["qty"]);
            finalPrice -= (parseFloat(modifier["price"]));
            jQuery('.osnModifierPanelPriceDetails').find("p").html("$ "+parseFloat((finalPrice/100)*osnItemQty).toFixed(2));
        }
    }



}