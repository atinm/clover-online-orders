jQuery(document).ready(function($){
    window.addEventListener("message", handlePopupResult, false);
    window.moo_loading = '<svg xmlns="http://www.w3.org/2000/svg" width="44px" height="44px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-default"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(0 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(30 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.08333333333333333s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(60 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.16666666666666666s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(90 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.25s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(120 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.3333333333333333s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(150 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.4166666666666667s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(180 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.5s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(210 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.5833333333333334s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(240 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.6666666666666666s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(270 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.75s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(300 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.8333333333333334s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(330 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.9166666666666666s" repeatCount="indefinite"></animate></rect></svg>';
    window.moo_first_time = true;
    window.moo_nb_allItems =0;
    window.customHoursForCategoriesUpdated = false;
    window.customHoursForOrderTypesUpdated = false;


    $('.moo-edit-description-button').magnificPopup({
        type:'inline',
        closeBtnInside: true,
        midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
    });

    $('.moo-open-popupItems').magnificPopup({
        type:'inline',
        closeBtnInside: true,
        midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
    });
    $("#show_menu").click(function(e) {
        e.preventDefault();
        $("#MooPanel_main>#menu_for_mobile>ul").toggle();
    });
    // check if we ae on settings page
    if(document.getElementById("MooPanel") !== null) {
        moo_Update_stats();
        Moo_GetOrderTypes();
        Moo_SetupCategoriesSection();

        $("div.faq_question").click(function() {
            var clicked = $(this);
            // Get next element to current element
            clicked = clicked.next();
            // Show or hide the next element
            clicked.toggle();
        });

        //update height of panel

        var sideBarHeight = jQuery("#MooPanel_sidebar").height();
        if(sideBarHeight>658){
            jQuery("#MooPanel_main").css("min-height",(sideBarHeight));
        }


        if($('#moo_progressbar_container').length == 1)
            window.bar = new ProgressBar.Line('#moo_progressbar_container', {
                strokeWidth: 4,
                easing: 'easeInOut',
                duration: 1400,
                color: '#496F4E',
                trailColor: '#eee',
                trailWidth: 1,
                svgStyle: {width: '100%', height: '100%'},
                text: {
                    style: {
                        // Text color.
                        // Default: same as stroke color (options.color)
                        color: '#999',
                        position: 'absolute',
                        right: '0',
                        top: '30px',
                        padding: 0,
                        margin: 0,
                        transform: null
                    },
                    autoStyleContainer: false
                },
                from: {color: '#FFEA82'},
                to: {color: '#ED6A5A'}
            });

        $('div#MooPanel_tabContent4 input#MooDefaultStyle').each(function (i,val) {
            var v = $(val).attr('value');
            if($(val).prop('checked'))
            {
                $('div#MooPanel_tabContent4 input[value='+v+']').next().css('border', '2px solid rgb(34, 255, 79)');
                $('#mooInterface-'+v).show();
            }
            else
            {
                $('div#MooPanel_tabContent4 input[value='+v+']').next().css('border', '1px solid #999');
                $('#mooInterface-'+v).hide();
            }
        });

        $('div#MooPanel_tabContent4 input#MooDefaultStyle').click(function(event) {
            var elm = $(event.target);
            var value = elm.attr('value');
            $('div#MooPanel_tabContent4 input#MooDefaultStyle').each(function (i,val) {
                var v = $(val).attr('value');
                if(v == value)
                {
                    $('div#MooPanel_tabContent4 input[value='+value+']').next().css('border', '2px solid rgb(34, 255, 79)');
                    $('#mooInterface-'+v).show();
                }
                else
                {
                    $('div#MooPanel_tabContent4 input[value='+v+']').next().css('border', '1px solid #999');
                    $('#mooInterface-'+v).hide();
                }
            })
        });





        $('#orderCategory input').bind('click.sortable mousedown.sortable',function(ev){
            ev.target.focus();
        });
        name_cat = "";
        $(".table_category").on("click",".edit_name",function(event) {
            event.preventDefault();
            var idCat   = $(this).parent().parent().find("td:eq(0)").text();
            var idCatStr   = '"'+idCat+'"';
            var nameCat = $(this).parent().parent().find("td:eq(2)").text();
            var id_name = $(this).parent().parent().find("td:eq(2)").attr("id");
            name_cat = '"'+nameCat+'"';
            var c_html = "";
            c_html +="<div class='input-change-name'>";
            c_html += "<div class='input-name'>";
            c_html += "<input type='text' value='"+nameCat+"' id='newName"+idCat+"' class='newname'>";
            c_html += "</div>";
            // c_html += "<div class='button-name'>";
            c_html += "<div class='bt-valider'>";
            c_html +='<a href="#" class="vald-change-name" onclick=\'vald_change_name(event,'+idCatStr+',"D")\'>';
            c_html +='<span id="moo_valide_change1" data-ot="valider_change_name" data-ot-target="#moo_valide_change1">';
            c_html +="<img src='"+moo_params.plugin_url+"/public/img/valider.png' alt='Validate change'>";
            c_html +="</span>";
            c_html +="</a>";
            c_html += "</div>";
            c_html += "<div class='bt-annuler'>";
            c_html +='<a href="#" class="annuler-change-name" onclick=\'annuler_change_name(event,'+idCatStr+',"D",'+name_cat+')\'>';
            c_html += "<img src='"+moo_params.plugin_url+"/public/img/annuler.png' alt='Annuler change'>";
            c_html +="</a>";
            c_html += "</div>";
            //c_html += "</div>";
            c_html += "</div>";
            $("#"+id_name).html(c_html);
        });
        /* --- Modifier Group --- */

        $('.moo_ModifierGroup input').bind('click.sortable mousedown.sortable',function(ev){
            ev.target.focus();
        });
        $('.sub-group input').bind('click.sortable mousedown.sortable',function(ev){
            ev.target.focus();
        });
    }

   /* Remove loader*/
    $('body').addClass('loaded');
    //Force removing loader after 10 seconds
    setTimeout(function(){ $('body').addClass('loaded') }, 10000);

    $('.moo-color-field').wpColorPicker();

    if ( window.location.hash != "" ) {
        switch (window.location.hash) {
            case "#apikey":
                tab_clicked(1);
                break;
            case "#inventory":
                tab_clicked(2);
                break;
            case "#ordertypes":
                tab_clicked(3);
                break;
            case "#categories":
                tab_clicked(5);
                break;
            case "#modifiergroups":
                tab_clicked(6);
                break;
            case "#checkout":
                tab_clicked(7);
                break;
            case "#store":
                tab_clicked(8);
                break;
            case "#delivery":
                tab_clicked(9);
                break;
            case "#help":
                tab_clicked(10);
                break;
            case "#faq":
                tab_clicked(11);
                break;
            case "#custom-hours":
                tab_clicked(12);
                break;
        }
    }
});

jQuery(document).ready(function() {

    //Drag and drop
    jQuery("#sortable").sortable({
        stop: function(event, ui) {
            var tabNew = new Array();
            var i = 0;

            jQuery("#sortable tr").each(function(i, el){
                tabNew[i] = jQuery(this).attr("data-cat-id");
                i++;
            });
            jQuery.post(moo_params.ajaxurl,{'action':'moo_new_order_categories','newtable':tabNew},function(data){
                //console.log(data);
            })
        }
    });

    jQuery("#orderCategory").sortable({
        stop: function(event, ui) {
            var tabNew = new Array();
            var i = 0;
            jQuery("#orderCategory .category-item").each(function (i, el) {
                tabNew[i] = jQuery(this).attr("cat-id-mobil");
                i++;
            });
            jQuery.post(moo_params.ajaxurl,{'action':'moo_new_order_categories','newtable':tabNew},function(data){
                //console.log(data);
            })
        }
    });

    jQuery(".moo_listItem").sortable({
        stop: function(event, ui) {
            var category = jQuery(this).attr("id-cat");
            var tabNew = new Array();
            var i = 0;
            jQuery(".moo_listItem li.cat"+category).each(function(i, el){
                tabNew[i] = jQuery(this).attr("uuid_item");
                i++;
            });
            jQuery.post(moo_params.ajaxurl,{'action':'moo_reorder_items','newtable':tabNew},function(data){
                console.log(data);
            });
        }
    });


    jQuery(".sub-group").sortable({
        stop: function(event, ui) {
            var group = jQuery(this).attr("GM");
            var tabNew = new Array();
            var i = 0;
            jQuery(".moo_ModifierGroup .list-GModifier_"+group).each(function (i, el) {
                tabNew[i] = jQuery(this).attr("group-id");
                i++;
            });
            //var NB = tabNew.length;
            jQuery.post(moo_params.ajaxurl,{'action':'moo_new_order_modifier','group_id':group,'newtable':tabNew},function(data){
                console.log(data);
            })
        }
    });

    jQuery(".moo_ModifierGroup").sortable({
        stop: function(event, ui) {
            var tabNew = new Array();
            var i = 0;
            jQuery(".moo_ModifierGroup .list-group").each(function (i, el) {
                tabNew[i] = jQuery(this).attr("group-id");
                i++;
            });
            jQuery.post(moo_params.ajaxurl,{'action':'moo_new_order_group_modifier','newtable':tabNew},function(data){
                console.log(data);
            })
        }
    });

    jQuery('.moo-color-field').wpColorPicker();

    jQuery('#CouponExpiryDate').datepicker({
        dateFormat: 'mm-dd-yy'
    });

    jQuery('#CouponStartDate').datepicker({
        dateFormat: 'mm-dd-yy'
    });

});
/* --- Modifier Group --- */
function edit_name_GGroup(event,id){
    event.preventDefault();
    jQuery("#label_"+id+" .getname").css("display","none");
    jQuery("#label_"+id+" .change-name").css("display","block");
}
function validerChangeNameGG(event,id){
    event.preventDefault();
    var newName = jQuery("#newName_"+id).val();
    //
    jQuery("#label_"+id+" .getname").css("display","block");
    jQuery("#label_"+id+" .change-name").css("display","none");
    jQuery("#label_"+id+" .getname").text(newName);
    jQuery.post(moo_params.ajaxurl,{'action':'moo_change_modifiergroup_name',"mg_uuid":id,"mg_name":newName}, function (data) {
        //console.log(data);
        }
    );
}
function annulerChangeNameGG(event,id,name) {
    event.preventDefault();
    var name = jQuery("#label_"+id+" .getname").text();
    jQuery("#label_"+id+" .getname").css("display","block");
    jQuery("#label_"+id+" .change-name").css("display","none");
    jQuery("#label_"+id+" .change-name input").val(name);
}

function show_sub(event,id){
    event.preventDefault();
    jQuery('#detail_group_'+id).slideToggle('fast', function() {
        if (jQuery(this).is(':visible')) {
            jQuery("#plus_"+id).attr('src',moo_params.plugin_url+'/public/img/substract.png');
        } else {
            jQuery("#plus_"+id).attr('src',moo_params.plugin_url+'/public/img/add.png');
        }
    });
    //jQuery('#detail_group_'+id).slideToggle();
}
function edit_name_GModifer(event,id){
    event.preventDefault();
    jQuery("#label_"+id+" .getname").css("display","none");
    jQuery("#label_"+id+" .change-name-modifier").css("display","block");
}
function validerChangeNameModifier(event,id){
    event.preventDefault();
    var newName = jQuery("#newName_"+id).val();
    //
    jQuery("#label_"+id+" .getname").css("display","block");
    jQuery("#label_"+id+" .change-name-modifier").css("display","none");
    jQuery("#label_"+id+" .getname").text(newName);
    jQuery.post(moo_params.ajaxurl,{'action':'moo_change_modifier_name',"m_uuid":id,"m_name":newName}, function (data) {
            //console.log(data);
        }
    );
}
function annulerChangeNameModifier(event,id,name){
    event.preventDefault();
    var name = jQuery("#label_"+id+" .getname").text();
    jQuery("#label_"+id+" .getname").css("display","block");
    jQuery("#label_"+id+" .change-name-modifier").css("display","none");
    jQuery("#label_"+id+" .change-name-modifier input").val(name);
}
/* --- Modifier Group --- */
function vald_change_name(event,uuid,v) {
    event.preventDefault();
    var name = jQuery("#name_"+uuid).val();
    var newname = jQuery("#newName"+uuid).val();
    if ( v=="D" ){jQuery("td#name_"+uuid).html(newname);}
    else{
        jQuery("#newName"+uuid).prop('disabled', true);
        jQuery("#bt_MV_"+uuid).remove();
        jQuery("#bt_MA_"+uuid).remove();
    }
    jQuery.post(moo_params.ajaxurl,{'action':'moo_change_name_category','newName':newname,"id_cat":uuid}, function(response){
        if(response == 1){
           // console.log(response);
        }
        else{
            jQuery("td#name_"+uuid).html(name_cat);
        }
    });
}


