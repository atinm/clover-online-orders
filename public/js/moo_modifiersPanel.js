/**
 * Created by Med EL BANYAOUI on 8/25/2017.
 */

/*
* @param : data : a list of modifiers
* @param : item_id : the item identifier that we will build the modifier selection for it
* @param : qty : the ordered qty for this item
* @param : setting : a param if we want customize the modifiers panel
 */

function mooBuildModifiersPanel(data,item_id,qty,settings)
{
    var defaultSettings = {
        "inlineDisplay" : false,
        "qtyForAll" : true,
        "qtyForZeroPrice" : true
    };
    if (settings === undefined || settings === null) {
        settings = defaultSettings;
    }
    var nb_modifiers = data.length;
    var required_modifiers = "";
    if(nb_modifiers>1)
    {
        if(typeof settings.inlineDisplay !== "undefined" && settings.inlineDisplay === true) {
            var html = '<div tabindex="-1" aria-label="Choose options dialog" aria-modal="true" role="dialog" class="white-popup moo-modifiersPanel" style="background-color: #ffffff">';
        } else {
            var html = '<div tabindex="-1" aria-label="Choose options dialog" aria-modal="true" role="dialog" class="white-popup moo-modifiersPanel" >';
        }
        html += '<form action="" id="moo-modifiers-for-'+item_id+'">'+
            '<div class="moo-row" style="margin-bottom: 15px;">';
        html += '<div class="moo-col-md-6 moo-col-lg-6 moo-col-sm-12 moo-col-xs-12">';
        for(var i=0;i<Math.ceil(nb_modifiers/2);i++)
        {
            var modifier_group = data[i];
            var modifierG_uuid = modifier_group.uuid;

            if(modifier_group.min_required != null && modifier_group.min_required > 0)
                required_modifiers += modifier_group.uuid+"-"+modifier_group.min_required+";";

            html += '<div class="mooModifierGroup">'+
                '<button type="button" class="mooModifiers-title" role="button" tabindex="0" aria-controls="mooModifiers-wrapper-for-'+modifierG_uuid+'" aria-expanded="true" onclick="MooClickOnModifiersCollaps(this,\''+modifierG_uuid+'\')"><span>-</span>'+modifier_group.name;
            html +='<span class="mooModifiers-title-span" tabindex="0">';
            html +=mooBuildNbModifierSpan(modifier_group.min_required,modifier_group.max_allowd);
            html +='</span>';
            html +='</button>';
            html += '<div id="mooModifiers-wrapper-for-'+modifierG_uuid+'" class="mooModifiers-wrapper mooModifiers-wrapper-for-'+modifierG_uuid+'">';

            for(var j=0;j<modifier_group.modifiers.length;j++)
                html += mooBuildOneModifierLineHtml(modifier_group.modifiers[j],modifierG_uuid,modifier_group.min_required,modifier_group.max_allowd,settings);

            html+=  '</div>';
            html+=  '</div>';
        }

        html +=  '</div>';

        html += '<div class="moo-col-md-6 moo-col-lg-6 moo-col-sm-12 moo-col-xs-12">';

        for(var i=Math.ceil(nb_modifiers/2);i<nb_modifiers;i++)
        {
            var modifier_group = data[i];
            var modifierG_uuid = modifier_group.uuid;

            if(modifier_group.min_required != null && modifier_group.min_required > 0)
                required_modifiers += modifier_group.uuid+"-"+modifier_group.min_required+";";

            html += '<div class="mooModifierGroup">'+
                '<button type="button" class="mooModifiers-title" aria-controls="mooModifiers-wrapper-for-'+modifierG_uuid+'" aria-expanded="true" onclick="MooClickOnModifiersCollaps(this,\''+modifierG_uuid+'\')"><span>-</span>'+modifier_group.name;
            html +='<span class="mooModifiers-title-span" tabindex="0" >';
            html +=mooBuildNbModifierSpan(modifier_group.min_required,modifier_group.max_allowd);
            html +='</span>';
            html +='</button>';
            html += '<div id="mooModifiers-wrapper-for-'+modifierG_uuid+'"  class="mooModifiers-wrapper mooModifiers-wrapper-for-'+modifierG_uuid+'">';

            for(var j=0;j<modifier_group.modifiers.length;j++)
                html += mooBuildOneModifierLineHtml(modifier_group.modifiers[j],modifierG_uuid,modifier_group.min_required,modifier_group.max_allowd,settings);

            html+=  '</div>';
            html+=  '</div>';
        }

        html+=   '</div>';
    } else {
        if(typeof settings.inlineDisplay !== "undefined" && settings.inlineDisplay === true)
        {
            var html = '<div class="white-popup moo-modifiersPanel" style="max-width:450px;background-color: #ffffff">';
        }
        else
        {
            var html = '<div class="white-popup moo-modifiersPanel" style="max-width:450px;">';
        }

        html += '<form action="" id="moo-modifiers-for-'+item_id+'">'+
            '<div class="moo-row" style="margin-bottom: 15px;">'+
            '<div class="moo-col-md-12 moo-col-lg-12 moo-col-sm-12 moo-col-xs-12">';

        for(var i=0;i<nb_modifiers;i++) {
            var modifier_group = data[i];
            var modifierG_uuid = modifier_group.uuid;

            if(modifier_group.min_required != null && modifier_group.min_required > 0)
                required_modifiers += modifier_group.uuid+"-"+modifier_group.min_required+";";

            html += '<div class="mooModifierGroup">'+
                '<button type="button" class="mooModifiers-title" aria-controls="mooModifiers-wrapper-for-'+modifierG_uuid+'" aria-expanded="true" onclick="MooClickOnModifiersCollaps(this,\''+modifierG_uuid+'\')"><span>-</span>'+modifier_group.name;
            html +='<span class="mooModifiers-title-span" tabindex="0">';
            html +=mooBuildNbModifierSpan(modifier_group.min_required,modifier_group.max_allowd);
            html +='</span>';
            html +='</button>';
            html += '<div id="mooModifiers-wrapper-for-'+modifierG_uuid+'" class="mooModifiers-wrapper mooModifiers-wrapper-for-'+modifierG_uuid+'">';

            for(var j=0;j<modifier_group.modifiers.length;j++)
                html += mooBuildOneModifierLineHtml(modifier_group.modifiers[j],modifierG_uuid,modifier_group.min_required,modifier_group.max_allowd,settings);

            html+=  '</div>';
            html+=  '</div>';
        }


        html+=  '</div>';
    }

    html+=   '</div>';

    html+=   '<div class="mooModifier-addToCartBtn2" onclick="ClickOnAddToCartBtnFIWM(event,\''+required_modifiers+'\',\''+item_id+'\',\''+qty+'\')"><a  href="#">Add to Cart</a></div>';
    html+=   '<div class="mooModifier-closeBtn" onclick="removeModifiersList(event,\''+item_id+'\')"><a  href="#">Close</a></div>';

    html+=   '</form></div>';

if(typeof settings.inlineDisplay === "undefined" || settings.inlineDisplay === false)
{
    jQuery.magnificPopup.open({
        items: {
            src: html,
            type: 'inline'
        },
        midClick: true,
        fixedContentPos: true,
        fixedBgPos: true,
        autoFocusLast: true,
        overflowY: 'scroll',
        callbacks : function() {
            var startWindowScroll = 0;
            return {
                beforeOpen: function() {
                    startWindowScroll = jQuery(window).scrollTop();
                    jQuery('html').addClass('mfp-helper');
                },
                close: function() {
                    jQuery('html').removeClass('mfp-helper');
                    setTimeout(function(){
                        jQuery('body').animate({ scrollTop: startWindowScroll }, 0);
                    }, 0);
                }
            }
        }
    });
} else {
    jQuery(".moo-modifiersContainer-for-"+item_id).html(html)
}
}
function mooBuildOneModifierLineHtml(modifier,modifierG_uuid,min,max,settings)
{

    var modifier_price = parseFloat(modifier.price);
    modifier_price = modifier_price/100;
    var uuid =  modifier.uuid;
    var html='';
    if( modifier_price > 0 ) {
        html += '<div class="moo-row">'+
            '<div class="moo-col-lg-1 moo-col-md-1 moo-col-sm-1 moo-col-xs-1">'+
            '<input role="checkbox" name="'+modifierG_uuid+'" value="'+uuid+'" class="mooModifierCheckbox" type="checkbox" onchange="mooChangeModifierLine(event,\''+uuid+'\',\''+min+'\',\''+max+'\')">'+
            '</div>'+
            '<div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-5 moo-col-xs-5 mooModifier-name" onclick="mooClickOnModifierLine(event,\''+uuid+'\',\''+min+'\',\''+max+'\')" tabindex="0">'+modifier.name+'</div>'+
            '<div class="moo-col-lg-3 moo-col-md-3 moo-col-sm-2 moo-col-xs-2 mooModifier-price" tabindex="0">'+((modifier_price>0)?'$'+modifier_price.toFixed(2):'')+'</div>'+
            '<div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-5 moo-col-xs-5">';
            if(typeof settings.qtyForAll !== "undefined" && settings.qtyForAll === false ) {
                /* Qty for modifiers is disabled */
                html +='';
            } else {
                 html += '<div class="moo-input-group mooModifierLine-QtyContainer">'+
                        '<span class="moo-input-group-btn">'+
                        '<a class="moo-btn moo-btn-primary mooOpBtn" onclick="ClickOnMooOpBtnMinus(\''+modifierG_uuid+'\',\''+uuid+'\',\''+max+'\')">-</a>'+
                        '</span>'+
                        '<input tabindex="0" aria-label="The option quantity" class="mooInputQty moo-form-control" value="1" id="mooModifierInputQty-for-'+uuid+'">'+
                        '<span class="input-group-btn">'+
                        '<a class="moo-btn moo-btn-primary mooOpBtn" onclick="ClickOnMooOpBtnPlus(\''+modifierG_uuid+'\',\''+uuid+'\',\''+max+'\')">+</a>'+
                        '</span>'+
                        '</div>' ;
            }


        html += '</div>'+
                '</div>';
    } else {
        html += '<div class="moo-row">'+
            '<div class="moo-col-lg-1 moo-col-md-1 moo-col-sm-1 moo-col-xs-1">'+
            '<input role="checkbox" name="'+modifierG_uuid+'" value="'+uuid+'" class="mooModifierCheckbox" type="checkbox" onchange="mooChangeModifierLine(event,\''+uuid+'\',\''+min+'\',\''+max+'\')">'+
            '</div>'+
            '<div class="moo-col-lg-7 moo-col-md-7 moo-col-sm-7 moo-col-xs-7 mooModifier-name" onclick="mooClickOnModifierLine(event,\''+uuid+'\',\''+min+'\',\''+max+'\')" tabindex="0">'+modifier.name+'</div>'+
            '<div class="moo-col-lg-4 moo-col-md-4 moo-col-sm-5 moo-col-xs-5">';

        if(typeof settings.qtyForAll !== "undefined" && settings.qtyForAll === false ) {
            /* Qty for modifiers is disabled */
            html +='';
        } else {
            if(typeof settings.qtyForZeroPrice !== "undefined" && settings.qtyForZeroPrice === false )
            {
                /* Qty for modifiers when price is zero is disabled */
                html +='';
            }
            else
            {
                html += '<div class="moo-input-group mooModifierLine-QtyContainer">'+
                    '<span class="moo-input-group-btn">'+
                    '<a class="moo-btn moo-btn-primary mooOpBtn" onclick="ClickOnMooOpBtnMinus(\''+modifierG_uuid+'\',\''+uuid+'\',\''+max+'\')">-</a>'+
                    '</span>'+
                    '<input tabindex="0" aria-label="The option quantity" class="mooInputQty moo-form-control" value="1" id="mooModifierInputQty-for-'+uuid+'">'+
                    '<span class="input-group-btn">'+
                    '<a class="moo-btn moo-btn-primary mooOpBtn" onclick="ClickOnMooOpBtnPlus(\''+modifierG_uuid+'\',\''+uuid+'\',\''+max+'\')">+</a>'+
                    '</span>'+
                    '</div>' ;
            }
        }
        html +=  '</div>'+
                 '</div>';
    }
    return html;
}
function mooBuildNbModifierSpan(min_required,max_allowd)
{
    var html ='';
    if(min_required != null && max_allowd != null && min_required == 1  && max_allowd == 1) {
        html +=' (required)';
    } else {
        if(min_required != null && max_allowd != null && max_allowd == min_required ) {
            html +=' (Must choose '+min_required +' options)';
        } else {
            if(min_required != null && max_allowd != null && min_required >= 1 &&  max_allowd > 1) {
                html +=' (Must choose between '+min_required +' & '+max_allowd+' options)';
            } else {
                if(min_required != null && min_required == 1)
                    html +=' (Must choose at least 1 option)';
                if(min_required != null && min_required > 1)
                    html +=' (Must choose at least '+min_required +' options)';

                if(max_allowd != null && max_allowd > 1)
                    html +=' (Select up to '+max_allowd +' options)';
                if(max_allowd != null && max_allowd == 1)
                    html +=' (Select one option)';
            }
        }
    }
    return html;
}
function mooClickOnModifierLine(event,modifier_uuid,min,max)
{
    event.preventDefault();
    min = parseInt(min);
    max = parseInt(max);
    var checkboxContainer = jQuery(event.target).prev();
    var checkbox = jQuery(".mooModifierCheckbox",checkboxContainer);
    var group = "input:checkbox[name=\"" + checkbox.attr("name") + "\"]";

    jQuery(".mooInputQty",checkbox.parent().parent()).val(1);

    if(max == 1) {
        if(!checkbox.prop("checked")) {
            jQuery(group).prop("checked", false);
            jQuery(group).each(function (index,value) {
                element = jQuery(value);
                //remove the backgroud
                element.parent().parent().removeClass("mooModifier-checked");
                //Hide the qty filed
                jQuery(".moo-input-group",element.parent().parent()).hide();
            });

            checkbox.prop("checked",true);
            //add a background
            jQuery(event.target).parent().addClass("mooModifier-checked");
            //Show the qty field
            //jQuery(".moo-input-group",jQuery(event.target).parent()).show();
        } else {
            checkbox.prop("checked",false);
            //remove the backgroud
            jQuery(event.target).parent().removeClass("mooModifier-checked");
            //Hide the qty filed
            jQuery(".moo-input-group",jQuery(event.target).parent()).hide();

        }
    } else {
        if(max>1) {
            var group = "input:checkbox[name=\"" + checkbox.attr("name") + "\"]:checked";
            var group_not_checked = "input:checkbox[name=\"" + checkbox.attr("name") + "\"]:not(:checked)";

            if(!checkbox.prop("checked")) {
                checkbox.prop("checked",true);

                //add a background
                jQuery(event.target).parent().addClass("mooModifier-checked");
                //Show the qty field
                jQuery(".moo-input-group",jQuery(event.target).parent()).show();

            } else {
                checkbox.prop("checked",false);
                //remove the backgroud
                jQuery(event.target).parent().removeClass("mooModifier-checked");
                //Hide the qty filed
                jQuery(".moo-input-group",jQuery(event.target).parent()).hide();
            }

           // var nb_checked_boxes = jQuery(group).length;

            var nb_checked_boxes = 0;

            jQuery(group).each(function (index,element) {
                var modifier_uuid = jQuery(element).val();
                var qty = parseInt(jQuery("#mooModifierInputQty-for-"+modifier_uuid).val());
                nb_checked_boxes +=  (qty>1)?qty:1;
            });

            if(nb_checked_boxes >= max)
            {
                jQuery(group_not_checked).each(function (index,value) {
                    //disable other checkboxes
                    element = jQuery(value);
                    element.parent().parent().addClass("mooModifier-disabled");
                });
            }
            else
            {
                jQuery(group_not_checked).each(function (index,value) {
                    //enable other checkboxes
                    element = jQuery(value);
                    element.parent().parent().removeClass("mooModifier-disabled");
                });
            }
        }
        else
        {
            if(!checkbox.prop("checked"))
            {
                checkbox.prop("checked",true);
                //add a background
                jQuery(event.target).parent().addClass("mooModifier-checked");
                //Show the qty field
                jQuery(".moo-input-group",jQuery(event.target).parent()).show();
            } else {
                checkbox.prop("checked",false);
                //remove the backgroud
                jQuery(event.target).parent().removeClass("mooModifier-checked");
                //Hide the qty filed
                jQuery(".moo-input-group",jQuery(event.target).parent()).hide();
            }
        }
    }
}
function mooChangeModifierLine(event,modifier_uuid,min,max)
{
    event.preventDefault();
    var checkbox = jQuery(event.target);
    jQuery(".mooInputQty",checkbox.parent().parent()).val(1);

    if(max == 1 )
    {
        if(checkbox.prop("checked"))
        {
            var group = "input:checkbox[name=\"" + checkbox.attr("name") + "\"]";
            jQuery(group).prop("checked", false);
            checkbox.prop("checked",true);

            jQuery(group).each(function (index,value) {
                //remove the backgroud
                var element = jQuery(value);
                element.parent().parent().removeClass("mooModifier-checked");
                //Hide the qty filed
                jQuery(".moo-input-group",element.parent().parent()).hide();
            });

            //add a background
            checkbox.parent().parent().addClass("mooModifier-checked");
            //Show the qty field
            // jQuery(".moo-input-group",checkbox.parent().parent()).show();
        } else {
            //remove the backgroud
            checkbox.parent().parent().removeClass("mooModifier-checked");
            //Hide the qty filed
            jQuery(".moo-input-group",checkbox.parent().parent()).hide();
        }
    } else {
        if(max > 1) {
            MooModifiersMax(checkbox.attr("name"),max,checkbox, jQuery(checkbox).parent().parent());
        } else {
            if(checkbox.prop("checked")) {
                //add a background
                jQuery(event.target).parent().parent().addClass("mooModifier-checked");
                //Show the qty field
                jQuery(".moo-input-group",jQuery(event.target).parent().parent()).show();
            } else {
                //remove the backgroud
                jQuery(event.target).parent().parent().removeClass("mooModifier-checked");
                //Hide the qty filed
                jQuery(".moo-input-group",jQuery(event.target).parent().parent()).hide();
            }
        }
    }


}
function ClickOnMooOpBtnPlus(modifier_group_uuid,modifier_uuid,max)
{
    var qty = jQuery("#mooModifierInputQty-for-"+modifier_uuid).val();
    var newQty = parseInt(qty);

    if(max != null && max > 1)
    {
        //Get the nb of checked modifiers
        var group_checked = "input:checkbox[name=\"" + modifier_group_uuid + "\"]:checked";
        //calc the new max
        var nb_checked_boxes = 0;
        jQuery(group_checked).each(function (index,element) {
            var modifier_uuid = jQuery(element).val();
            var qty = parseInt(jQuery("#mooModifierInputQty-for-"+modifier_uuid).val());
            nb_checked_boxes +=  (qty>1)?qty:1;
        });
        if(max>nb_checked_boxes)
            newQty++;

        var checkboxContainer =  jQuery("#mooModifierInputQty-for-"+modifier_uuid).parent().parent().parent();
        var checkbox = jQuery(".mooModifierCheckbox",checkboxContainer);

        jQuery("#mooModifierInputQty-for-"+modifier_uuid).val(newQty);
        MooModifiersMax(modifier_group_uuid,max,checkbox,checkboxContainer);
    }
    else
    {
        newQty += 1;
        jQuery("#mooModifierInputQty-for-"+modifier_uuid).val(newQty);
    }



}
function ClickOnMooOpBtnMinus(modifier_group_uuid,modifier_uuid,max)
{
    var qty = jQuery("#mooModifierInputQty-for-"+modifier_uuid).val();
    var newQty = parseInt(qty) - 1;
    if(newQty<1)
        jQuery("#mooModifierInputQty-for-"+modifier_uuid).val(1);
    else
        jQuery("#mooModifierInputQty-for-"+modifier_uuid).val(newQty)

    if(max != null && max > 1)
    {
        var checkboxContainer =  jQuery("#mooModifierInputQty-for-"+modifier_uuid).parent().parent().parent();
        var checkbox = jQuery(".mooModifierCheckbox",checkboxContainer);
        MooModifiersMax(modifier_group_uuid,max,checkbox,checkboxContainer);
    }
}
function MooModifiersMax(modifier_group_uuid,max,checkbox,elm)
{
    var group_checked = "input:checkbox[name=\"" + modifier_group_uuid + "\"]:checked";
    var group_not_checked = "input:checkbox[name=\"" + modifier_group_uuid + "\"]:not(:checked)";
    var nb_checked_boxes = 0;
    jQuery(group_checked).each(function (index,element) {
        var modifier_uuid = jQuery(element).val();
        var qty = parseInt(jQuery("#mooModifierInputQty-for-"+modifier_uuid).val());
        nb_checked_boxes +=  (qty>1)?qty:1;
    });
    if(nb_checked_boxes >= max)
    {
        jQuery(group_not_checked).each(function (index,value) {
            //disable other checkboxes
            var element = jQuery(value);
            element.parent().parent().addClass("mooModifier-disabled");
        });
    }
    else
    {
        jQuery(group_not_checked).each(function (index,value) {
            //enable other checkboxes
            var element = jQuery(value);
            element.parent().parent().removeClass("mooModifier-disabled");

        });
    }

    if(checkbox.prop("checked"))
    {
        //add a background
        elm.addClass("mooModifier-checked");
        //Show the qty field
        jQuery(".moo-input-group",elm).show();
    }
    else
    {
        //remove the background
        elm.removeClass("mooModifier-checked");
        //Hide the qty filed
        jQuery(".moo-input-group",elm).hide();
    }

}
function MooClickOnModifiersCollaps(target,uuid)
{
    var wrapper = jQuery(".mooModifiers-wrapper-for-"+uuid);
    if(wrapper.is(':visible'))
    {
        wrapper.slideUp();
        //jQuery(target).text('+');
        jQuery('span:first-child',jQuery(target)).text('+');
    }
    else
    {
        wrapper.slideDown();
        jQuery('span:first-child',jQuery(target)).text('-');
    }
}
function ClickOnAddToCartBtnFIWM(event,requierd_modifiers,item_uuid,qty)
{
    event.preventDefault();
    var form = jQuery("#moo-modifiers-for-"+item_uuid).serializeArray();
    var r_modifiers = [];
    var selected_modifiers = {};
    var item_qty = parseInt(qty);

    //Convert required modifiers ro array
    requierd_modifiers = requierd_modifiers.split(";");
    requierd_modifiers.forEach(function (element) {
        var t = element.split("-");
        if(t.length === 2) {
            r_modifiers.push({"uuid":t[0],"min":parseInt(t[1])});
        }
    });

    //Convert selected modifiers to array
    form.forEach(function (element) {
        var modifier = {
            'uuid': element.value,
            'qty': parseInt(jQuery("#mooModifierInputQty-for-"+element.value).val() )
        };
        if (isNaN(modifier.qty) || modifier.qty < 1 )
            modifier.qty = 1;


        if(selected_modifiers[element.name] == null)
            selected_modifiers[element.name]=[modifier];
        else
            selected_modifiers[element.name].push(modifier);

    });
    //Check the required modifiers
    if( r_modifiers.length > 0 )
    {
        var count = r_modifiers.length;
        jQuery(r_modifiers).each(function (index,modifierGroup) {

            var nb_checked_boxes = 0;
            jQuery(selected_modifiers[modifierGroup.uuid]).each(function (index,element) {
                nb_checked_boxes +=  (element.qty>1)?element.qty:1;
            });

            if((typeof selected_modifiers[modifierGroup.uuid] == 'undefined' || selected_modifiers[modifierGroup.uuid] == null) || nb_checked_boxes < modifierGroup.min)
            {
                swal("You did not select all of the required options","Please check again","error");

                //Required modifier not selected or the min required not attain
                //hide all modifers then display the midiifer group that must be selected
                var element = jQuery(".mooModifiers-wrapper-for-"+modifierGroup.uuid).parent();
                jQuery(".mooModifiers-title",element).css("color","red");
                // add attribue for all modifers
                jQuery(".mooModifiers-title").attr("aria-expanded","false");
                var wrappers = jQuery("div[class*='mooModifiers-wrapper-for-']");
                wrappers.each(function (i,wrapper) {
                        jQuery(wrapper).slideUp();
                        jQuery(".mooModifiers-title>span:first-child",jQuery(wrapper).parent()).text('+');
                });
                var wrapper = jQuery(".mooModifiers-wrapper-for-"+modifierGroup.uuid);
                wrapper.slideDown();
                jQuery(".mooModifiers-title>span:first-child",wrapper.parent()).text('-');
                jQuery(".mooModifiers-title",element).attr("aria-expanded","false");
                return false;
            }
            if (!--count) addToCartAnItemWithModifiers(selected_modifiers,item_uuid,item_qty);
        });
    }
    else
    {
        addToCartAnItemWithModifiers(selected_modifiers,item_uuid,item_qty)
    }

}
function addToCartAnItemWithModifiers(selected_modifiers,item_uuid,item_qty)
{
    var count = Object.keys(selected_modifiers).length;

    swal({
        html:
        '<div class="moo-msgPopup">Adding the items to your cart</div>' +
        '<img src="'+ moo_params['plugin_img']+'/loading.gif" class="moo-imgPopup"/>',
        showConfirmButton: false
    });
    console.log(window.moo_theme_setings);
    var final_modifiers = [];
    if(count>0)
    {
        for(var modifierGroup in selected_modifiers)
        {
            jQuery(selected_modifiers[modifierGroup]).each(function (i,val) {
                final_modifiers.push(val);
            });

            if (!--count)
            {
                //Add item to cart
                var body = {
                    item_uuid:item_uuid,
                    item_qty:item_qty,
                    item_modifiers:final_modifiers
                };
                /* Add to cart the item */
                jQuery.post(moo_RestUrl+"moo-clover/v1/cart", body,function (data) {
                    if(data != null) {
                        if(data.status == "error") {
                            swal({
                                title:data.message,
                                type:"error"
                            });
                        } else {
                            if (typeof mooUpdateCart === "function") {
                                mooUpdateCart();
                            }

                            if (typeof mooShowAddingItemResult === "function") {
                                setTimeout(function () {
                                    mooShowAddingItemResult(data);
                                },2000)
                            } else {
                                swal({
                                    title:(data.name!=null)?data.name:'Items',
                                    text:"Added to cart",
                                    timer:3000,
                                    type:"success"
                                });
                            }
                            if(jQuery.magnificPopup.instance) {
                                jQuery.magnificPopup.close();
                            }
                        }

                    } else {
                        swal({
                            title:"Items not added",
                            type:"error"
                        });
                    }
                }).fail(function ( data ) {
                    swal({
                        title:"Items not added",
                        text:'Please verify your internet connection or contact us',
                        type:"error"
                    });
                }).done(function ( data ) {
                    if(typeof data.nb_items != "undefined" && typeof jQuery("#moo-cartNbItems") != "undefined")
                        jQuery("#moo-cartNbItems").text(data.nb_items)
                });
            }
        }
    }
    else
    {
        var body = {
            item_uuid:item_uuid,
            item_qty:item_qty,
            item_modifiers:{}
        };
        /* Add to cart the item */
        jQuery.post(moo_RestUrl+"moo-clover/v1/cart", body,function (data) {
            if(data != null)
            {
                if (typeof mooUpdateCart === "function") {
                    mooUpdateCart();
                }

                if (typeof mooShowAddingItemResult === "function") {
                    setTimeout(function () {
                        mooShowAddingItemResult(data);
                    },2000)
                } else {
                    swal({
                        title:(data.name!=null)?data.name:'Items',
                        text:"Added to cart",
                        timer:3000,
                        type:"success"
                    });
                }

                if(jQuery.magnificPopup.instance) {
                    jQuery.magnificPopup.close();
                }
            }
            else
            {
                swal({
                    title:"Item not added",
                    type:"error"
                });
            }
        }).fail(function ( data ) {
            swal({
                title:"Item not added",
                text:'Please verify your internet connection or contact us',
                type:"error"
            });
        }).done(function ( data ) {
            if(typeof data.nb_items != "undefined" && typeof jQuery("#moo-cartNbItems") != "undefined")
                jQuery("#moo-cartNbItems").text(data.nb_items)
        });;
    }
}

function removeModifiersList(e,item_id)
{
    e.preventDefault();
    e.stopPropagation();
    if(jQuery.magnificPopup.instance) {
        jQuery.magnificPopup.close();
    }

    if(typeof jQuery(".moo-modifiersContainer-for-"+item_id) === "undefined")
        return false;
    jQuery(".moo-modifiersContainer-for-"+item_id).html('')
}