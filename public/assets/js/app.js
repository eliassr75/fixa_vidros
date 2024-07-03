function formatString(value, mask, params={}) {

    if (!value){
        return 'Não informado'
    }

    let $tempInput = $('<input>').val(value).mask(mask, params);
    return $tempInput.val();
}

function toast_alert(response, title=null){

    let toastTheme = "";
    switch (response.status) {
        case "success":
            toastTheme = "primary";
            break;
        case "warning":
            toastTheme = "warning";
            break;
        case "error":
            toastTheme = "danger";
            break;
        default:
            toastTheme = "info";
            break;
    }

    if(response.message){
        $('#toast-container').html(`
            <div id="toast" class="toast-box toast-bottom bottom-0 bg-${toastTheme}">
                <div class="in">
                    <div class="text">
                        ${response.message}
                    </div>
                </div>
            </div>
        `);

        toastbox('toast', 3000)
    }

    if (response.url){
        setTimeout(() => {
            window.location.href = response.url;
        }, 1500)
    }

    if (response.reload){
        setTimeout(() => {
            window.location.reload()
        }, 1500)
    }

}


async function sendGlobalAjax(params, progressBar=false) {
    try {
        return await $.ajax(params);
    } catch (error) {

        console.log(error)

        if (error.responseJSON){
            toast_alert(error.responseJSON)
        }else{
            toast_alert({
                "status": "error",
                "message": "Erro ao processar o formulário"
            });
            console.log("Erro ao processar o formulário:", error.responseText);
        }


        if(progressBar){
            progressBar.addClass('bg-danger')
        }

        return false;
    }
}


/* Eemple Usage: data-method="PUT" data-action="{{ request.path }}" data-ajax="default" data-callback="get_datails" */
function processForm(form=false, params=false, callbackName=false, callbackParams=false) {

    let progressBar = $("#progressBar");
    let progressDiv = $("#progressDiv");
    let progressContainer = $("#progressContainer");

    if (!form && !params){
        toast_alert({
            "status": "error",
            "message": "A requisição não pode ser iniciada, pois é obrigatório um formulário e/ou paraâmetros personalizados."
        });
        return
    }

    if (form){

        const method = $(form).data("method");
        const url = $(form).data("action");
        if (!callbackName){
            callbackName = $(form).data("callback")
        }

        if(!callbackParams){
            callbackParams = $(form).data("callbackParams")
        }

        const formDataMethod = ['POST', 'PUT']

        if (!params){
            params = {
                method: method,
                url: url,
                dataType: 'json',
            }
        }

        let formDataValues = null
        if (formDataMethod.includes(method.toUpperCase())){
            formDataValues = new FormData($(form)[0]);
            params.processData = false;
            params.contentType = false;
        }else{
            formDataValues = $(form).serialize();
        }

        params.data = formDataValues;
    }

    params.beforeSend = function() {

        window.bkp_html = $('.btn-submit').html()

        $('.btn-submit').html(`
            <div class="spinner-border text-primary" role="status"></div>
        `).addClass("disabled")
        progressContainer.removeClass('bg-danger').show(500);
        progressBar.removeClass('bg-danger');
        progressDiv.prop('aria-valuenow', '0');
        progressBar.css("width", "0%");
    }

    params.xhr = function() {
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(event) {
            if (event.lengthComputable) {
                var percentComplete = Math.round((event.loaded / event.total) * 100);
                progressBar.css("width", percentComplete + "%");
                progressDiv.prop('aria-valuenow', percentComplete)
            }
        }, false);
        return xhr;
    }

    params.complete = function(response) {
        $('.btn-submit').html(window.bkp_html).removeClass("disabled")

        if (callbackName && typeof window[callbackName] === 'function') {
            window[callbackName](response, callbackParams);
        }

        setTimeout(function() {
            progressContainer.hide(500);
        }, 2000);
    }

    sendGlobalAjax(params, progressBar).then(function (response){
        toast_alert(response);
    })
}


$(document).ready(() => {

    $("#progressContainer").hide()

    $("form").on('submit', function (e){

        let form = $(this);
        const ajaxMod = $(form).data('ajax')
        if (ajaxMod === "default"){

            e.preventDefault();
            processForm(form);

        }
    })
})
