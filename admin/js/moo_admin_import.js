jQuery(function ()
{
    window.moo_imported_categories = false;
    window.moo_imported_labels = false;
    window.moo_imported_taxes = false;
    window.moo_imported_items = false;

    window.moo_importing = false;

    window.moo_loading = '<svg xmlns="http://www.w3.org/2000/svg" width="44px" height="44px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-default"><rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(0 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(30 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.08333333333333333s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(60 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.16666666666666666s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(90 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.25s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(120 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.3333333333333333s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(150 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.4166666666666667s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(180 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.5s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(210 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.5833333333333334s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(240 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.6666666666666666s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(270 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.75s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(300 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.8333333333333334s" repeatCount="indefinite"></animate></rect><rect x="46.5" y="40" width="7" height="20" rx="5" ry="5" fill="#00b2ff" transform="rotate(330 50 50) translate(0 -30)">  <animate attributeName="opacity" from="1" to="0" dur="1s" begin="0.9166666666666666s" repeatCount="indefinite"></animate></rect></svg>';


    jQuery("#wizard").steps({
        headerTag: "h2",
        bodyTag: "section",
        transitionEffect: "slideLeft",
        stepsOrientation: "vertical",
        onStepChanging: function (event, currentIndex, newIndex) {
            console.log(currentIndex);
            switch (currentIndex){
                case 0: if(currentIndex<newIndex)
                            if(!window.moo_imported_categories) {
                                    if(!window.moo_importing) Moo_ImportCategories();
                                    return false;
                                }
                            else return true;
                         else return true;
                        break;
                case 1:if(currentIndex<newIndex)
                            if(!window.moo_imported_labels) {
                                if(!window.moo_importing) Moo_ImportLabels();
                                return false;
                            }
                            else return true;
                        else return true;
                        break;

                case 2:if(currentIndex<newIndex)
                            if(!window.moo_imported_taxes) {
                                if(!window.moo_importing) Moo_ImportTaxes();
                                return false;
                            }
                            else return true;
                        else return true;
                            break;
                    ;
            }
        },
        onFinishing: function (event, currentIndex) {
            console.log(currentIndex);

                        if(!window.moo_imported_items) {
                            if(!window.moo_importing) Moo_ImportItems();
                            return false;
                        }
                        else return true;
        }
    });

    function Moo_ImportCategories()
{
    window.moo_importing=true;
    jQuery('#moo_import_categories').html(window.moo_loading);

    jQuery.post(moo_params.ajaxurl,{'action':'moo_import_categories'}, function (data) {
            window.moo_imported_categories=true;
            if(data.status=='Success')
                jQuery('#moo_import_categories').html(data.data);
            else
                jQuery('#moo_import_categories').html("refresh the page and try again");
        }
    ).done(function () {
            window.moo_importing=false;
        });
}

    function Moo_ImportLabels()
{
    window.moo_importing=true;
    jQuery('#moo_import_labels').html(window.moo_loading);

    jQuery.post(moo_params.ajaxurl,{'action':'moo_import_labels'}, function (data) {
            window.moo_imported_labels=true;
            if(data.status=='Success')
                jQuery('#moo_import_labels').html(data.data);
            else
                jQuery('#moo_import_labels').html("refresh the page and try again");
        }
    ).done(function () {
            window.moo_importing=false;
        });
}
    function Moo_ImportTaxes()
{
    window.moo_importing=true;
    jQuery('#moo_import_taxes').html(window.moo_loading);

    jQuery.post(moo_params.ajaxurl,{'action':'moo_import_taxes'}, function (data) {
            window.moo_imported_taxes=true;
            if(data.status=='Success')
                jQuery('#moo_import_taxes').html(data.data);
            else
                jQuery('#moo_import_taxes').html("refresh the page and try again");
        }
    ).done(function () {
            window.moo_importing=false;
        });
}
    function Moo_ImportItems()
{
    window.moo_importing=true;
    jQuery('#moo_import_items').html(window.moo_loading);

    jQuery.post(moo_params.ajaxurl,{'action':'moo_import_items'}, function (data) {
            window.moo_imported_items=true;
            if(data.status=='Success')
                jQuery('#moo_import_items').html(data.data);
            else
                jQuery('#moo_import_items').html("refresh the page and try again");
        }
    ).done(function () {
            window.moo_importing=false;

        });
}

});