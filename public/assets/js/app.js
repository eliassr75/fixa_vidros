const spinner = `<div class="spinner-border text-white" role="status"></div>`;

function _alert(icon, message, type){

    $('.custom-alert').html(`

    <div class="alert alert-imaged alert-${type} alert-dismissible fade show mb-2" role="alert">
        <div class="icon-wrap">
            <ion-icon name="${icon}" role="img" class="md hydrated" aria-label="${icon}"></ion-icon>
        </div>
        <div>
            ${message}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
    `)

}

function global_alert(response, time){

    let type = "";
    let icon = "";
    switch (response.status) {
        case "success":
            type = "primary";
            icon = `alert-circle-outline`;
            break;
        case "warning":
            type = "warning";
            icon = `warning-outline`
            break;
        case "error":
            type = "danger";
            icon = `close-circle`;
            break;
        default:
            type = "info";
            icon = `information-circle-outline`;
            break;
    }

    $('#global-custom-alert').html(`

    <div class="alert alert-imaged alert-${type} alert-dismissible fade show mb-2" id="global-alert-container" role="alert">
        <div class="icon-wrap">
            <ion-icon name="${icon}" role="img" class="md hydrated" aria-label="${icon}"></ion-icon>
        </div>
        <div>
            ${response.message}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
    `)

    if(time){
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert('#global-alert-container')
            bsAlert.close()
        }, time*1000)

    }

}


function dialog(response, time) {

    let dialogTheme = "";
    let icon = "";
    switch (response.status) {
        case "success":
            dialogTheme = "primary";
            icon = `<ion-icon name="alert-circle-outline"></ion-icon>`;
            break;
        case "warning":
            dialogTheme = "warning";
            icon = `<ion-icon name="warning-outline"></ion-icon>`
            break;
        case "error":
            dialogTheme = "danger";
            icon = `<ion-icon name="close-circle"></ion-icon>`;
            break;
        default:
            dialogTheme = "info";
            icon = `<ion-icon name="information-circle-outline"></ion-icon>`;
            break;
    }

    $('#dialog-container').html(`
        <div class="modal fade dialogbox" id="DialogIconed" data-bs-backdrop="static" tabIndex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-icon text-${dialogTheme}">
                        ${icon}
                    </div>
                    <div class="modal-header"></div>
                    <div class="modal-body">
                        ${response.message}
                    </div>
                    <div class="modal-footer">
                        <div class="btn-inline">
                            <a href="#" class="btn" data-bs-dismiss="modal">OK</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `)

    if(time){
        auto_remove_alert(time)
    }

    $("#DialogIconed").modal('toggle')

}

function auto_remove_alert(time) {
    setTimeout(() => {
        $('.custom-alert').hide(250)
    }, time * 1000)

}

function checkPassword() {

    const password = $("input#password").val()
    const new_password = $("input#confirm-password").val()

    if (password === new_password) {

        if (password.length >= 8 && new_password.length >= 8) {
            $('.btn-submit').attr("disabled", false)
            auto_remove_alert(0)
        } else {
            _alert("alert-circle-outline", "A deve ter no mínimo 8 caracteres!", "danger")
        }

    } else {
        _alert("alert-circle-outline", "As senhas devem ser iguais!", "danger")
        $('.btn-submit').attr("disabled", true)
    }
}

function formatString(value, mask, params = {}) {

    if (!value) {
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

    let timer = 1.5;
    let timer_global_alert = 3;
    if(response.custom_timer){
        timer = response.custom_timer
        timer_global_alert = response.custom_timer
    }

    if(response.message){

        if(response.dialog){
            dialog(response);
        }else{
            //$('#toast-container').html(`
            //    <div id="toast" class="toast-box toast-top top-0 bg-${toastTheme}">
            //        <div class="in w-100">
            //            <div class="text w-100">
            //                ${response.message}
            //            </div>
            //        </div>
            //    </div>
            //`);
            //toastbox('toast', 3000)
            global_alert(response, timer_global_alert)
        }

    }

    if(response.spinner){
        $('.btn-submit').html(spinner).attr("disabled", true)
    }

    if (response.url) {
        setTimeout(() => {
            window.location.href = response.url;
        }, timer*1000)
    }

    if (response.reload){
        setTimeout(() => {
            window.location.reload()
        }, timer*1000)
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
            ${spinner}
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
