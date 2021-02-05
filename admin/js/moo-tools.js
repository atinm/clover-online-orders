jQuery(document).ready(function() {
    window.moo_RestUrl = moo_params.moo_RestUrl;
});
function moo_change_api_key(event) {
    if(event !== undefined) {
        event.preventDefault();
    }
    var new_api_key = "";
    swal({
        title: 'Please wait this operation may take few minutes',
        showConfirmButton: false
    });
    jQuery.ajax({
        type: 'POST',
        url: moo_RestUrl+"moo-clover/v1/dashboard/change_api_key",
        contentType: 'application/json; charset=UTF-8',
        beforeSend: function(jqXhr) {
            jqXhr.setRequestHeader('X-WP-Nonce', moo_params.nonce)
        },
        data: JSON.stringify({"api_key":new_api_key})
    }).fail(function (data) {
        swal({ title: 'You cannot change the api key',text:data.message,   type: "error",   confirmButtonText: "ok" });
    }).done(function (data) {
        if(data.status == "success"){
            swal({
                title: "Great",
                text: 'Your api key changed',
                type: "success",
                confirmButtonText: "ok"
            });

        } else {
            swal({
                title: "Error",
                text: data.message,
                type: "error",
                confirmButtonText: "ok"
            });
        }
    });
}