function annuler_change_name(event,uuid,v,lastname){
    event.preventDefault();
    if (v == "D"){jQuery("td#name_"+uuid).html(name_cat);}
    else{
        jQuery("#newName"+uuid).val(lastname);
        jQuery("#newName"+uuid).prop('disabled', true);
        jQuery("#bt_MV_"+uuid).remove();
        jQuery("#bt_MA_"+uuid).remove();
    }
}

function MooChangeM_Status(uuid)
{
    var mg_status = jQuery('#myonoffswitch_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_modifier_status',"mg_uuid":uuid,"mg_status":mg_status}, function (data) {
            console.log(data);
        }
    );
}

function delete_img_category(event,uuid,responsive){
    event.preventDefault();
    var image = "";
    if(responsive == 'D'){
        tr_new(uuid,image);
    } else {
        img_row(uuid,image)
    }
    jQuery.post(moo_params.ajaxurl,{'action':'moo_delete_img_category',"uuid":uuid}, function(data){
     if (data == 1) {
         //console.log(data);
     }
     });
}

function img_row(uuid,img){
    var html="<label>Operation</label>";
    html +='<div class="bt bt-upload">';
    html +='<a href="#" onclick="uploader_image_category(event,&quot;'+uuid+'&quot;,&quot;M&quot;)">';
    html +="<img src='"+moo_params.plugin_url+"public/img/upload.png' style='width: 20px;'>";
    html +='</a>';
    html +='</div>';
    html +='<div class="bt bt-edit">';
    html +='<a href="#" onclick="edit_name_mobil(event,&quot;'+uuid+'&quot;)">';
    html +="<img src='"+moo_params.plugin_url+"public/img/edit.png' style='width: 20px;'>";
    html +='</a>';
    html +='</div>';
    if(img == ""){
        jQuery("#id_img_M_"+uuid).html("<label>Pecture</label><img src='"+moo_params.plugin_url+"/public/img/no-image.png' style='width: 50px;'>")
        jQuery("#id_bt_M"+uuid).html(html);
    }
    else{
        html +='<div class="bt bt-delete">';
        html +='<a href="#" onclick="delete_img_category(event,&quot;'+uuid+'&quot;,&quot;M&quot;)">';
        html +="<img src='"+moo_params.plugin_url+"public/img/delete.png' style='width: 20px;'>";
        html +='</a>';
        html +='</div>';
        jQuery("#id_img_M_"+uuid).html("<label>Pecture</label><img src='"+img+"' style='width: 50px;'>");
        jQuery("#id_bt_M"+uuid).html(html);
    }
}
function edit_name_mobil(event,uuid){
    event.preventDefault();
    var name = jQuery("#newName"+uuid).val();
    var htmlC = "";
    htmlC +='<a href="#" class="vald-change-name" onclick="vald_change_name(event,&quot;'+uuid+'&quot;,&quot;M&quot;)" id="bt_MV_'+uuid+'">';
    htmlC +="<img src='"+moo_params.plugin_url+"/public/img/valider.png'  alt='Validate change' style='width: 18px;'>";
    htmlC +="</a>";
    htmlC +='<a href="#" class="annuler-change-name" onclick="annuler_change_name(event,&quot;'+uuid+'&quot;,&quot;M&quot;,&quot;'+name+'&quot;)" id="bt_MA_'+uuid+'">';
    htmlC +="<img src='"+moo_params.plugin_url+"/public/img/annuler.png' alt='annuler change' style='width: 18px;margin-left: 2px;'>";
    htmlC +="</a>";
    jQuery("#bt_MV_"+uuid).remove();
    jQuery("#bt_MA_"+uuid).remove();
    jQuery("#newName"+uuid).prop('disabled', false);
    jQuery("#name_cat_Mobil"+uuid).append(htmlC);
}

// add parametre ,image,name,visibility
function tr_new(uuid,img){
    var nameCat = jQuery("td#name_"+uuid).text();
    var visib_cat = jQuery(".visib_cat"+uuid).is(":checked")? true : false;
    var input_check = "";
    if (visib_cat == true){
        input_check = '<input type="checkbox" name="onoffswitch[]" id="myonoffswitch_Visibility_'+uuid+'" class="moo-onoffswitch-checkbox visib_cat'+uuid+'" onclick="visibility_cat(&quot;'+uuid+'&quot;)" checked>';
    }
    else{
        input_check = '<input type="checkbox" name="onoffswitch[]" id="myonoffswitch_Visibility_'+uuid+'" class="moo-onoffswitch-checkbox visib_cat'+uuid+'" onclick="visibility_cat(&quot;'+uuid+'&quot;)">';
    }
    var cont_html = "";
    cont_html +="<td style='display:none;'><span id='id_cat'>"+uuid+"</span></td>";
    cont_html +="<td class='img-cat'' style='width: 50px;' id='"+uuid+"'>";
    if(img == ""){
        cont_html +="<img src='"+moo_params.plugin_url+"/public/img/no-image.png' style='width: 50px;'>";
    }
    else {
        cont_html +="<img src='"+img+"' style='width: 50px;'>";
    }
    cont_html +="</td>";
    cont_html +="<td class='name_cat' id='name_"+uuid+"'>";
    cont_html +=nameCat;
    cont_html +="</td>";
    cont_html +="<td class='show-cat'>";
    cont_html +='<div class="moo-onoffswitch" title="Visibility Category">';
    cont_html +=input_check;
    cont_html +='<label class="moo-onoffswitch-label" for="myonoffswitch_Visibility_'+uuid+'"><span class="moo-onoffswitch-inner"></span>';
    cont_html +='<span class="moo-onoffswitch-switch"></span>';
    cont_html +='</label>';
    cont_html +="</div>";
    cont_html +="</td>";
    if (img == ""){
        cont_html +="<td class='bt-cat'>";
        cont_html +='<a href="#" onclick="uploader_image_category(event,&quot;'+uuid+'&quot;,&quot;D&quot;)" title="Uploader Image">';
        cont_html +="<img src='"+moo_params.plugin_url+"/public/img/upload.png'>";
        cont_html +="</a>";
        cont_html +="</td>";
        cont_html +="<td colspan='2' class='bt-cat'>";
    cont_html +="<a href='#' class='edit_name' title='Edite Name'>";
    cont_html +="<img src='"+moo_params.plugin_url+"/public/img/edit.png' alt='Edite Name' >";
    cont_html +="</a>";
    cont_html +="</td>";
    }
    else{
        cont_html +="<td class='bt-cat'>";
        cont_html +='<a href="#" onclick="uploader_image_category(event,&quot;'+uuid+'&quot;,&quot;D&quot;)" title="Change Image">';
        cont_html +="<img src='"+moo_params.plugin_url+"/public/img/upload.png'>";
        cont_html +="</a>";
        cont_html +="</td>";
        cont_html +="<td class='bt-cat'>";
        cont_html +="<a href='#' class='edit_name' title='Edite Name'>";
        cont_html +="<img src='"+moo_params.plugin_url+"/public/img/edit.png'>";
        cont_html +="</a>";
        cont_html +="</td>";
        cont_html +="<td class='bt-cat'>";
        cont_html +='<a href="#" onclick="delete_img_category(event,&quot;'+uuid+'&quot;,&quot;D&quot;)" title="Delete Image">'
        cont_html +="<img src='"+moo_params.plugin_url+"/public/img/delete.png'>";
        cont_html +="</a>";
        cont_html +="</td>";
    }
    cont_html +="<td class='items_cat'>";
    cont_html +="<a href='#detailCat"+uuid+"' class='moo-open-popupItems'>";
    cont_html +="<img src='"+moo_params.plugin_url+"/public/img/plusIT.png' style='width: 20px;'>";
    cont_html +="</a>";
    cont_html +="</td>";
    jQuery("tr#row_id_"+uuid).html(cont_html);
    jQuery('.moo-open-popupItems').magnificPopup({
        type:'inline',
        midClick: true // Allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source in href.
    });
    jQuery("tr#row_id_"+uuid).html(cont_html);
}
function visibility_cat(uuid) {
    var check = jQuery(".visib_cat"+uuid).is(":checked")? true : false;
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_visiblite_category','visiblite':check,"id_cat":uuid}, function(response){
        //console.log(response);
    });
}
function visibility_cat_mobile(uuid) {
    var check = jQuery("#visib"+uuid).is(":checked")? true : false;
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_visiblite_category','visiblite':check,"id_cat":uuid}, function(response){
        //console.log(response);
    });
}
function tab_clicked(tab)
{
    var Nb_Tabs=12; // Number for tabs
    for(var i=1;i<=Nb_Tabs;i++) {
        jQuery('#MooPanel_tabContent'+i).hide();
        jQuery('#MooPanel_tab'+i).removeClass("MooPanel_Selected");
    }
    jQuery('#MooPanel_tabContent'+tab).show();
    jQuery('#MooPanel_tab'+tab).addClass("MooPanel_Selected");

    if(tab === 9 &&  window.moo_first_time === true)
    {
        moo_getLatLongforMapDa();
        window.moo_first_time = false;
        moo_setup_existing_zones();
    }
    //refresh when clicking on categories
    if(tab === 5){
        if(window.customHoursForCategoriesUpdated)  {
            Moo_CustomHoursForCategories();
        }
    }
    //refresh when clicking on ordertypes
    if(tab === 3){
        if( window.customHoursForOrderTypesUpdated ) {
            Moo_RefreshOrderTypesSection();
        }
    }
    if(tab === 12){
        window.customHoursForCategoriesUpdated = true;
        window.customHoursForOrderTypesUpdated = true;

       // var iframe = document.getElementById('mooFrameCustomHours');
       // iframe.src = iframe.src;

    }
    jQuery("#menu_for_mobile ul").toggle();

}
function MooPanel_ImportItems(event)
{
    event.preventDefault();
    jQuery('#MooPanelSectionImport').html(window.moo_loading);
    jQuery('#MooPanelSectionImportItems').html('');
    Moo_ImportCategories();
}
function MooPanel_RefreshPage(event)
{
    event.preventDefault();
    window.location.reload();
}
var flag_key_noy_found = false;

function Moo_ImportCategories() {
    jQuery.post(moo_params.ajaxurl,{'action':'moo_import_categories'}, function (data) {
            if(data.status === 'Success')
            {
                if(data.data === "Please verify your Key in page settings") {
                    flag_key_noy_found = true;
                    jQuery('#MooPanelSectionImport').html('Please verify your API Key<br/> ');
                }
                else
                    jQuery('#MooPanelSectionImport').append('<br/> '+data.data);
            }
            else
                jQuery('#MooPanelSectionImport').append('<br/> '+"Error when importing the categories, please try again");
        }
    ).done(function () {
            Moo_ImportLabels();
    });
}

