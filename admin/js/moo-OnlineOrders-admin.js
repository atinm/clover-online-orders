jQuery(document).ready(function($){
    $("div.faq_question").click(function() {
        var clicked = $(this);
        // Get next element to current element
        clicked = clicked.next();
        // Show or hide the next element
        clicked.toggle();
    });

    window.moo_loading = '<svg xmlns="http://www.w3.org/2000/svg" width="44px" height="44px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-default"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(0 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(30 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.08333333333333333s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(60 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.16666666666666666s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(90 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.25s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(120 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.3333333333333333s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(150 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.4166666666666667s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(180 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.5s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(210 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.5833333333333334s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(240 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.6666666666666666s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(270 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.75s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(300 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.8333333333333334s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(330 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.9166666666666666s" repeatCount="indefinite"></animate></rect></svg>';
    window.moo_first_time = true; // this variable is used to make sure the an action is happen only one time
    window.moo_nb_allItems =0;

    moo_Update_stats();
    Moo_GetOrderTypes();

    $('.moo-color-field').wpColorPicker();

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

    if($('div#MooPanel_tabContent4 input[value=style1]').prop('checked')) {
        a = $('div#MooPanel_tabContent4 input[value=style1]').next();
        a.css('border', '5px solid #1e5429');
        $('div#MooPanel_tabContent4 input[value=style3]').next().css('border', 'none');
    }
    if ($('div#MooPanel_tabContent4 input[value=style3]').prop('checked')) {
        a = $('div#MooPanel_tabContent4 input[value=style3]').next();
        a.css('border', '5px solid #1e5429');
        $('div#MooPanel_tabContent4 input[value=style1]').next().css('border', 'none');
    }
    $('div#MooPanel_tabContent4 input[value=style1]').click(function() {
        $('div#MooPanel_tabContent4 input[value=style1]').next().css('border', '5px solid #1e5429');
        $('div#MooPanel_tabContent4 input[value=style3]').next().css('border', 'none');
    });
    $('div#MooPanel_tabContent4 input[value=style3]').click(function() {
        $('div#MooPanel_tabContent4 input[value=style3]').next().css('border', '5px solid #1e5429');
        $('div#MooPanel_tabContent4 input[value=style1]').next().css('border', 'none');
    });

    $("#show_menu").click(function() {
        $("#menu_for_mobile ul").toggle();
    });
    $("#sortable").sortable({
        stop: function(event, ui) {
            var tabNew = new Array();
            var i = 0;

            $("#sortable tr").each(function(i, el){
                tabNew[i] = $(this).attr("data-cat-id");
                i++;
            });
            jQuery.post(moo_params.ajaxurl,{'action':'moo_new_order_categories','newtable':tabNew},function(data){
                //console.log(data);
            })
        }
    });
    //$( "#orderCategory" ).sortable();
    $("#orderCategory").sortable({
        stop: function(event, ui) {
            var tabNew = new Array();
            var i = 0;
            $("#orderCategory .category-item").each(function (i, el) {
                tabNew[i] = $(this).attr("cat-id-mobil");
                i++;
            });
            jQuery.post(moo_params.ajaxurl,{'action':'moo_new_order_categories','newtable':tabNew},function(data){
                //console.log(data);
            })
        }
    });
    $('#orderCategory input').bind('click.sortable mousedown.sortable',function(ev){
        ev.target.focus();
    });
   name_cat = "";
    $(".table_category").on("click",".edit_name",function(event){
        event.preventDefault();
        var idCat = $(this).parent().parent().find("td:eq(0)").text();
        var nameCat = $(this).parent().parent().find("td:eq(2)").text();
        var id_name = $(this).parent().parent().find("td:eq(2)").attr("id");
        name_cat = nameCat;
        var c_html = "";
        c_html +="<div class='input-change-name'>";
        c_html += "<div class='input-name'>";
        c_html += "<input type='text' value='"+nameCat+"' id='newName"+idCat+"' class='newname'>";
        c_html += "</div>";
       // c_html += "<div class='button-name'>";
        c_html += "<div class='bt-valider'>";
        c_html +='<a href="#" class="vald-change-name" onclick="vald_change_name(event,\''+idCat+'\',\'D\')">';
        c_html +='<span id="moo_valide_change1" data-ot="valider_change_name" data-ot-target="#moo_valide_change1">';
        c_html +="<img src='"+moo_params.plugin_url+"/public/img/valider.png' alt='Validate change'>";
        c_html +="</span>";
        c_html +="</a>";
        c_html += "</div>";
        c_html += "<div class='bt-annuler'>";
        c_html +='<a href="#" class="annuler-change-name" onclick="annuler_change_name(event,\''+idCat+'\',\'D\',\''+nameCat+'\')">';
        c_html += "<img src='"+moo_params.plugin_url+"/public/img/annuler.png' alt='Annuler change'>";
        c_html +="</a>";
        c_html += "</div>";
        //c_html += "</div>";
        c_html += "</div>";
        $("#"+id_name).html(c_html);
    });
    /* --- Modifier Group --- */
    $(".moo_ModifierGroup").sortable({
        stop: function(event, ui) {
            var tabNew = new Array();
            var i = 0;
            $(".moo_ModifierGroup .list-group").each(function (i, el) {
                tabNew[i] = $(this).attr("group-id");
                i++;
            });
            $.post(moo_params.ajaxurl,{'action':'moo_new_order_group_modifier','newtable':tabNew},function(data){
                console.log(data);
            })
        }
    });
    $('.moo_ModifierGroup input').bind('click.sortable mousedown.sortable',function(ev){
        ev.target.focus();
    });
    $(".sub-group").sortable({
        stop: function(event, ui) {
            var group = $(this).attr("GM");
            var tabNew = new Array();
            var i = 0;
            $(".moo_ModifierGroup .list-GModifier_"+group).each(function (i, el) {
                tabNew[i] = $(this).attr("group-id");
                i++;
            });
            //var NB = tabNew.length;
            $.post(moo_params.ajaxurl,{'action':'moo_new_order_modifier','group_id':group,'newtable':tabNew},function(data){
                console.log(data);
            })
        }
    });
    $('.sub-group input').bind('click.sortable mousedown.sortable',function(ev){
        ev.target.focus();
    });
   /* --- Modifier Group --- */

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
    function annulerChangeNameGG(event,id,name){
        event.preventDefault();
        var name = jQuery("#label_"+id+" .getname").text();
        jQuery("#label_"+id+" .getname").css("display","block");
        jQuery("#label_"+id+" .change-name").css("display","none");
        jQuery("#label_"+id+" .change-name input").val(name);
    }

    function show_sub(event,id){
        event.preventDefault();
        jQuery('#detail_group_'+id).slideToggle('slow', function() {
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
        if (v=="D"){jQuery("td#name_"+uuid).html(newname);}
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
        if (v=="D"){jQuery("td#name_"+uuid).html(name_cat);}
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
         }
    else {
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
    html +='<a href="#" onclick="uploader_image_category(event,\''+uuid+'\',\'M\')">';
    html +="<img src='"+moo_params.plugin_url+"public/img/upload.png' style='width: 20px;'>";
    html +='</a>';
    html +='</div>';
    html +='<div class="bt bt-edit">';
    html +='<a href="#" onclick="edit_name_mobil(event,\''+uuid+'\')">';
    html +="<img src='"+moo_params.plugin_url+"public/img/edit.png' style='width: 20px;'>";
    html +='</a>';
    html +='</div>';
    if(img == ""){
        jQuery("#id_img_M_"+uuid).html("<label>Pecture</label><img src='"+moo_params.plugin_url+"/public/img/no-image.png' style='width: 50px;'>")
        jQuery("#id_bt_M"+uuid).html(html);
    }
    else{
        html +='<div class="bt bt-delete">';
        html +='<a href="#" onclick="delete_img_category(event,\''+uuid+'\',\'M\')">';
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
    htmlC +='<a href="#" class="vald-change-name" onclick="vald_change_name(event,\''+uuid+'\',\'M\')" id="bt_MV_'+uuid+'">';
    htmlC +="<img src='"+moo_params.plugin_url+"/public/img/valider.png'  alt='Validate change' style='width: 18px;'>";
    htmlC +="</a>";
    htmlC +='<a href="#" class="annuler-change-name" onclick="annuler_change_name(event,\''+uuid+'\',\'M\',\''+name+'\')" id="bt_MA_'+uuid+'">';
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
        input_check = '<input type="checkbox" name="onoffswitch[]" id="myonoffswitch_Visibility_'+uuid+'" class="onoffswitch-checkbox visib_cat'+uuid+'" onclick="visibility_cat(\''+uuid+'\')" checked>';
    }
    else{
        input_check = '<input type="checkbox" name="onoffswitch[]" id="myonoffswitch_Visibility_'+uuid+'" class="onoffswitch-checkbox visib_cat'+uuid+'" onclick="visibility_cat(\''+uuid+'\')">';
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
    cont_html +='<div class="onoffswitch" title="Visibility Category">';
    cont_html +=input_check;
    cont_html +='<label class="onoffswitch-label" for="myonoffswitch_Visibility_'+uuid+'"><span class="onoffswitch-inner"></span>';
    cont_html +='<span class="onoffswitch-switch"></span>';
    cont_html +='</label>';
    cont_html +="</div>";
    cont_html +="</td>";
    if (img == ""){
        cont_html +="<td class='bt-cat'>";
        cont_html +='<a href="#" onclick="uploader_image_category(event,\''+uuid+'\',\'D\')" title="Uploader Image">';
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
        cont_html +='<a href="#" onclick="uploader_image_category(event,\''+uuid+'\',\'D\')" title="Change Image">';
        cont_html +="<img src='"+moo_params.plugin_url+"/public/img/upload.png'>";
        cont_html +="</a>";
        cont_html +="</td>";
        cont_html +="<td class='bt-cat'>";
        cont_html +="<a href='#' class='edit_name' title='Edite Name'>";
        cont_html +="<img src='"+moo_params.plugin_url+"/public/img/edit.png'>";
        cont_html +="</a>";
        cont_html +="</td>";
        cont_html +="<td class='bt-cat'>";
        cont_html +='<a href="#" onclick="delete_img_category(event,\''+uuid+'\',\'D\')" title="Delete Image">'
        cont_html +="<img src='"+moo_params.plugin_url+"/public/img/delete.png'>";
        cont_html +="</a>";
        cont_html +="</td>";
    }
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
    var Nb_Tabs=10; // Number for tabs
    for(var i=1;i<=Nb_Tabs;i++) {
        jQuery('#MooPanel_tabContent'+i).hide();
        jQuery('#MooPanel_tab'+i).removeClass("MooPanel_Selected");
    }
    jQuery('#MooPanel_tabContent'+tab).show();
    jQuery('#MooPanel_tab'+tab).addClass("MooPanel_Selected");
    jQuery('#MooPanel_sidebar').css('min-height',jQuery('#MooPanel_main').height()+72+'px');

    if(tab==8 &&  window.moo_first_time == true)
    {
        moo_getLatLongforMapDa();
        window.moo_first_time = false;
        moo_setup_existing_zones();
    }
    jQuery("#menu_for_mobile ul").toggle();

}
function MooPanel_ImportItems(event)
{
    event.preventDefault();
    jQuery('#MooPanelSectionImport').html(window.moo_loading);
    Moo_ImportCategories();
}
var flag_key_noy_found=false;
function Moo_ImportCategories()
{
    jQuery.post(moo_params.ajaxurl,{'action':'moo_import_categories'}, function (data) {
            if(data.status == 'Success')
            {

                if(data.data == "Please verify your Key in page settings") {
                    flag_key_noy_found=true;
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
    if(!flag_key_noy_found)
        jQuery.post(moo_params.ajaxurl,{'action':'moo_import_labels'}, function (data) {
                if(data.status=='Success')
                    jQuery('#MooPanelSectionImport').append('<br/> '+data.data);
                else
                    jQuery('#MooPanelSectionImport').append('<br/> '+"Error when importing the label, please try again");
            }
        ).done(function () {
                Moo_ImportTaxes();
            });
}
function Moo_ImportTaxes()
{
    jQuery.post(moo_params.ajaxurl,{'action':'moo_import_taxes'}, function (data) {
            if(data.status=='Success')
                jQuery('#MooPanelSectionImport').append('<br/> '+data.data);
            else
                jQuery('#MooPanelSectionImport').append('<br/> '+"Error when importing the taxes rates, please try again");
        }
    ).done(function () {
            Moo_ImportItems();
        });
}
function Moo_ImportItems()
{
    jQuery.post(moo_params.ajaxurl,{'action':'moo_import_items'}, function (data) {
            if(data.status=='Success')
                jQuery('#MooPanelSectionImport').append('<br/> '+data.data);
            else
                jQuery('#MooPanelSectionImport').append('<br/> '+"Error when importing the products, please try again");
        }
    ).done(function () {
            jQuery('#MooPanelSectionImport').html("All of your data was successfully imported from Clover POS"+'<br/> ');
            moo_Update_stats();
            Moo_GetOrderTypes();
        });
}
function Moo_GetOrderTypes()
    {
        if(document.querySelector('#MooOrderTypesContent') != null)
            jQuery.post(moo_params.ajaxurl,{'action':'moo_getAllOrderTypes'}, function (data) {
            if(data.status == 'success')
            {
                var orderTypes = {};
                try {
                    orderTypes = JSON.parse(data.data);
                } catch (e) {
                    console.log("Parsing error: orderTypes");
                }
                var html='';
                html +='<div class="display_order_types_desktop"><div class="Moo_option-title">';

                var html_m ="";
                html_m += "<div class='display_order_types_mobile'><div>";

                if(orderTypes.length>0){
                    html += '<div class="label"><strong>Name</strong></div><div class="onoffswitch"><strong>Disable/Enable</strong></div>';
                    html += '<div class="onoffswitch" style="margin-left: 60px;width: 150px;">';
                    html += '<strong>Show customer address</strong></div><div style="float: right"><strong>DELETE</strong></div></div>';

                    for(var i=0;i<orderTypes.length;i++) {
                        var $ot = orderTypes[i];
                        if($ot.label == "") continue;
                        html +='<div class="Moo_option-item">';
                        html +="<div class='label'>"+($ot.label)+"</div>";
                        //enable/disable
                        html +='<div class="onoffswitch" onchange="MooChangeOT_Status(\''+$ot.ot_uuid +'\')" title="'+(($ot.status==1)?"Disable":"Enable")+' this order types">';
                        html +='<input type="checkbox" name="onoffswitch[]" class="onoffswitch-checkbox" id="myonoffswitch_'+$ot.ot_uuid+'"'+(($ot.status==1)?"checked":"")+'>';
                        html +='<label class="onoffswitch-label" for="myonoffswitch_'+$ot.ot_uuid +'">';
                        html +='<span class="onoffswitch-inner"></span> <span class="onoffswitch-switch"></span></label></div>';
                        //show shipping adress
                        html +='<div class="onoffswitch" onchange="MooChangeOT_showSa(\''+$ot.ot_uuid +'\')" style="margin-left: 100px" title="Hide/Show the shipping address for this order types">';
                        html +='<input type="checkbox" name="onoffswitch[]" class="onoffswitch-checkbox" id="myonoffswitch_sa_'+$ot.ot_uuid+'"'+(($ot.show_sa==1)?"checked":"")+'>';
                        html +='<label class="onoffswitch-label" for="myonoffswitch_sa_'+$ot.ot_uuid +'">';
                        html +='<span class="onoffswitch-inner"></span> <span class="onoffswitch-switch"></span></label></div>';
                        //delete
                        html +='<div  style="float: right"><a href="#" title="Delete this order types from the wordpress Database" onclick="Moo_deleteOrderType(event,\''+$ot.ot_uuid+'\')">DELETE</a></div></div>';

                        html_m  += "<div style='border-bottom: 1px solid'><div style='margin-bottom: 7px;'><strong>Name</strong> : "+($ot.label)+"</div>";
                        html_m +='<div style="display: block; margin-bottom: 7px;" class="" onchange="MooChangeOT_Status_Mobile(\''+$ot.ot_uuid +'\')" title="'+(($ot.status==1)?"Disable":"Enable")+' this order types"><strong>Enabled</strong> : ';
                        html_m +='<input type="checkbox" name="onoffswitch[]" class="" id="myonoffswitch_mobile_'+$ot.ot_uuid+'"'+(($ot.status==1)?"checked":"")+'>';
                        html_m +='<label class="" for="myonoffswitch_'+$ot.ot_uuid +'">';
                        html_m +='<span class=""></span> <span class=""></span></label></div>';

                        html_m +='<div class="" onchange="MooChangeOT_showSa_Mobile(\''+$ot.ot_uuid +'\')" style="margin-bottom: 7px;" title="Hide/Show the shipping address for this order types"><strong>Show customer address</strong> : ';
                        html_m +='<input type="checkbox" name="onoffswitch[]" class="" id="myonoffswitch_sa_mobile_'+$ot.ot_uuid+'"'+(($ot.show_sa==1)?"checked":"")+'>';
                        html_m +='<label class="" for="myonoffswitch_sa_'+$ot.ot_uuid +'">';
                        html_m +='<span class=""></span> <span class=""></span></label></div>';
                        html_m +='<div style="margin-bottom: 8px;"><strong>Delete</strong> : <a href="#" title="Delete this order types from the wordpress Database" onclick="Moo_deleteOrderType(event,\''+$ot.ot_uuid+'\')">DELETE</a></div></div>';
                    }
                    html += "</div>";
                    html_m += "</div>";
                }
                else
                  html = "<div class='normal_text' >You don't have any OrderTypes,<br/> please import your data by clicking on <b>Import Items</b></div>";

               document.querySelector('#MooOrderTypesContent').innerHTML = html;
               document.querySelector('#MooOrderTypesContent').innerHTML += html_m;
            }
            else
                document.querySelector('#MooOrderTypesContent').innerHTML  ="<div style='text-align: center'>Please verify your API Key<br/></div>";

        }
    );
}

function moo_Update_stats()
{
    jQuery.post(moo_params.ajaxurl,{'action':'moo_get_stats'}, function (data) {
            if(data.status=='Success'){
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

function MooChangeOT_Status(uuid)
{
    var ot_status = jQuery('#myonoffswitch_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_ot_status',"ot_uuid":uuid,"ot_status":ot_status}, function (data) {
           console.log(data);
        }
    );
}
function MooChangeOT_Status_Mobile(uuid)
{
    var ot_status = jQuery('#myonoffswitch_mobile_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_ot_status',"ot_uuid":uuid,"ot_status":ot_status}, function (data) {
            console.log(data);
        }
    );
}
function MooChangeOT_showSa_Mobile(uuid)
{
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
    if(label == "") alert("Please enter a label for your order Type")
    else
    {
        jQuery('#Moo_AddOT_loading').html(window.moo_loading);
        jQuery('#Moo_AddOT_btn').hide();

        jQuery.post(moo_params.ajaxurl,{'action':'moo_add_ot',"label":label,"taxable":taxable}, function (data) {
            if(data.status=='success')
            {
                if(data.message == '401 Unauthorized') jQuery('#Moo_AddOT_loading').html('Verify your API key');
                else
                {
                    Moo_GetOrderTypes();
                    jQuery('#Moo_AddOT_loading').html('');
                    jQuery('#Moo_AddOT_btn').show();
                }

            }
            else
            {
                jQuery('#Moo_AddOT_loading').html('Verify your API key');
            }


            }
        );
    }

}
function Moo_deleteOrderType(e,uuid)
{
    e.preventDefault();
    jQuery.post(moo_params.ajaxurl,{'action':'moo_delete_ot',"uuid":uuid}, function (data) {
            Moo_GetOrderTypes();
        }
    );
}

function MooSendFeedBack(e)
{
    e.preventDefault();
    var msg =  jQuery("#Moofeedback").val();
    var email =  jQuery("#MoofeedbackEmail").val();
    if(msg == '')
    {
        alert("Please enter your message");

    }
    else
    {
        jQuery("#MooSendFeedBackBtn").hide();
        jQuery.post(moo_params.ajaxurl,{'action':'moo_send_feedback','message':msg,'email':email}, function (data) {
            if(data.status == "Success"){
                alert("Thank you for your feedback.");
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
function MooChangeCategory_Status(uuid)
{
    var cat_status = jQuery('#myonoffswitch_'+uuid).prop('checked');
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_category_status',"cat_uuid":uuid,"cat_status":cat_status}, function (data) {
            console.log(data);
        }
    );
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

function uploader_image_category(enent,id,responsive){
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
                alert("Error when saving your changes, please try again")
        });
    }
    else
    {
        history.back();
    }
}
function open_media_uploader_image() {
    console.log("open_media_uploader_image");
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
        if (a1 == 1) {
            tag = "<input id='image_default_id_"+i+"' onchange='moo_default_item_image("+i+")' type='radio' name='image_default' value='image_default' checked><label style='position: relative; top: -4px; right: -10px;' for='image_default_id_"+i+"'>Default Image</label>";
        } else {
            tag = "<input id='image_default_id_"+i+"' onchange='moo_default_item_image("+i+")' type='radio' name='image_default' value='image_default'><label style='position: relative; top: -4px; right: -10px;' for='image_default_id_"+i+"'>Default Image</label>";
        }
        if (b1 == 1) {
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
                    '<div class="image_options_holder"><div><a href="#" onclick="moo_delete_item_images(\''+i+'\')">Delete</a></div>'+
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
    console.log('I am here');
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
    console.log('moo_save_item_images');
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
    if(description.length>250) {
        alert("Description too long");
        return
    }
    if(description != "" || Object.keys(moo_item_images).length>=0) {
        jQuery.post(moo_params.ajaxurl,{'action':'moo_save_items_with_images',"item_uuid":uuid,"description":description,"images":images}, function (data) {
            if(data.status == 'Success') {
                if(data.data==true) {
                    alert("Your changes were saved");
                    history.back();
                } else alert("Error when saving your changes, please try again");
            } else alert("Error when saving your changes, please try again");
        }
        );
    } else history.back();
}

function moo_get_item_with_images(uuid) {
    console.log(moo_item_images);
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
        jQuery('#moo_item_description').val(items[0].description);
        jQuery('#moo_item_name').text(items[0].name);
        jQuery('#moo_item_price').text("$"+items[0].price/100);
    });
}
/*End upload Functions*/

function MooPanel_UpdateItems(event) {
    event.preventDefault();
    window.bar.animate(0.01);
    window.bar.setText('1 %');
    moo_upadateItemsPerPage(0)
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
            alert("Categories updated");
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
            alert("Modifiers updated");
            window.bar.animate(1.0);
            window.bar.setText('100 %');

        });

    });
}
function moo_upadateItemsPerPage(page)
{
    var received = 0;
    jQuery.post(moo_params.ajaxurl,{'action':'moo_update_items','page':page}, function (data)
    {
        received = data.received;
        var percent_loaded = data.received*100/window.moo_nb_allItems;
        if(percent_loaded == null)
            percent_loaded = 1;
        window.bar.animate(bar.value()+percent_loaded/100);
        window.bar.setText(Math.round(percent_loaded+bar.value()*100) + ' %');
    }
    ).done(function () {
        if(received>0)
            moo_upadateItemsPerPage(page+1)
        else
        {
            alert("Items updated");
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
