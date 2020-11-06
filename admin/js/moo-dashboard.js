jQuery(document).ready(function($) {
    //Load the content of the opened tab
    mooLoadInstalledThemes();
    //$(".moo_dashboard_inputColor").wpColorPicker();
    //console.log("hello world from dashboard")
});
function moo_dashboard_tab_clicked(tab) {
    var Nb_Tabs=2; // Number for tabs
    for(var i=1;i<=Nb_Tabs;i++) {
        jQuery('#mooDashbboardTabContent'+i).hide();
        jQuery('#mooDashbboardTab'+i).removeClass("active");
    }
    jQuery('#mooDashbboardTabContent'+tab).show();
    jQuery('#mooDashbboardTab'+tab).addClass("active");
    mooLoadDashboardTabContent(tab);
}

function mooLoadDashboardTabContent(tab) {
    if(tab === 1 ) {
        if(jQuery('#mooDashbboardTabContent1').html() == '' ) {
            mooLoadInstalledThemes();
        }
    } else {
        if(tab === 2) {
            mooLoadAllThemes();
        }else {
            if(tab === 3) {

            }else {

            }
        }
    }
}

function mooLoadInstalledThemes() {
    jQuery.get(moo_RestUrl+"moo-clover/v1/dashboard/installed_themes", function (response) {
        if(response !== null) {
           if(response.status == 'ok') {
               var html = '';
               response.data.forEach(function(element) {
                   html += mooRenderHtmlOneTheme(element,'activate');
               });
               jQuery('#mooDashbboardTabContent1').html(html);
               //console.log(html);
           }
        } else {
            swal({ title: "Error", text: 'We cannot Load the themes, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
        }
    }).fail(function (data) {
        //Change butn text
        swal({ title: "Error", text: 'We cannot Load the themes, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
    });

}
function mooLoadAllThemes() {
    swal({
        title: 'Please wait ..',
        showConfirmButton: false
    });
    jQuery.get(moo_RestUrl+"moo-clover/v1/dashboard/all_themes", function (response) {
        if(response !== null) {
           if(response.status == 'ok') {
               var html = '';
               response.data.forEach(function(element) {
                   html += mooRenderHtmlOneTheme(element,'install');
               });
               jQuery('#mooDashbboardTabContent2').html(html);
           } else {
               jQuery('#mooDashbboardTabContent2').html("<div class='moo_dashboard_text_error' style='margin-top: 80px;'>There are no new Store Interfaces at this time, please check back later<p>Are you a developer and would like to make a Store Interface for Smart Online Order? click <a href='https://docs.zaytech.com'>Here</a> for Documentation</p></div>");
           }
           swal.close();
        } else {
            swal({ title: "Error", text: 'We cannot Load the themes, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
        }
    }).fail(function (data) {
        //Change butn text
        swal({ title: "Error", text: 'We cannot Load the themes, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
    });
}

function mooRenderHtmlOneTheme(theme,activeOrInstall) {
    var html = '';


    if(theme.is_active){
        html += '<div class="moo_dashboard_addon_box"  style="border: 2px solid #1F3C71;"><div class="moo_dashboard_addon_box_thumb">';
        html += '<div class="moo_dashboard_addon_box_led new"><i class="fas fa-star"></i>Activated</div>';
    } else {
        html += '<div class="moo_dashboard_addon_box"><div class="moo_dashboard_addon_box_thumb">';
    }
        html += '<img src="'+theme.screenshots+'">'+
                '<a target="_blank" href="" title="More details"></a> </div>'+
                '<div class="moo_dashboard_addon_box_title">'+theme.name+'</div>'+
                '<div class="moo_dashboard_addon_box_excerpt">'+
                '<p class="moo_dashboard_addon_box_excerpt_content">'+theme.description+'</p>'+
                '<p class="moo_dashboard_addon_box_links">';
                //'<a target="_blank" class="moo_dashboard_addon_link pull-left" href="">Documentation</a>' ;
        if(activeOrInstall === 'activate') {
            html += '<a class="moo_dashboard_addon_link pull-right " href="?page=moo_themes&theme_identifier='+theme.identifier+'">Customize</a>' ;
        }
        html += '</p></div><div class="moo_dashboard_addon_box_action">';
                if(activeOrInstall === 'activate') {
                    if(theme.is_active) {
                        html += '<a href="#" onclick="event.preventDefault()" class="moo_dashboard_addon_box_button moo_dashboard_submit_button moo_dashboard_addon_manage">'+
                            '<div>'+
                            '<span>ACTIVATED</span></div></a>';
                    } else {
                        html += '<a href="#" onclick="mooChooseTheme(event,\''+theme.identifier+'\')" class="moo_dashboard_addon_box_button moo_dashboard_submit_button moo_dashboard_addon_manage">'+
                            '<div>'+
                            '<span>ACTIVATE</span></div></a>';
                    }

                } else {
                    html += '<a href="#" onclick="mooInstallTheme(event,\''+theme.identifier+'\')" class="moo_dashboard_addon_box_button moo_dashboard_submit_button moo_dashboard_addon_manage">'+
                        '<div>'+
                        '<span>INSTALL</span></div></a>';
                }

                html +=  '</div></div>';
    return html;
}
function mooChooseTheme(event,identifier) {
    if(event !== undefined) {
        event.preventDefault();
    }
    swal({
        title: 'Please wait ..',
        showConfirmButton: false
    });
    jQuery.ajax({
        type: 'POST',
        url: moo_RestUrl+"moo-clover/v1/dashboard/installed_themes",
        contentType: 'application/json; charset=UTF-8',
        beforeSend: function(jqXhr) {
            jqXhr.setRequestHeader('X-WP-Nonce', moo_params.nonce)
        },
        data: JSON.stringify({"theme":identifier})
    }).fail(function (data) {
        //Change butn text
        swal({ title: "Error", text: 'We cannot change the theme, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
    }).done(function (data) {
        if(data.status == "success"){
            mooLoadInstalledThemes();
            swal({
                title: "Store Interface Changed",
                text: 'The store interface changed',
                type: "success",
                confirmButtonText: "ok"
            });

        } else {
            swal({
                title: "Error",
                text: 'Try again',
                type: "error",
                confirmButtonText: "ok"
            });
        }
    });

}
function mooChangedInputColorValue(id){
    var value = jQuery('#'+id).val();
    jQuery('#'+id+"_val").val(value);
}
function mooChangedInputColorTextValue(id){
    var value = jQuery('#'+id+"_val").val();
    jQuery('#'+id).val(value);
}

function moo_save_theme_customization(event,theme) {
    event.preventDefault();
    var form = jQuery("#moo_theme_customize").serializeArray();
    //send post request to save new settings
    swal({
        title: 'Please wait ..',
        showConfirmButton: false
    });
    jQuery.ajax({
        type: 'POST',
        url: moo_RestUrl+"moo-clover/v1/theme_settings/"+theme,
        contentType: 'application/json; charset=UTF-8',
        beforeSend: function(jqXhr) {
            jqXhr.setRequestHeader('X-WP-Nonce', moo_params.nonce)
        },
        data: JSON.stringify(form)
    }).fail(function (data) {
        //Change butn text
        swal({ title: "Error", text: 'Settings not saved, please refresh the page or contact us',   type: "error",   confirmButtonText: "ok" });
    }).done(function (data) {
        if(data.status == "success"){

            swal({
                    title: 'New settings were saved',
                    type: "success",
                    confirmButtonColor: "#0333dd",
                    confirmButtonText: "Ok"
                });

        } else {
            swal({
                title: "'Settings not saved",
                text: 'Please Try again',
                type: "error",
                confirmButtonText: "ok"
            });
        }
    });
}