function Moo_ImportLabels()
{
    if(!flag_key_noy_found) {
        jQuery.post(moo_params.ajaxurl,{'action':'moo_import_labels'}, function (data) {
                if(data.status === 'Success')
                    jQuery('#MooPanelSectionImport').append('<br/> '+data.data);
                else
                    jQuery('#MooPanelSectionImport').append('<br/> '+"Error when importing the label, please try again");
            }
        ).done(function () {
            Moo_ImportTaxes();
        });
    }

}
function Moo_ImportTaxes()
{
    jQuery.post(moo_params.ajaxurl,{'action':'moo_import_taxes'}, function (data) {
            if(data.status ==='Success')
                jQuery('#MooPanelSectionImport').append('<br/> '+data.data);
            else
                jQuery('#MooPanelSectionImport').append('<br/> '+"Error when importing the taxes rates, please try again");
        }
    ).done(function () {
            Moo_ImportItems();
        });
}
function Moo_ImportItems() {
    Moo_ImportItemsV2(0);
}
function Moo_ImportItemsV2(page) {
    var received = 0;
    var productsNb = 0;
    jQuery.post(moo_params.ajaxurl,{'action':'moo_import_items_v2','page':page}, function (data) {
            received = data.received;
            productsNb = data.currentNb;
        }
    ).done(function () {
        if(received > 0) {
            jQuery('#MooPanelSectionImportItems').html(productsNb + " Items imported"+'<br/> ');
            Moo_ImportItemsV2(page+1);
        } else {
            jQuery('#MooPanelSectionImport').html("All of your data was successfully imported from Your Clover POS. Please click refresh button below or re-upload this page"+'<br/> ');
            jQuery('#MooPanelSectionImportItems').html('');
            jQuery('#MooPanelButtonImport').html('<a href="#" onclick="MooPanel_RefreshPage(event)" class="button button-secondary" style="margin-bottom: 35px;" >Refresh</a>');
            moo_Update_stats();
            Moo_GetOrderTypes();
            Moo_SetupCategoriesSection();
        }
    });
}

function Moo_GetOrderTypes(uuid = null){
    if( document.querySelector('#MooOrderTypesContent') != null )
    {
        document.querySelector('#MooOrderTypesContent').innerHTML  ="<div style='text-align: center'>Loading your ordertypes, please wait...</div>";
        jQuery.post(moo_params.ajaxurl,{'action':'moo_getAllOrderTypes'}, function (data) {
            if(data.status == 'success') {
                var orderTypes = {};
                try {
                    orderTypes = JSON.parse(data.data);
                } catch (e) {
                    console.log("Parsing error: orderTypes");
                }
                var html='<p>Drag and drop to rearrange</p><ul style="margin-bottom: 10px">';
                if(orderTypes.length>0) {
                    for(var i=0;i<orderTypes.length;i++) {
                        var $ot = orderTypes[i];
                        if($ot.label == "") continue;
                        if ($ot.status==1){
                            html += '<li class="moo_orderType enabled" ot_uuid="'+$ot.ot_uuid+'">';
                        }
                        else{
                            html += '<li class="moo_orderType disabled" ot_uuid="'+$ot.ot_uuid+'">';
                        }

                        html +='<div class="moo_item_order">';
                        html +='<span style="float: left">';
                        html += $ot.label ;
                        html +='</span>';
                        if ($ot.status==1){
                            html += '<span style="margin-left: 10px"><img src="'+moo_params.plugin_url+"/public/img/bullet_ball_green.png"+'"/></span>';
                        }
                        else{
                            html += '<span style="margin-left: 10px"><img src="'+moo_params.plugin_url+"/public/img/bullet_ball_glass_grey.png"+'"/></span>';
                        }
                        html +='<span style="float: right;font-size: 12px;" id="top-bt-'+$ot.ot_uuid+'">';
                        html += '<a href="#" onclick="moo_showOrderTypeDetails(event,&quot;'+$ot.ot_uuid+'&quot;)">Edit</a> | <a href="#" title="Delete this order types from the wordpress Database" onclick="Moo_deleteOrderType(event,&quot;'+$ot.ot_uuid+'&quot;)">DELETE</a>';
                        html +='</span>';
                        html += '</div>';
                        if(uuid && uuid===$ot.ot_uuid) {
                            html +='<div class="Detail_OrderType" id="detail_'+$ot.ot_uuid+'" style="display: block">';
                        } else {
                            html +='<div class="Detail_OrderType" id="detail_'+$ot.ot_uuid+'">';
                        }

                        html +='<div class="champ_order name_order"><span class="label_Torder">Order Type Name </span><input type="text" id="label_'+$ot.ot_uuid+'" value="'+$ot.label+'" style="width: 160px;"></div>';
                        //enable/disable
                        html +='<div class="champ_order IsEnabled_order"><span class="label_Torder">Disable / Enabled </span>';

                        html +='<div class="moo-onoffswitch" title="Enable or disable this order type">';

                        if ($ot.status==1){
                            html += '<input type="checkbox" name="onoffswitch[]" id="select_En_'+$ot.ot_uuid+'" class="moo-onoffswitch-checkbox" checked>';
                        }
                        else{
                            html += '<input type="checkbox" name="onoffswitch[]" id="select_En_'+$ot.ot_uuid+'" class="moo-onoffswitch-checkbox" >';
                        }
                        html +='<label class="moo-onoffswitch-label" for="select_En_'+$ot.ot_uuid+'"><span class="moo-onoffswitch-inner"></span>';
                        html +='<span class="moo-onoffswitch-switch"></span>';
                        html +='</label>';
                        html +="</div>";
                        html += '</div>';
                        //Min amount
                        html +='<div class="champ_order Taxable_order"><span class="label_Torder">Minimum Amount </span>';
                        html +='<input type="text" id="minAmount_'+$ot.ot_uuid+'" value="'+$ot.minAmount+'" style="width: 160px;">';
                        html += '</div>';
                        //show Taxable
                        html +='<div class="champ_order Taxable_order"><span class="label_Torder">Taxable </span>';
                        html +='<select style="width: 160px;" id="select_Tax_'+$ot.ot_uuid+'">';
                        html +='<option value="1" '+(($ot.taxable==1)?"selected":"")+'>Yes</option>';
                        html +='<option value="0" '+(($ot.taxable==0)?"selected":"")+'>No</option>';
                        html +='</select>';
                        html += '</div>';

                        //Delivery Order
                        html +='<div class="champ_order type_order"><span class="label_Torder">Delivery Order </span>';
                        html +='<select style="width: 160px;" id="select_type_'+$ot.ot_uuid+'">';
                        html +='<option value="1" '+(($ot.show_sa==1)?"selected":"")+'>Yes</option>';
                        html +='<option value="0" '+(($ot.show_sa==0)?"selected":"")+'>No</option>';
                        html +='</select>';
                        html +='<br/><small>Selecting Yes Will require customers to provide their delivery address</small>';
                        html += '</div>';
                        //Use Coupons
                        html +='<div class="champ_order IsEnabled_order"><span class="label_Torder">Allow Coupons </span>';
                        html +='<div class="moo-onoffswitch" title="Enable or disable this order type">';

                        if ($ot.use_coupons==1){
                            html += '<input type="checkbox" name="onoffswitch[]" id="useCoupons_'+$ot.ot_uuid+'" class="moo-onoffswitch-checkbox" checked>';
                        }
                        else{
                            html += '<input type="checkbox" name="onoffswitch[]" id="useCoupons_'+$ot.ot_uuid+'" class="moo-onoffswitch-checkbox" >';
                        }
                        html +='<label class="moo-onoffswitch-label" for="useCoupons_'+$ot.ot_uuid+'"><span class="moo-onoffswitch-inner"></span>';
                        html +='<span class="moo-onoffswitch-switch"></span>';
                        html +='</label>';
                        html +="</div>";
                        html += '</div>';
                        //Limit Availability time
                        html +='<div class="champ_order type_order"><span class="label_Torder">Ordering Hours</span>';
                        html +='<select name="" id="availabilityCustomTime_'+$ot.ot_uuid+'" onchange="mooSelecetdTimeChanged(event,\'ordertypes\',\''+$ot.ot_uuid+'\')">' +
                            '<option value="">Default Clover Business Hours</option>'+
                            '<optgroup label="Choose Order Type Hours">' ;

                        //Add the Custom Hours to the select
                        if(moo_custom_hours_for_ot){
                            Object.keys(moo_custom_hours_for_ot).forEach(function (key) {
                                html +=  '<option value="'+key+'"' ;
                                if($ot.custom_hours!== null && $ot.custom_hours === key) {
                                    html += 'selected' ;
                                }
                                html +=   '>'+moo_custom_hours_for_ot[key]+'</option>';
                            });
                        }


                        html +=  '</optgroup>';
                        html +=  '</select>' ;
                        html +='</div>';
                        //Custom message
                        html +='<div class="champ_order IsEnabled_order">Custom message to display when ordering method is not available (maximum 200 characters)';
                        html +='<div><textarea cols="100" rows="5" id="moo_ot_customMessage_'+$ot.ot_uuid+'">'+$ot.custom_message+'</textarea></div>';
                        html += '</div>';
                        //Buttons
                        html +='<div class="bt_update_order" style="float: right;">';
                        html +='<a class="button" style="min-width: 70px !important;margin-right: 5px;" onclick="moo_saveOrderType(event,&quot;'+$ot.ot_uuid+'&quot;)">Save</a>';
                        html +='<a class="button" style="min-width: 70px !important;" onclick="Moo_GetOrderTypes()">Cancel</a>';
                        html += '</div>';
                        html += '</div>';
                        html += '</li>';
                    }
                } else {
                    html = "<div class='normal_text' >You don't have any OrderTypes,<br/> please import your data by clicking on <b>Import / Sync Inventory</b></div>";
                }
                html += "</ul>";
                document.querySelector('#MooOrderTypesContent').innerHTML = html;
                window.customHoursForOrderTypesUpdated = false;
                swal.close();
                makeOrderTypesSortable();
            } else {
                document.querySelector('#MooOrderTypesContent').innerHTML  ="<div style='text-align: center'>Please verify your API Key<br/></div>";
            }

        });
    }
}

function makeOrderTypesSortable() {
    jQuery("#MooOrderTypesContent ul").sortable({
        stop: function(event, ui) {
            var tabNew = new Array();
            var i = 0;
            jQuery("#MooOrderTypesContent ul li").each(function(i, el){
                tabNew[i] = jQuery(this).attr("ot_uuid");
                i++;
            });
            jQuery.post(moo_params.ajaxurl,{'action':'moo_reorder_ordertypes','newtable':tabNew},function(data){
                if(data===false)
                    swal('Sort Order Not Changed, please contact us to fix that');
            })
        }
    });
}

function Moo_RefreshOrderTypesSection(){
    if( document.querySelector('#MooOrderTypesContent') != null )
    {
        document.querySelector('#MooOrderTypesContent').innerHTML  ="<div style='text-align: center'>Loading your ordertypes, please wait...</div>";
    }
    var endpoint = moo_RestUrl+'moo-clover/v2/dash/ordertypes_hours/?_wpnonce='+ moo_params.nonce;
    jQuery.get(endpoint, function (data) {
        if(data.status === "success"){
            moo_custom_hours_for_ot= data.data;
        } else {
            console.log(moo_custom_hours_for_ot);
        }
        Moo_GetOrderTypes();
    }).fail(function (response) {
        console.log(response);
        Moo_GetOrderTypes();
    });

}
function Moo_SetupCategoriesSection( uuid = null ){
    if( document.querySelector('.moo-categories-section') !== null )
    {
        jQuery(".moo-categories-section").show();
        jQuery("#moo-categories-edit-section").hide().html('');
        jQuery("#moo-btn-backtocategories").hide();
        jQuery("#moo-btn-reordercategories").show();
        var endpoint = moo_RestUrl+'moo-clover/v2/dash/categories?_wpnonce='+ moo_params.nonce;
        jQuery.get(endpoint, function (response) {
            if(response.status === 'success') {
                var categories = response.data;
                var html ='';
                if(categories.length>0) {
                    for(var i=0;i<categories.length;i++) {
                        var category = categories[i];
                        html += '<div class="moo-row moo-category-row" id="moo-category-row-'+category.uuid+'" onmouseenter="mooShowQuicklinks(this)" onmouseleave="mooHideQuicklinks(this)" >';
                        html += '<div class="moo-col-md-2">';
                        if(category.image_url === null){
                            html += '<div class="moo-category-image moo-cat-no-img">';
                            html +='<span class="moo-category-image-delete-btn" onclick="mooDeleteCategoryImage(event,\''+category.uuid+'\')"><img alt="remove the  image" src="'+ moo_params.plugin_url+"/public/img/close.png"  +'"/></span>';
                            html += '<img src="'+moo_params.plugin_url+"/public/img/noImg3.png"+'" class="moo-category-img-'+category.uuid+'"/>';
                        } else {
                            html += '<div class="moo-category-image">';
                            html +='<span class="moo-category-image-delete-btn" onclick="mooDeleteCategoryImage(event,\''+category.uuid+'\')"><img alt="remove the  image" src="'+ moo_params.plugin_url+"/public/img/close.png"  +'"/></span>';
                            html += '<img src="'+ category.image_url  +'" class="moo-category-img-'+category.uuid+'"/>';
                        }
                        html += '<div class="moo-category-image-PicSelector" onclick="mooUploadImageForCategory(event,\''+category.uuid+'\')">' +
                            '<div class="moo-category-image-label">' +
                            'Update'+
                            '</div>'+
                            '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '<div class="moo-col-md-8 moo-category-info">';

                        html += '<div class="moo-category-uuid">'+ category.uuid +'</div>';

                        if(category.alternate_name !== null && category.alternate_name !== "") {
                            html += '<div class="moo-category-title">'+ category.alternate_name +'</div>';
                        } else {
                            html += '<div class="moo-category-title">'+ category.name +'</div>';
                        }


                       // html += '<div class="moo-category-uuid">XLAIHZ354155</div>';
                       // html += '<div class="moo-category-title">Dozen bagels dunkin donuts</div>';

                        html += '<div class="moo-category-description">';
                        if( category.description !== null){
                            html += category.description;
                        } else {
                           // html += "Our Real Special marinara sauce with fresh tomatoes baked on our signature thin crust, baked to a perfect crisp. We create food we’re proud to serve and deliver it fast, with a smile.";
                            html += "";
                        }
                        html +='</div>';
                        //html += '<div class="moo-category-quicklinks"><a href="">Edit Name or Description</a></div>';
                        html += '<div class="moo-category-quicklinks"></div>';
                        html += '</div>';
                        html += '<div class="moo-col-md-2 moo-category-actions-container">';
                        html += '<div class="moo-category-actions">' +
                            '<div class="moo-onoffswitch">' +
                            '   <input type="checkbox" name="onoffswitch[]" id="myonoffswitch_Visibility_'+ category.uuid +'" class="moo-onoffswitch-checkbox visib_cat'+ category.uuid +'" onclick="visibility_cat(\''+ category.uuid +'\')" ';
                            if(category.show_by_default === "1") {
                                html += 'checked' ;
                            }
                            html +=  '>' +
                            '       <label class="moo-onoffswitch-label" for="myonoffswitch_Visibility_'+ category.uuid +'"><span class="moo-onoffswitch-inner"></span>' +
                            '       <span class="moo-onoffswitch-switch"></span>' +
                            '       </label>' +
                            '   </div>';
                        html += '<div class="moo-category-action-edit" onclick="Moo_SetupEditCategorySection(event,\''+ category.uuid +'\')">' +
                                '<img src="'+ moo_params.plugin_url +'/public/img/settings.png" />'+
                                '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    }
                } else{
                    html += "<div class='normal_text' >You don't have any category,<br/> please import your data by clicking on <b>Import Inventory</b></div>";
                }

                document.querySelector('#MooPanel_tabContent5 .moo-categories-section').innerHTML = html;
                //to refresh categories section only if custom hours clicked
                window.customHoursForCategoriesUpdated = false;

                if(uuid){
                    var cat_row_id = '#moo-category-row-'+uuid;
                    var box = document.querySelector('#MooPanel_main');
                    var targetElm = document.querySelector(cat_row_id);
                    if(targetElm){
                        scrollToElm( box, targetElm , 1 );
                    }
                }
                swal.close();
            } else {
                console.log('You do not have any categries');
                swal.close();
            }
        }).fail(function () {
            var html = "<div class='normal_text' >We cannot get your categories, Please verify your API Key and your wordpress settings, if you are using HTTPS and in your settings you have HTTP</div>";
            document.querySelector('#MooPanel_tabContent5 .moo-categories-section').innerHTML = html;
            swal.close();
        });
    }
}
function Moo_SetupReorderCategoriesSection(event){
    event.preventDefault();

    if( document.querySelector('.moo-categories-section') !== null )
    {
        jQuery(".moo-categories-section").hide();
        jQuery("#moo-categories-edit-section").show().html('');
        document.querySelector('#MooPanel_tabContent5 #moo-categories-edit-section').innerHTML = "Loading your categories...";
        var endpoint = moo_RestUrl+'moo-clover/v2/dash/categories?_wpnonce='+ moo_params.nonce;
        jQuery.get(endpoint, function (response) {
            if(response.status === 'success') {
                var categories = response.data;
                var html  = '<div class="moo-row moo-goback-row">';
                html += '<div class="moo-goback-icon" onclick="Moo_SetupCategoriesSection()"><img src="'+moo_params.plugin_url+'/public/img/back.png"/></div><div onclick="Moo_SetupCategoriesSection()" class="moo-goback-text">Back</div>';
                html += '</div>';
                if(categories.length>0) {
                    html += '<div class="moo-row moo-reorder-category-row">';
                    for(var i=0;i<categories.length;i++) {
                        var category = categories[i];
                        html += '<div class="moo-reorder-category moo-reorder-category-title" catid="'+category.uuid+'">';
                        if(category.alternate_name !== null && category.alternate_name !== ""){
                            html +=  category.alternate_name;
                        } else {
                            html +=  category.name;
                        }

                        html +=  '</div>';
                    }
                } else{
                    html += "<div class='normal_text' >You don't have any category,<br/> please import your categories by clicking on <b>Import inventory</b></div>";

                }
                html += '</div>';

                document.querySelector('#MooPanel_tabContent5 #moo-categories-edit-section').innerHTML = html;

                jQuery(".moo-reorder-category-row").sortable({
                    stop: function(event, ui) {
                        var newSortOrder = new Array();
                        jQuery(".moo-reorder-category-row .moo-reorder-category ").each(function (i, el) {
                            newSortOrder[i] = jQuery(this).attr("catid");
                        });
                        jQuery.post(moo_params.ajaxurl,{'action':'moo_new_order_categories','newtable':newSortOrder},function(data){
                            console.log(data);
                        })
                    }
                });
            }
            else
                document.querySelector('#MooPanel_tabContent5 #moo-categories-edit-section').innerHTML  ="<div style='text-align: center'>Please verify your API Key<br/></div>";

        }).fail(function () {
            swal({
                title:"An error has occurred",
                text:"Please try again or contact us",
                type:"error"
            });

        });
    }
}
function Moo_RefeshEditCategorySection(event,uuid) {
    var endpoint = moo_RestUrl+'moo-clover/v2/dash/categories_hours/?_wpnonce='+ moo_params.nonce;
    jQuery.get(endpoint, function (data) {
        if(data.status === "success"){
            moo_custom_hours = data.data;
        } else {
            console.log(moo_custom_hours);
        }
        Moo_SetupEditCategorySection(event,uuid);
    }).fail(function (response) {
        console.log(response);
        Moo_SetupEditCategorySection(event,uuid);
    });
}
function Moo_CustomHoursForCategories() {
    if( document.querySelector('.moo-categories-section') !== null )
    {
        jQuery(".moo-categories-section").show();
        jQuery("#moo-categories-edit-section").hide().html('');
        jQuery("#moo-btn-backtocategories").hide();
        jQuery("#moo-btn-reordercategories").show();
        document.querySelector('#MooPanel_tabContent5 .moo-categories-section').innerHTML = "Loading your categories please wait";
    }
    var endpoint = moo_RestUrl+'moo-clover/v2/dash/categories_hours/?_wpnonce='+ moo_params.nonce;
    jQuery.get(endpoint, function (data) {
        if(data.status === "success"){
            moo_custom_hours = data.data;
        } else {
            console.log(moo_custom_hours);
        }
        Moo_SetupCategoriesSection();
    }).fail(function (response) {
        console.log(response);
        Moo_SetupCategoriesSection();
    });
}

function moo_click_on_limitTime( uuid ) {
    var endpoint = moo_RestUrl+'moo-clover/v2/dash/category/'+uuid+'/time?_wpnonce='+ moo_params.nonce;

    var check = jQuery("#myonoffswitch_limit_time_"+uuid).is(":checked")? true : false;
    if(check){
        jQuery("#moo-av-time-for-"+uuid).slideDown("slow");
        // send changes to server
        jQuery.post(endpoint,{"status":"custom"});
    } else {
        jQuery("#moo-av-time-for-"+uuid).slideUp("slow");
        // send changes to server
        jQuery.post(endpoint,{"status":"all"});
    }
}
function moo_EditCategoryTimeAvailability(event, uuid) {
    if(event) {
        event.preventDefault();
    }
    var cat_time_status = jQuery("#myonoffswitch_limit_time_"+uuid).is(":checked")? "custom" : "all";
    var cat_time = jQuery("#moo-category-availability-time-" + uuid).val();
    if(cat_time === ""){
        swal({
            title:"No Time selected",
            text:"Please choose a time or add a new one",
            type:"error"
        });
        return;
    }
    var endpoint = moo_RestUrl+'moo-clover/v2/dash/category/'+uuid+'/time?_wpnonce='+ moo_params.nonce;
    mooShowWaitMessage();
    jQuery.post(endpoint,{"status":cat_time_status,"hour":cat_time}, function (response) {

        if(response.status === "success" ) {
            swal({
                title:"The category updated",
                type:"success"
            });
        } else {
            swal({
                title:"No changes detected",
                text:'Re-ordering items doesn’t require pressing “Save”',
                type:"error"
            });
        }
    }).fail(function () {
        swal({
            title:"An error has occurred",
            text:"Please try again or contact us",
            type:"error"
        });
        mooHideWaitMessage();
    });
}

function Moo_SetupEditCategorySection(event, uuid) {
    if(event)
        event.preventDefault();
    //todo:show loading section
    if( document.querySelector('.moo-categories-section') !== null ) {
        jQuery(".moo-categories-section").hide();
        jQuery("#moo-categories-edit-section").show().html('');
        var endpoint = moo_RestUrl+'moo-clover/v2/dash/category/'+uuid+'?_wpnonce='+ moo_params.nonce;
        jQuery.get(endpoint, function (data) {
            if(data.uuid !== undefined ) {
                var category = data;
                var html  = '<div class="moo-row moo-goback-row">';
                    html += '<div class="moo-goback-icon" onclick="Moo_SetupCategoriesSection(\''+category.uuid+'\')"><img src="'+moo_params.plugin_url+'/public/img/back.png"/></div><div onclick="Moo_SetupCategoriesSection(\''+category.uuid+'\')" class="moo-goback-text">Back</div>';
                    html += '<div class="mooHelpRefreshLinks">' +
                            '<a href="#" onclick="Moo_RefeshEditCategorySection(event,\''+ category.uuid +'\')">Refresh</a>'+
                            ' | <a href="https://docs.smartonlineorder.com" target="_blank">Help</a>'+
                            '</div>';
                    html += '</div>';

                // Edit section
                html += '<div class="moo-row moo-category-row">';
                html += '<div class="moo-col-md-2">';

                if(category.image_url === null){
                    html += '<div class="moo-category-image moo-cat-no-img">';
                    html +='<span class="moo-category-image-delete-btn" onclick="mooDeleteCategoryImage(event,\''+category.uuid+'\')"><img alt="remove the  image" src="'+ moo_params.plugin_url+"/public/img/close.png"  +'"/></span>';
                    html += '<img src="'+moo_params.plugin_url+'/public/img/noImg3.png" class="moo-category-img-'+category.uuid+'"/>';
                } else {
                    html += '<div class="moo-category-image">';
                    html += '<span class="moo-category-image-delete-btn" onclick="mooDeleteCategoryImage(event,\''+category.uuid+'\')"><img alt="remove the  image" src="'+ moo_params.plugin_url+"/public/img/close.png"  +'"/></span>';
                    html += '<img src="'+ category.image_url  +'" class="moo-category-img-'+category.uuid+'"/>';
                }
                html += '<div class="moo-category-image-PicSelector" onclick="mooUploadImageForCategory(event,\''+category.uuid+'\')">' +
                    '<div class="moo-category-image-label">' +
                    'Update'+
                    '</div>'+
                    '</div>';
                html += '</div>';
                html += '</div>';
                html += '<div class="moo-col-md-8 moo-category-info">';
                //html += '<div class="moo-category-uuid">'+ category.uuid +'</div>';

                if(category.alternate_name !== null && category.alternate_name !== "" && category.alternate_name !== category.name){
                    html += '<div class="moo-category-title"><input class="moo-category-title-input" type="text" value="'+ category.alternate_name.replace(/"/g, '&quot;') +'" />';
                    html += "<div class='moo-category-title-cloverName'> Name on Clover : "+ category.name.replace(/"/g, '&quot;') +'</div></div>';
                } else {
                    html += '<div class="moo-category-title"><input class="moo-category-title-input" type="text" value="'+ category.name.replace(/"/g, '&quot;') +'" /></div>';
                }

               // html += '<div class="moo-category-title"><input class="moo-category-title-input" type="text" value="Dozen bagels dunkin donuts" /></div>';
                html += '<div class="moo-category-description"><textarea class="moo-category-description-input">';
                if( category.description !== null){
                    html += category.description;
                } else {
                   // html += "Our Real Special marinara sauce with fresh tomatoes baked on our signature thin crust, baked to a perfect crisp. We create food we’re proud to serve and deliver it fast, with a smile.";
                    html += "";
                }
                html +='</textarea></div>';
                html += '</div>';
                html += '<div class="moo-col-md-2 moo-category-actions-container">';
                html += '<div class="moo-category-actions">' ;
                html += '<div class="moo-category-action-edit" onclick="Moo_EditCategory(event,\''+category.uuid+'\')">' +
                    '<div class="moo-category-action-edit-button-save">Save</div>'+
                    '</div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';

                // Category hours section
                html += '<div class="moo-row moo-category-row moo-category-hours-row">';
                html += '<div class="moo-col-md-12  moo-category-hours-row-buttons">' +
                    '</div>';
                html += '<div class="moo-col-md-10 moo-category-info">';
                html += '<div class="moo-category-timing-title">Use custom hours for this category</div>';
                html += '</div>';
                html += '<div class="moo-col-md-2 moo-category-actions-container">';
                html += '<div class="moo-category-actions">' +
                    '<div class="moo-onoffswitch">' +
                    '   <input type="checkbox" name="onoffswitch[]" id="myonoffswitch_limit_time_'+ category.uuid +'" class="moo-onoffswitch-checkbox cat_'+ category.uuid +'" onclick="moo_click_on_limitTime(\''+ category.uuid +'\')" ';
                if(category.time_availability === "custom") {
                    html += 'checked' ;
                }
                html +=  '>' +
                    '       <label class="moo-onoffswitch-label" for="myonoffswitch_limit_time_'+ category.uuid +'"><span class="moo-onoffswitch-inner"></span>' +
                    '       <span class="moo-onoffswitch-switch"></span>' +
                    '       </label>' +
                    '   </div>';
                html += '</div>';
                html += '</div>';
                html += '</div>';
                // Choose hours

                html += '<div class="moo-category-timing-content" id="moo-av-time-for-'+category.uuid+'">';
                html += '<div class="moo-col-md-12  moo-category-timing-header"></div>';
                html += '<div class="moo-col-md-8  moo-category-timing-body">' +
                    '<div class="moo-col-md-4">Choose hours</div> '  +
                    '<div class="moo-col-md-8"> <select name="" id="moo-category-availability-time-'+category.uuid+'" onchange="mooSelecetdTimeChanged(event,\'standard\',\''+category.uuid+'\')">' +
                    '<option value="">Select</option>';
                //Add the Custom Hours to the select
                if(moo_custom_hours){
                    Object.keys(moo_custom_hours).forEach(function (key) {
                        html +=  '<option value="'+key+'"' ;
                        if(category.custom_hours!== null && category.custom_hours === key) {
                            html += 'selected' ;
                        }
                        html +=   '>'+moo_custom_hours[key]+'</option>';
                    });
                }


                html +=  '</select>' +
                    '</div>' +
                    '</div>';
                html += '<div class="moo-col-md-3  moo-category-timing-footer"><div class="moo-category-save-time-button" onclick="moo_EditCategoryTimeAvailability(event, \''+category.uuid+'\')">Save</div></div>';
                html += '</div>';


                // Sync section
                html += '<div class="moo-row moo-category-sync-row">';
                html += '<div class="moo-col-md-9 moo-category-sync-col1">';
                html += '   <div class="moo-category-sync-line1">You can rearrange items by dragging and dropping.</div>';
                html += '   <div class="moo-category-sync-line2">If you don\'t see all items, click “Sync” to sync this category with your Clover Inventory</div>';
                html += '   <div class="moo-category-sync-line3">To make changes to items. <a href="admin.php?page=moo_items&category='+category.uuid+'" >Select Items images / description</a></div>';
                html += '</div>';
                html += '<div class="moo-col-md-3 moo-category-sync-col2" onclick="mooImportOneCategory(\''+category.uuid+'\')"><div class="moo-category-sync-button-sync">Sync</div></div>';
                html += '</div>';
                // Reorder items section
                html += '<div class="moo-row moo-reorder-category-items-row">';
                if(category.items !== undefined && category.items.length>0) {
                    for(var i=0;i<category.items.length;i++) {
                        var item = category.items[i];
                        html += '<div class="moo-reorder-category-title" item_uuid="'+item.uuid+'">' +
                            item.name +
                            '</div>';
                    }
                }

                html += '</div>';

                document.querySelector('#MooPanel_tabContent5 #moo-categories-edit-section').innerHTML = html;

                jQuery(".moo-reorder-category-items-row").sortable({
                    stop: function(event, ui) {
                        var newSortOrder = new Array();
                        jQuery(".moo-reorder-category-items-row .moo-reorder-category-title ").each(function (i, el) {
                            newSortOrder[i] = jQuery(this).attr("item_uuid");
                        });
                        jQuery.post(moo_params.ajaxurl,{'action':'moo_reorder_items','newtable':newSortOrder},function(data){
                            console.log(data);
                        })
                    }
                });

                if(category.time_availability === "custom"){
                    jQuery("#moo-av-time-for-"+uuid).slideDown("slow");
                } else  {
                    jQuery("#moo-av-time-for-"+uuid).slideUp("slow");
                }

            } else {
                document.querySelector('#MooPanel_tabContent5 #moo-categories-edit-section').innerHTML  ="<div style='text-align: center'>Please verify your API Key<br/></div>";
            }

        }).fail(function () {
            swal({
                title:"An error has occurred",
                text:"Please try again or contact us",
                type:"error"
            });
            jQuery(".moo-categories-section").show();
            jQuery("#moo-categories-edit-section").hide();

        });
    }
}

function Moo_EditCategory(event,uuid) {
    if(event) {
        event.preventDefault();
    }
    var cat_newName = jQuery(".moo-category-title-input").val();
    var cat_newDescription = jQuery(".moo-category-description-input").val();
    if(cat_newName === ""){
        return;
    }
    var endpoint = moo_RestUrl+'moo-clover/v2/dash/category/'+uuid+'?_wpnonce='+ moo_params.nonce;
    mooShowWaitMessage();
    jQuery.post(endpoint,{"cat_name":cat_newName,"cat_description":cat_newDescription}, function (response) {

        if(response.status === "success" ) {
            swal({
                title:"Update Completed",
                type:"success"
            });
        } else {
            swal({
                title:"No changes detected",
                text:"Please try again or contact us",
                type:"error"
            });
        }
    }).fail(function () {
        swal({
            title:"An error has occurred",
            text:"Please try again or contact us",
            type:"error"
        });
        mooHideWaitMessage();
    });
}

function mooShowQuicklinks(element){
    jQuery(".moo-category-quicklinks",element).show();
}
function mooHideQuicklinks(element){
    jQuery(".moo-category-quicklinks",element).hide();
}
function moo_Update_stats() {
    jQuery.post(moo_params.ajaxurl,{'action':'moo_get_stats'}, function (data) {
            if(data.status === 'Success'){
                window.moo_nb_allItems = data.products;
                jQuery({someValue: 0}).animate({someValue: data.products}, {
                    duration: 5000,
                    easing:'swing',
                    step: function() {jQuery('#MooPanelStats_Products').html(Math.round(this.someValue));}
                });
                jQuery({someValue: 0}).animate({someValue: data.cats}, {
                    duration: 3000,
                    easing:'swing',
                    step: function() {jQuery('#MooPanelStats_Cats').html(Math.round(this.someValue));}
                });
                jQuery({someValue: 0}).animate({someValue: data.labels}, {
                    duration: 3000,
                    easing:'swing',
                    step: function() {jQuery('#MooPanelStats_Labels').html(Math.round(this.someValue));}
                });
                jQuery({someValue: 0}).animate({someValue: data.taxes}, {
                    duration: 3000,
                    easing:'swing',
                    step: function() {jQuery('#MooPanelStats_Taxes').html(Math.round(this.someValue));}
                });
                setTimeout(function(){

                    jQuery('#MooPanelStats_Products').html(data.products);
                    jQuery('#MooPanelStats_Cats').html(data.cats);
                    jQuery('#MooPanelStats_Labels').html(data.labels);
                    jQuery('#MooPanelStats_Taxes').html(data.taxes);

                },5000);
            }

        }
    );
}

function MooChangeOT_Status(uuid) {
    var ot_status = jQuery('#myonoffswitch_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_ot_status',"ot_uuid":uuid,"ot_status":ot_status}, function (data) {
           console.log(data);
        }
    );
}
function MooChangeOT_Status_Mobile(uuid) {
    var ot_status = jQuery('#myonoffswitch_mobile_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_ot_status',"ot_uuid":uuid,"ot_status":ot_status}, function (data) {
            console.log(data);
        }
    );
}
function MooChangeOT_showSa_Mobile(uuid) {
    var ot_showSa = jQuery('#myonoffswitch_sa_mobile_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_ot_showSa',"ot_uuid":uuid,"show_sa":ot_showSa}, function (data) {
            console.log(data);
        }
    );
}
function MooChangeOT_showSa(uuid)
{
    var ot_showSa = jQuery('#myonoffswitch_sa_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_ot_showSa',"ot_uuid":uuid,"show_sa":ot_showSa}, function (data) {
           console.log(data);
        }
    );
}
function moo_addordertype(e)
{
    e.preventDefault();

    var label   = document.querySelector('#Moo_AddOT_label').value;
    var taxable = document.querySelector('#Moo_AddOT_taxable_oui').checked ;
    var show_sa = jQuery("#Moo_AddOT_delivery_oui").prop("checked");
    var minAmount = document.querySelector('#Moo_AddOT_minAmount').value;
    if(label === "")
        swal("Error","Please enter a label for your order Type","error");
    else
    {
        jQuery('#Moo_AddOT_loading').html(window.moo_loading);
        jQuery('#Moo_AddOT_btn').hide();

        jQuery.post(moo_params.ajaxurl,{'action':'moo_add_ot',"label":label,"taxable":taxable,"minAmount":minAmount,"show_sa":show_sa}, function (data) {
            if(data.status === 'success')
            {
                if(data.message === '401 Unauthorized')
                    jQuery('#Moo_AddOT_loading').html('Verify your API key');
                else {
                    swal({
                        title:"Order type added",
                        type:'success'

                    });
                    Moo_GetOrderTypes();
                    jQuery('#Moo_AddOT_loading').html('');
                    jQuery('#Moo_AddOT_btn').show();
                }

            } else {
                jQuery('#Moo_AddOT_loading').html('Verify your API key');
            }
         }).fail(function () {
            jQuery('#Moo_AddOT_loading').html('');
            jQuery('#Moo_AddOT_btn').show();
        });
    }

}
function Moo_deleteOrderType(e,uuid)
{
    e.preventDefault();
    swal({
        text: 'Please confirm that you want delete this order type',
        type: 'warning',
        showCancelButton: true,
        showLoaderOnConfirm: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        preConfirm: function(data) {
            return new Promise(function (resolve, reject) {
                jQuery.post(moo_params.ajaxurl,{'action':'moo_delete_ot',"uuid":uuid}, function (data) {
                    if(data !== null && data.status === 'success') {
                        Moo_GetOrderTypes();
                        setTimeout(function(){ resolve(true); }, 3000);
                    } else {
                        reject(false);
                    }
                }).fail(function ( data ) {
                    reject(false);
                });
            });
        }
    }).then(function (result) {
        if(result.value) {
            swal({
                title:"Order type deleted",
                type:'success'

            });
        } else {
            if(!result.dismiss) {
                swal({
                    title:"Order type not deleted, try again",
                    type:'error'

                });
            }
        }
    });
}

function MooSendFeedBack(e)
{
    e.preventDefault();
    var msg   =  jQuery("#Moofeedback").val();
    var email =  jQuery("#MoofeedbackEmail").val();
    var name  =  jQuery("#MoofeedBackFullName").val();
    var bname =  jQuery("#MoofeedBackBusinessName").val();
    var phone =  jQuery("#MoofeedBackPhone").val();
    var website =  jQuery("#MoofeedBackWebsiteName").val();

    if(msg === '')
    {
        swal("Error","Please enter your message","error");
    }
    else
    {
        var data = {
            'name':name,
            'bname':bname,
            'message':msg,
            'email':email,
            'phone':phone,
            'website':website
        };
        jQuery("#MooSendFeedBackBtn").hide();
        jQuery.post(moo_params.ajaxurl,{'action':'moo_send_feedback','data':data}, function (data) {
            if(data.status === "Success"){
                swal("Thank you","Your feedback has been sent. We will get back to you shortly","success");
                jQuery("#Moofeedback").val("");
                jQuery("#MooSendFeedBackBtn").show();
            }
        });
    }
}
/* Modifiers Panel */

function Moo_changeModifierGroupName(uuid)
{
    var mg_name = jQuery('#Moo_ModifierGroupNewName_'+uuid).val();
    jQuery.post(moo_params.ajaxurl,{'action':'moo_change_modifiergroup_name',"mg_uuid":uuid,"mg_name":mg_name}, function (data) {
           jQuery('#Moo_ModifierGroupSaveName_'+uuid).show();
        }
    );
    setTimeout(function () {
        jQuery('#Moo_ModifierGroupSaveName_'+uuid).hide();
    }, 5000);

}
function Moo_changeModifierGroupName_Mobile(uuid)
{
    var mg_name = jQuery('#Moo_ModifierGroupNewName_mobile_'+uuid).val();
    jQuery.post(moo_params.ajaxurl,{'action':'moo_change_modifiergroup_name',"mg_uuid":uuid,"mg_name":mg_name}, function (data) {
            console.log(data);
            jQuery('#Moo_ModifierGroupSaveName_mobile_'+uuid).show();
        }
    );
    setTimeout(function () {
        jQuery('#Moo_ModifierGroupSaveName_mobile_'+uuid).hide();
    }, 5000);
}
function MooChangeModifier_Status(uuid)
{
    var mg_status = jQuery('#myonoffswitch_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_modifiergroup_status',"mg_uuid":uuid,"mg_status":mg_status}, function (data) {
            console.log(data);
        }
    );
}
function MooChangeModifier_Status_Mobile(uuid)
{
    var mg_status = jQuery('#myonoffswitch_mobile_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_modifiergroup_status',"mg_uuid":uuid,"mg_status":mg_status}, function (data) {
            console.log(data);
        }
    );
}
/* Categories Panel */
function Moo_changeCategoryName(uuid)
{
    var cat_name = jQuery('#Moo_categoryNewName_'+uuid).val();
    if(cat_name != '')
        jQuery.post(moo_params.ajaxurl,{'action':'moo_change_category_name',"cat_uuid":uuid,"cat_name":cat_name}, function (data) {
                jQuery('#Moo_CategorySaveName_'+uuid).show();
            }
        );
        setTimeout(function () {
            jQuery('#Moo_CategorySaveName_'+uuid).hide();
        }, 5000);
}
function Moo_changeCategoryName_Mobile(uuid)
{
    var cat_name = jQuery('#Moo_categoryNewName_mobile_'+uuid).val();
    if(cat_name != '')
        jQuery.post(moo_params.ajaxurl,{'action':'moo_change_category_name',"cat_uuid":uuid,"cat_name":cat_name}, function (data) {
                jQuery('#Moo_CategorySaveName_mobile_'+uuid).show();
            }
        );
    setTimeout(function () {
        jQuery('#Moo_CategorySaveName_mobile_'+uuid).hide();
    }, 5000);
}
function MooChangeCategory_Status(uuid) {
    var cat_status = jQuery('#myonoffswitch_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_category_status',"cat_uuid":uuid,"cat_status":cat_status}, function (data) {
            console.log(data);
        }
    );
}
function MooChangeOrderLater_Status()
{
    var status = jQuery('#myonoffswitch_order_later').prop('checked');
    if(status)
        jQuery("#moo_orderLater_Details").slideDown();
    else
        jQuery("#moo_orderLater_Details").slideUp();
}
function MooChangeCategory_Status_Mo(uuid)
{
    var cat_status = jQuery('#myonoffswitch_NoCategory_Mobile').prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_category_status',"cat_uuid":uuid,"cat_status":cat_status}, function (data) {
            console.log(data);
        }
    );
}
function MooShowCategoriesImages(id)
{
    var status = jQuery('#'+id).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_category_images_status',"status":status}, function (data) {
            console.log(data);
        }
    );
}
function MooShowCategoriesImages_Mobile(id)
{
    var status = jQuery('#myonoffswitch_Visibility_Mobile').prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_category_images_status',"status":status}, function (data) {
            console.log(data);
        }
    );
}
function MooChangeCategory_Status_Mobile(uuid)
{
    var cat_status = jQuery('#myonoffswitch_mobile_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_category_status',"cat_uuid":uuid,"cat_status":cat_status}, function (data) {
            console.log(data);
        }
    );
}
/* Start Upload Images Function */

var media_uploader  = null;
var moo_item_images = [];// {"image_url": "", "image_default": "", "image_enabled": ""}
var moo_category_images;

function uploader_image_category(event,id,responsive){
    event.preventDefault();
    media_uploader = wp.media({
        frame:    "post",
        state:    "insert",
        multiple: false
    });
    // insert image
    media_uploader.on("insert", function(){
        var json = media_uploader.state().get("selection").first().toJSON();
        var image_url = json.url;
        moo_category_images = image_url;
        moo_save_category_images(id,responsive);
    });
    media_uploader.open();
}
function mooUploadImageForCategory(event,uuid){
    event.preventDefault();
    media_uploader = wp.media({
        frame:    "post",
        state:    "insert",
        multiple: false
    });
    // on insert image
    media_uploader.on("insert", function(){
        var json = media_uploader.state().get("selection").first().toJSON();
        var image_url = json.url;
        moo_category_images = image_url;
        var body = {
                'action':'moo_save_category_image',
                'category_uuid':uuid,
                'image':image_url
            };


        var element = jQuery( ".moo-categories-section .moo-category-img-"+uuid );
        element.attr("src",image_url);
        element.parent().removeClass("moo-cat-no-img");

        jQuery.post(moo_params.ajaxurl,body,function(result){
            if (result !== 1) {
                swal("Error","Error when saving the uploaded images try again or contact the support team ","error");
            }
        });
    });
    media_uploader.open();
}
function mooDeleteCategoryImage(event,uuid){
    event.preventDefault();
    swal({
        title: "Are you sure?",
        text: "Please confirm the suppression of this image",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, detach it!",
        showLoaderOnConfirm: true,
        cancelButtonText: "No, cancel!",
        closeOnConfirm: false,
        closeOnCancel: false
    }).then(function(result){
        if (result.value) {
            //mooShowWaitMessage();
            var defaultImg = moo_params.plugin_url + "/public/img/noImg3.png";
            var element = jQuery( ".moo-categories-section .moo-category-img-"+uuid );
            element.attr("src",defaultImg);
            element.parent().addClass("moo-cat-no-img");
            jQuery.post(moo_params.ajaxurl,{'action':'moo_delete_img_category',"uuid":uuid}, function(data){
                if (data !== 1) {
                    swal("Error","Error when removing the image try again or contact the support team ","error");
                }
            });
        }
    });
}

function moo_save_category_images(uuid,response)
{
    if(Object.keys(moo_category_images).length>0)
    {
        image = moo_category_images;
        if(response == 'D'){
            tr_new(uuid,image);
        }
       else {
            img_row(uuid,image);
        }
        jQuery.post(moo_params.ajaxurl,{'action':'moo_save_category_image',"category_uuid":uuid,"image":moo_category_images},function(ret){
            if (ret == 1) {
                //console.log(ret);
            }
            else
                swal("Error","Error when saving your changes, please try again","error");

        });
    }
    else
    {
        history.back();
    }
}
function open_media_uploader_image() {
    media_uploader = wp.media({
        frame:    "post",
        state:    "insert",
        multiple: false
    });

    media_uploader.on("insert", function(){
        json = media_uploader.state().get("selection").first().toJSON();
        
        var image_url = json.url;
        var image_caption = json.caption;
        var image_title = json.title;
        moo_item_images.push({"image_url": image_url, "image_default": "1", "image_enabled": "1"});
        moo_display_item_images();
    });
    media_uploader.open();
}

function moo_display_item_images() {
    console.log('moo_display_item_images');
    jQuery('#moo_itemimagesection').html('');
    for(i in moo_item_images){
        var image = moo_item_images[i].image_url;
        var a1 = parseInt(moo_item_images[i].image_default);
        var b1 = parseInt(moo_item_images[i].image_enabled);
        var tag = "";
        var tag1 = "";
        if (a1 === 1) {
            tag = "<input id='image_default_id_"+i+"' onchange='moo_default_item_image("+i+")' type='radio' name='image_default' value='image_default' checked><label style='position: relative; top: -4px; right: -10px;' for='image_default_id_"+i+"'>Default Image</label>";
        } else {
            tag = "<input id='image_default_id_"+i+"' onchange='moo_default_item_image("+i+")' type='radio' name='image_default' value='image_default'><label style='position: relative; top: -4px; right: -10px;' for='image_default_id_"+i+"'>Default Image</label>";
        }
        if (b1 === 1) {
            tag1 = "<input id='image_enabled_id_"+i+"' onchange='moo_enable_item_image("+i+")' type='checkbox' name='image_enabled"+i+"' value='image_enabled' checked><label style='position: relative; top: -4px; right: -10px;' for='image_default_id_"+i+"'>Image Enabled</label>";
        } else {
            tag1 = "<input id='image_enabled_id_"+i+"' onchange='moo_enable_item_image("+i+")' type='checkbox' name='image_enabled"+i+"' value='image_enabled'><label style='position: relative; top: -4px; right: -10px;' for='image_default_id_"+i+"'>Image Enabled</label>";
        }
        /*var html = '<table style="margin: 0 auto;">'+
                    '<tr><td rowspan="3"><img height="200" width="300" src="'+image+'" alt=""></td>'+
                    '<td><a href="#" onclick="moo_delete_item_images(\''+i+'\')">Delete</a></td>'+
                    '<tr><td>'+tag+'</td></tr>'+
                    '<tr><td>'+tag1+'</td></tr></table>';*/

        var html = '<div class="image_item" style="width: 30%; display: inline-block; margin: 1%;">'+
                    '<img class="img-rounded img-thumbnail img-responsive image1" width="" src="'+image+'" alt="">'+
                    '<div class="image_options_holder"><div><a href="#" onclick="moo_delete_item_images(&quot;'+i+'&quot;)">Delete</a></div>'+
                    '<div style="margin-top: 4px;">'+tag+'</div>'+
                    '<div style="margin-top: 4px;">'+tag1+'</div></div></div>';
        jQuery('#moo_itemimagesection').append(html);

    }
}
function moo_delete_item_images(id) {
    delete(moo_item_images[id]);
    moo_display_item_images();
}
function moo_default_item_image(id) {
    jQuery("input[name=image_default]:checked");
    moo_item_images[id].image_default = "1";
    for (var i = 0; i < moo_item_images.length; i++) {
        if(i == id) continue;
        else moo_item_images[i].image_default = "0";
    }
}
function moo_enable_item_image(id) {
    var b = jQuery("input#image_enabled_id_"+id+"").is(':checked');
    if (b) {
        moo_item_images[id].image_enabled = "1";
        b = false;    
    } else {
        moo_item_images[id].image_enabled = "0";
        b = true;
    }
}

function moo_save_item_images(uuid) {
    var description = jQuery('#moo_item_description').val();
    var flag = false;
    for(var i=0 in moo_item_images) {
        if (moo_item_images[i].image_default == "1" && flag == false) {
            flag = true;
            continue;
        }
        if(moo_item_images[i].image_default == "1" && flag == true) {
            moo_item_images[i].image_default = "0";
        }
    }
    var images = [];
    for(i in moo_item_images) {
        images.push({"image_url": moo_item_images[i].image_url, 
            "image_default": moo_item_images[i].image_default, 
            "image_enabled": moo_item_images[i].image_enabled
        });
    }
    jQuery.post(moo_params.ajaxurl,{'action':'moo_save_items_with_images',"item_uuid":uuid,"description":description,"images":images}, function (data) {
        if(data.status == 'Success') {
            if(data.data==true) {
                swal("Your changes were saved");
                history.back();
            }
            else
                swal("Error","Description too longError when saving your changes, please try again","error");
        } else
            swal("Error","Error when saving your changes, please try again","error");
    }
    );

}

function moo_get_item_with_images(uuid) {
    jQuery.post(moo_params.ajaxurl,{'action':'moo_get_items_with_images',"item_uuid":uuid}, function (data) {
        var items = data.data;
        for(i in items ){
            var item = items[i];
            if(item._id) {
                var image_url = item.url;
                var image_default = item.is_default;
                var image_enabled = item.is_enabled;
                moo_item_images.push({"image_url": image_url, "image_default": image_default, "image_enabled": image_enabled});
            }
        }
        moo_display_item_images();
    });
}
/*End upload Functions*/

function MooPanel_UpdateItems(event) {
    event.preventDefault();
    window.bar.animate(0.01);
    window.bar.setText('1 %');
    window.itemReceived = 0;
    moo_upadateItemsPerPage(0);
}
function MooPanel_UpdateCategories(event)
{
    event.preventDefault();
    window.bar.animate(0.01);
    window.bar.setText('1 %');

    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_categories'}, function (data)
        {
            window.bar.animate(0.5);
            window.bar.setText('50 %');
        }
    ).done(function () {
            swal("Categories updated");
            window.bar.animate(1.0);
            window.bar.setText('100 %');

    });
}
function MooPanel_UpdateModifiers(event)
{
    event.preventDefault();
    window.bar.animate(0.01);
    window.bar.setText('1 %');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_modifiers_groups'}, function (data)
        {
            window.bar.animate(0.5);
            window.bar.setText('50 %');
        }
    ).done(function () {
        jQuery.post(moo_params.ajaxurl,{'action':'moo_update_modifiers'}, function (data)
            {
                window.bar.animate(1.0);
                window.bar.setText('100 %');
            }
        ).done(function () {
            swal("Modifiers updated");
            window.bar.animate(1.0);
            window.bar.setText('100 %');

        });

    });
}
function MooPanel_UpdateOrderTypes(event)
{
    event.preventDefault();
    window.bar.animate(0.01);
    window.bar.setText('1 %');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_order_types'}, function (data) {
            window.bar.animate(1.0);
            window.bar.setText('100 %');
    }).done(function () {
        Moo_GetOrderTypes();
        swal("Order Types updated");
        window.bar.animate(1.0);
        window.bar.setText('100 %');

    });


}
function MooPanel_UpdateTaxes(event)
{
    event.preventDefault();
    window.bar.animate(0.01);
    window.bar.setText('1 %');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_taxes'}, function (data)
        {
            window.bar.animate(1.0);
            window.bar.setText('100 %');
        }
    ).done(function () {
        swal("Taxes updated");
        window.bar.animate(1.0);
        window.bar.setText('100 %');

    });


}
function moo_upadateItemsPerPage(page) {
    var received = 0;
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_items','page':page}, function (data)
    {
        received = data.received;
        var percent_loaded = data.received*100/window.moo_nb_allItems;

        if( percent_loaded === null )
            percent_loaded = 1;

        if (window.moo_nb_allItems !== 0 ) {
            window.bar.animate(bar.value()+percent_loaded/100);
        }
    }
    ).done(function () {
        if(received>0) {
            window.itemReceived += received;
            moo_upadateItemsPerPage(page+1);
            window.bar.setText(window.itemReceived+' items updated');
        } else {
            swal("Items updated");
            window.bar.animate(1.0);
            window.bar.setText('100 %');
            moo_Update_stats();

        }
    });
}

function moo_bussinessHours_Details(status)
{
     if(status)
         jQuery('#moo_bussinessHours_Details').removeClass('moo_hidden');
    else
         jQuery('#moo_bussinessHours_Details').addClass('moo_hidden');

}
function moo_filtrer_by_category(e)
{
    e.preventDefault();
    var cat_id = jQuery('#moo_cat_filter').val();
    if(cat_id != '') {
        document.location.href = 'admin.php?page=moo_items'+cat_id;
    } else {
        document.location.href = 'admin.php?page=moo_items';
    }
}
function moo_editItemDescription(event,item_uuid)
{
    event.preventDefault();
    var new_description = jQuery('#edit-description-content-'+item_uuid).val();
    jQuery.magnificPopup.close();
    jQuery.post(moo_params.ajaxurl,{'action':'moo_save_items_description',"item_uuid":item_uuid,"description":new_description}, function (data) {
            if(data.status === 'Success') {
                if(data.data === true) {
                    swal("The description was updated");
                } else {
                    swal("Error when saving your changes, please try again","","error");
                }
            }
            else
                swal("Error when saving your changes, please try again","","error");
        }
    );

}

function moo_showOrderTypeDetails(e,id) {
    if (e !==  null)
        e.preventDefault();
    //jQuery('#detail_'+id).slideToggle( "slow" );
    jQuery('#detail_'+id).slideToggle('fast', function() {
        if (jQuery(this).is(':visible')) {
            jQuery("#top-bt-"+id).css('display','none');
        } else {
            jQuery("#top-bt-"+id).css('display','block');
        }
    });
}
function moo_saveOrderType(e,uuid) {
    e.preventDefault();
    mooShowWaitMessage();
    var name = jQuery('#label_'+ uuid).val();
    var isEnabled = jQuery('#select_En_' + uuid).prop('checked')?'1':'0';
    var useCoupons = jQuery('#useCoupons_' + uuid).prop('checked')?'1':'0';
    //var availabilityTime = jQuery('#availabilityTime_' + uuid).prop('checked')?'1':'0';
    var taxable = jQuery('#select_Tax_'+ uuid).val();
    var type = jQuery('#select_type_'+ uuid).val();
    var minAmount = jQuery('#minAmount_'+ uuid).val();
    var availabilityCustomTime = jQuery('#availabilityCustomTime_'+ uuid).val();
    var customMessage = jQuery('#moo_ot_customMessage_'+ uuid).val();
    if(customMessage.length>200){
        swal({
            text:'Custom message length must be least than 200 characters'
        });
        return;
    }
    var data = {
        "action":'moo_update_ordertype',
        "name":name,
        "enable":isEnabled,
        "availabilityTime":"",
        "taxable":taxable,
        "type":type,"uuid":uuid,
        "minAmount":minAmount,
        "availabilityCustomTime":availabilityCustomTime,
        "useCoupons":useCoupons,
        "customMessage":customMessage,
    };
    jQuery.post(moo_params.ajaxurl,data, function (data) {
           // console.log(data);
            if(data && data.data == "1") {
                if(data.updated) {
                    swal({
                        text:'The order type "'+name+'" was updated'
                    });
                } else {
                    swal({
                        text:'The order type "'+name+'" was updated only locally, maybe you removed this order type from your account in Clover'
                    });
                }
                Moo_GetOrderTypes();
            } else {
                swal({
                    text:'The order type "'+name+'" was updated'
                });
            }
        }
    ).fail(function (data) {
        swal({
            text:'The order type "'+name+'" was removed from your account in Clover.com, You may need to re-create another one'
        });
    });

}
function moo_showCustomerMap(event,lat,lng)
{
    event.preventDefault();
    var location = {};
    location.lat = parseFloat(lat) ;
    location.lng = parseFloat(lng) ;

    jQuery('#mooCustomerMap').show();

    var map = new google.maps.Map(document.getElementById('mooCustomerMap'), {
        zoom: 15,
        center: location
    });

    var marker = new google.maps.Marker({
        position: location,
        map: map
    });

}
function mooDeleteCoupon(e)
{
    e.preventDefault();
    var url = jQuery(e.target).attr("href");
    swal({
        text: 'Please confirm that you want delete this coupon',
        type: 'warning',
        showCancelButton: true,
        showLoaderOnConfirm: false,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'

    }).then(function (data) {
        if(data) {
            swal.close();
            window.location.href = url;
        } else {
            swal({
                title:"Coupon not deleted, try again",
                type:'error'

            });
        }
    });
}

function moo_login2checkoutClicked(status)
{
    if(status === true) {
        jQuery(".moo_login2checkout").show();
    } else {
        jQuery(".moo_login2checkout").hide();
    }
}
function moo_click_on_textUnderSI(status)
{
    if(status === true) {
        jQuery(".moo_textUnderSI").show();
    } else {
        jQuery(".moo_textUnderSI").hide();
    }
}
function moo_couponsStatusClicked(status)
{
    if(status === true) {
        jQuery("#moo_use_couponsapp").show();
    } else {
        jQuery("#moo_use_couponsapp").hide();
    }
}
function moo_saveCardsClicked(status)
{
    if(status==true) {
        jQuery(".moo_saveCardsClicked").show();
    } else {
        jQuery(".moo_saveCardsClicked").hide();
    }
}

/*
 * Clean the inventory, is about removing data that was removed on Clover from local db
 * @version 1.2.8
 */

function MooPanel_CleanInventory(e)
{
    e.preventDefault();
    swal.setDefaults({
        confirmButtonText: 'Next &rarr;',
        allowOutsideClick: false,
        showCancelButton: true,
        animation: false,
        progressSteps: ['0','1', '2', '3','4', '5', '6']
    });

    var steps = [
        {
            title: 'Clean Inventory',
            html: 'This may take several minutes depending on the size of your inventory. Only use this feature if you have made significant '+
                   'changes on Clover and you find it a hassle to manually hide them from the website. <br /> Click "Next" if you wish to proceed'
        },
        {
            title: 'Order Types',
            html: '<p>Click "Start" to remove old order types that have been deleted from Clover yet still appears on the website.<br/> Click "Next" to move on to Tax Rates </p>'+
                  '<div id="mooClean_order_types"></div>'+
                  '<a href="" class="button button-secondary" onclick="mooClean(event,\'order_types\')">Start</a>'
        },
        {
            title: 'Taxes Rates',
            html: '<p>Click "Start" to remove old tax rates that have been deleted from Clover yet still appears on the website.<br/> Click "Next" to move on to Modifiers Groups</p>'+
                  '<div id="mooClean_tax_rates"></div>'+
                  '<a href="" class="button button-secondary" onclick="mooClean(event,\'tax_rates\')">Start</a>'
        },
        {
            title: 'Modifier Groups',
            html: '<p>Click "Start" to remove old Modifier Groups that have been deleted from Clover yet still appears on the website.<br/> Click "Next" to move on to Modifiers</p>'+
                  '<div id="mooClean_modifier_groups"></div>'+
                  '<a href="" class="button button-secondary" onclick="mooClean(event,\'modifier_groups\')">Start</a>'
        },
        {
            title: 'Modifiers',
            html: '<p>Click "Start" remove old Modifiers that have been deleted from Clover yet still appears on the website. This may take a few minutes.<br/> Click on "Next" to move on to Categories</p>'+
                  '<div id="mooClean_modifiers"></div>'+
                  '<a href="" class="button button-secondary" onclick="mooClean(event,\'modifiers\')">Start</a>'
        },
        {
            title: 'Categories',
            html: '<p>Click "Start" to remove old Categories that have been deleted from Clover yet still appears on the website. <br/> Click on "Next" to move on to Items '+
                  '<div id="mooClean_categories"></div>'+
                  '<a href="" class="button button-secondary" onclick="mooClean(event,\'categories\')">Start</a>'
        },
        {
            title: 'Items',
            html: '<p>Click "Start" to remove old items that have been deleted from Clover yet still appears on the website. '+
                    'This may take a few minutes. <br/> Click "Next" to finish and exit</p>'+
                  '<div id="mooClean_items"></div>'+
                  '<a href="" class="button button-secondary" onclick="mooClean(event,\'items\')">Start</a>'
        }
    ];

    swal.queue(steps).then(function () {
        swal.resetDefaults();
        swal({
            title: 'All Done!',
            html:
            "The clean up process has been successfully completed. If you have added additional items to your Clover inventory, you will need to do a manual sync. Clean inventory feature only removes old items",
            confirmButtonText: 'ok'
        })
    }, function () {
        swal.resetDefaults()
    })
}
/*
 * This function to Clean the inventory, we set teh typOfdate wich may take (order_tyes,tax_rates,categories,items..)
 * The default number of data per page is 10, then we send an other request using the recursive loop CleanByPage
 */
function mooClean(event,typeOfDate)
{
    event.preventDefault();
    jQuery(event.target).hide();
    var id = "#mooClean_"+typeOfDate;
    var pbar = new ProgressBar.Line(id, {
        strokeWidth: 4,
        easing: 'easeInOut',
        duration: 1400,
        color: '#496F4E',
        trailColor: '#eee',
        trailWidth: 1,
        svgStyle: {width: '100%', height: '100%'},
        text: {
            style: {
                // Text color.
                // Default: same as stroke color (options.color)
                color: '#999',
                right: '0',
                top: '30px',
                padding: 0,
                margin: 0,
                transform: null
            },
            autoStyleContainer: false
        },
        from: {color: '#FFEA82'},
        to: {color: '#ED6A5A'}
    });

    pbar.animate(0.1);
    jQuery.get(moo_RestUrl+'moo-clover/v1/clean/'+typeOfDate+'/10/0',function (data) {
        var nb = parseInt(data["nb_"+typeOfDate]);
        pbar.setText(nb +' '+typeOfDate.replace("_"," ")+' checked');
        if(! data.last_page)
            mooCleanByPage(1,pbar,typeOfDate);
        else
        {
            jQuery(id).html(nb +' '+typeOfDate.replace("_"," ")+' Cleaned, You can click on Next')
        }
    });


}
function mooCleanByPage(page,pbar,typeOfDate)
{
    var id = "#mooClean_"+typeOfDate;

    if(page/10 <1)
        pbar.animate(page/10);
    else
        pbar.animate(1.0);

    jQuery.get(moo_RestUrl+'moo-clover/v1/clean/'+typeOfDate+'/10/'+page,function (data) {
        var nb = (parseInt(data["nb_"+typeOfDate])+parseInt(page*10));
        pbar.setText( nb + ' ' + typeOfDate.replace("_"," ")+' checked');
        if(! data.last_page)
            mooCleanByPage(page+1,pbar,typeOfDate);
        else
        {
            jQuery(id).html(nb + ' ' + typeOfDate.replace("_"," ") +' Cleaned, You can click on Next')
        }
    });
}

function mooImportOneCategory(cat_id) {
    swal({
        html:
        '<div class="moo-msgPopup">Checking the items</div>' +
        '<img src="'+ moo_params['plugin_img']+'/loading.gif" class="moo-imgPopup" width="80px"/>',
        showConfirmButton: false
    });
    jQuery.get(moo_RestUrl+'moo-clover/v1/inventory/categories/'+cat_id,function (data) {
        if(data.status =='success'){
            swal({
                title:"Update Completed",
                text:"You may need to refresh the page to see the changes",
                type:"success"
            });
            if(typeof Moo_SetupEditCategorySection === 'function')
                Moo_SetupEditCategorySection(null,cat_id);
        } else {
            swal({
                title:"An error has occurred",
                text:"try again",
                type:"error"
            });
        }


    });
}
function mooUpdateOneCategory( cat_id ) {
    swal({
        html:
        '<div class="moo-msgPopup">Checking the items</div>' +
        '<img src="'+ moo_params['plugin_img']+'/loading.gif" class="moo-imgPopup"/>',
        showConfirmButton: false
    });
    jQuery.get(moo_RestUrl+'moo-clover/v1/inventory/categories/'+cat_id,function (data) {
        if(data.status == 'success'){
            swal({
                title:"Successfully Updated",
               // text:"You have "+data.clover_nb_items+" in your Clover account, and "+data.nb_items+" in your website, don't forget to refresh the page",
                text:"You may need to refresh the page to see the changes",
                type:"success"
            });
        } else {
            swal({
                title:"An error has occurred",
                text:"try again",
                type:"error"
            });
        }


    });
}

function expandSection(element) {
    jQuery(element).parent().toggleClass("MooPanelItemExpanded")
}
function expandAllSections(element) {
    if(jQuery(element).text() ==='[ Collapse All ]'){
        var parent = jQuery(element).parent();
        jQuery("#"+parent.attr("id")+" .MooPanelItem").each(function (i, el) {
            jQuery(el).removeClass("MooPanelItemExpanded")
        });
        jQuery(element).text("[ Expand All ]");
    } else {
        var parent = jQuery(element).parent();
        jQuery("#"+parent.attr("id")+" .MooPanelItem").each(function (i, el) {
            jQuery(el).addClass("MooPanelItemExpanded")
        });
        jQuery(element).text("[ Collapse All ]");
    }

}
function mooSaveChanges(event,element) {
    swal({
        title: 'Saving your changes, please wait ..',
        showConfirmButton: false
    });
}
function mooShowWaitMessage() {
    swal({
        title: 'Saving your changes, please wait ..',
        showConfirmButton: false
    });
}
function mooHideWaitMessage() {
    swal.close();
}

/**
 * Change the api key
 */
function mooUpdateApiKey() {
    mooShowWaitMessage();
    var newApiKey = jQuery("#chang_api_key").val();
    var endpoint = moo_RestUrl+'moo-clover/v2/dash/update_api_key?_wpnonce='+ moo_params.nonce;
    var data = {
      "api_key":newApiKey
    };
    jQuery.post(endpoint,data, function (response) {
        if(response.status){
            swal({
                type:"success",
                text:response.message
            });
        } else {
            swal({
                type:"error",
                text:response.message
            });
        }

    }).fail(function (response) {
        swal({
            title: 'An error has occurred, please refresh the page  and try again'
        });
        console.log(response);
    });

}
/* Scroll into div functions */

function scrollToElm(container, elm, duration){
    var pos = getRelativePos(elm);
    scrollTo( container, pos.top , duration);
}

function getRelativePos(elm){
    var pPos = elm.parentNode.getBoundingClientRect(), // parent pos
        cPos = elm.getBoundingClientRect(), // target pos
        pos = {};

    pos.top    = cPos.top    - pPos.top + elm.parentNode.scrollTop,
        pos.right  = cPos.right  - pPos.right,
        pos.bottom = cPos.bottom - pPos.bottom,
        pos.left   = cPos.left   - pPos.left;

    return pos;
}

function scrollTo(element, to, duration, onDone) {
    var start = element.scrollTop,
        change = to - start,
        startTime = performance.now(),
        val, now, elapsed, t;

    function animateScroll(){
        now = performance.now();
        elapsed = (now - startTime)/1000;
        t = (elapsed/duration);

        element.scrollTop = start + change * easeInOutQuad(t);
        if( t < 1 )
            window.requestAnimationFrame(animateScroll);
        else
            onDone && onDone();
    };

    animateScroll();
}

function easeInOutQuad(t){
    return t<.5 ? 2*t*t : -1+(4-2*t)*t
};

function  mooFilterModifiers(event) {
    var elem = jQuery(event.target);
    var word = elem.val();
    if(word!==""){
        jQuery("li.list-group>.label_name>.getname").each(function (index, element) {
            var text = jQuery(element).text().toUpperCase();
            if(text.includes(word.toUpperCase())) {
                jQuery(element).parent().parent().show();
            }  else {
                jQuery(element).parent().parent().hide();
            }
        })
    } else {
        jQuery(".list-group").each(function (index, element) {
            jQuery(element).show();
        })
    }

}
function mooSelecetdTimeChanged(event,type,source) {
    var elem = jQuery(event.target);
    var choice = elem.val();
    if(choice === "addNew") {
        mooShowAddingNewTimeSection(type,source);
        elem.val("");
    } else {
        if(choice === "ImportNew") {
            mooShowAddingImportTimeSection(type,source);
            elem.val("");
        }
    }
}
function mooShowAddingNewTimeSection(type,source) {
    popupWindow = window.open(
        'https://smh.smartonlineorder.com/hours/'+moo_api_key+"?type="+type+"&source="+source,
        'popUpWindow',
        'height=650,width=780,left=100,top=100,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no, status=yes'
    );
}
function mooShowAddingImportTimeSection(type,source) {
    popupWindow = window.open(
        'https://smh.smartonlineorder.com/import/'+moo_api_key+"?type="+type+"&source="+source,
        'popUpWindow',
        'height=650,width=780,left=100,top=100,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no, status=yes'
    );
}
function handlePopupResult(event) {
    if (event.origin === "https://smh.smartonlineorder.com") {
        //alert(event.data);
        if(event.data.type === "ordertypes") {
            swal({
                title: 'Getting your changes, please wait ..',
                showConfirmButton: false
            });
            Moo_RefreshOrderTypesSection(event.data.source);
        }
        if(event.data.type ==="standard") {
            swal({
                title: 'Getting your changes, please wait ..',
                showConfirmButton: false
            });
            Moo_RefeshEditCategorySection(null,event.data.source);
        }
    }
}
