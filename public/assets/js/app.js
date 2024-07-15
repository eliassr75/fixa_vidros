const spinner = `<div class="loader"></div>`;

let configDataTable = {
    stateSave: true,
    responsive: true,
    language: {
        searchPlaceholder: 'Faça uma pesquisa nesta página',
        zeroRecords: "Não encontramos resultados...",
        sSearch: '',
        sLengthMenu: '_MENU_',
        sLength: 'dataTables_length',
        info: 'Total de Registros: _TOTAL_',
        infoFiltered: '(Filtrado de _MAX_ resultados)',
        infoEmpty: "Total de Registros: _TOTAL_",
        oPaginate: {
            sFirst: '<ion-icon name="arrow-back-circle-outline"></ion-icon>',
            sPrevious: '<ion-icon name="arrow-back-circle-outline"></ion-icon>',
            sNext: '<ion-icon name="arrow-forward-circle-outline"></ion-icon>',
            sLast: '<ion-icon name="arrow-forward-circle-outline"></ion-icon>'
        }
    },
    order: false,
    lengthChange: false,
    autoWidth: false,
    paging: false
}


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

function input_phone_number(locale){

    $(`input[type="tel"]`).prop("disabled", true).parent().parent().append(`
        <input id="ddi-phone-number" name="ddi-phone-number" type="hidden">
        <input id="country-phone-number" name="country-phone-number" type="hidden">
    `)

    switch(locale){
        case 'es':
            locale = "es"
            $("#ddi-phone-number").val("+34")
            break
        case 'pt':
            locale = "br"
            $("#ddi-phone-number").val("+55")
            break
        case 'en':
            locale = "us"
            $("#ddi-phone-number").val("+1")
            break
        default:
            locale = "br"
            $("#ddi-phone-number").val("+55")
            break
    }
    const input = document.querySelector('input[type="tel"]');
    const iti = window.intlTelInput(input, {

        initialCountry: locale,
        showSelectedDialCode: true,
        utilsScript: "/assets/js/plugins/intlTelInput/build/js/utils.min.js",
    });

    iti.promise.then(() => {
        $(`input[type="tel"]`).prop('disabled', false)
    });
}

function actionForm(action){

    let global_action_sheet_content = $('#global-action-sheet-content');
    let global_action_sheet_title = $('#global-action-sheet-title');
    let body = ``;

    switch (action){
        case 'addUser':

            //For legends
            window.permissions = window.systemPermissions

            let values_select = ""
            for (let permission of window.systemPermissions){
                values_select += `
                    <option value="${permission.id}">${permission.name}</option>
                `
            }

            let values_select_language = ""
            let languages = [
                {language: "pt", label: locale.language_system_pt},
                {language: "en", label: locale.language_system_en},
                {language: "es", label: locale.language_system_es}
            ]

            for (let language of languages){
                values_select_language += `
                    <option value="${language.language}">${language.label}</option>
                `
            }

            global_action_sheet_title.html(locale.create_account);
            body = `
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name">${locale.input_name}</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="${locale.input_name}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="username">${locale.input_email}</label>
                        <input type="email" class="form-control" id="username" name="username" autocomplete="username" placeholder="${locale.input_email}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                
                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label" for="select-permission">${locale.permission_level}</label>
                        <select class="form-control custom-select" id="select-permission" name="permission" onchange="setLegend(this.value)" required>
                            <option value="" selected disabled>Selecione uma opção</option>
                            ${values_select}
                        </select>
                        <label class="text-warning label mt-1" id="legend-permission"></label>
                    </div>
                </div>
    
                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label" for="select-language">${locale.language_system}</label>
                        <select class="form-control custom-select" id="select-language" name="language" required>
                            <option value="" selected disabled>Selecione uma opção</option>
                            ${values_select_language}
                        </select>
                        <label class="text-warning label mt-1" id="legend-permission"></label>
                    </div>
                </div>
                
                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="suggestPassword" name="suggestPassword" checked>
                    <label class="form-check-label" for="suggestPassword">${locale.generate_password}</label>
                </div>

                <div class="form-group basic password-wrapper" hidden>
                    <div class="input-wrapper">
                        <label class="label" for="password">${locale.input_password}</label>
                        <input type="password" class="form-control" id="password" name="password" onkeyup="checkPassword()"
                        autocomplete="new-password" placeholder="${locale.input_password}">
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <div class="form-group basic password-wrapper" hidden>
                    <div class="input-wrapper">
                        <label class="label" for="new-password">${locale.input_confirm_password}</label>
                        <input type="password" class="form-control" id="confirm-password" name="confirm-password" onkeyup="checkPassword()"
                               autocomplete="new-password" placeholder="${locale.input_confirm_password}">
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <div class="custom-alert my-2"></div>
            `;

            break;
    }

    global_action_sheet_content.html(`
    <form data-method="POST" data-action="/new-account/" data-ajax="default" data-callback="">
        <input type="hidden" name="generate-link" value="1">
        
        ${body}
        <div class="row mt-2">
            <div class="col-6">
                <button type="button" class="btn btn-lg btn-cancel btn-outline-secondary btn-block" data-bs-dismiss="modal">
                    ${locale.label_btn_cancel}
                </button>
            </div>
            <div class="col-6">
                <button type="submit" class="btn btn-lg btn-primary btn-block btn-submit">
                    ${locale.label_btn_save}
                </button>
            </div>
        </div>
    </form>
    `);

    $("#suggestPassword").on("change", function(){

        if($(this).is(':checked')){
            $(".password-wrapper").prop('hidden', true)
            $('input[type="password"]').prop('required', false).val("")
        }else{
            $(".password-wrapper").prop('hidden', false)
            $('input[type="password"]').prop('required', true)
        }

    })

    $("form").on('submit', function (e){

        let form = $(this);
        const ajaxMod = $(form).data('ajax')
        if (ajaxMod === "default"){

            e.preventDefault();
            processForm(form);

        }
    })

}

function close_global_modal(){
    $('.modal').modal('hide')
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

    const alert_html = `

    <div class="alert alert-imaged alert-${type} alert-dismissible fade show mb-2" id="global-alert-container" role="alert">
        <div class="icon-wrap">
            <ion-icon name="${icon}" role="img" class="md hydrated" aria-label="${icon}"></ion-icon>
        </div>
        <div>
            ${response.message}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
    `;

    if (document.querySelector('#child-global-custom-alert') && !window.use_global_alert) {
        $('#child-global-custom-alert').html(alert_html).hide().show(500)
    }else{
        $('#global-custom-alert').html(alert_html).hide().show(500)
    }

    if(time){

        setTimeout(() => {

            close_global_modal();

            let bsAlert = new bootstrap.Alert('#global-alert-container')
            if (document.querySelector('#child-global-custom-alert') && !window.use_global_alert) {
                bsAlert = new bootstrap.Alert('#child-global-custom-alert')
            }
            window.use_global_alert = false;
            bsAlert.close()
            $('.appHeader').show()

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
            _alert("alert-circle-outline", locale.password_verify_length, "danger")
        }

    } else {
        _alert("alert-circle-outline", locale.password_verify_match, "danger")
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

function change(url=false, id=false, el=false){

    const url_call = `${url}${id}/`;
    let prop = false

    if(el){

        $('[id^="SwitchCheckUser"]').each(function() {
            let el = this
            prop = $(el).is(':checked')
            if (el.value === id){
                window.SwitchCheckUser = id
                $(el).prop('checked', !prop).prop('disabled', true)
            }else{
                $(el).prop('disabled', true)
            }

        });

        params = {
            method: "PUT",
            url: `${url_call}`,
            dataType: 'json',
        }
        window.use_global_alert = true;
        processForm(false, params, 'change', false, false, false)

    }else{

        $('[id^="SwitchCheckUser"]').each(function() {
            let el = this
            prop = $(el).is(':checked')

            if (el.value === window.SwitchCheckUser){
                $(el).prop('checked', !prop).prop('disabled', false);
            }else{
                $(el).prop('disabled', false);
            }
        });
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
function processForm(form=false, params=false, callbackName=false, callbackParams=false, custom_spinner=false, progresBarEnabled=false) {

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

        if (custom_spinner){
            $("#section-animation").html(spinner)
        }

        window.bkp_html = $('.btn-submit').html()
        $('.btn-submit').html(spinner).addClass("disabled")

        $('.appHeader').hide()
        progressContainer.css('z-index', 9999)

        if(progresBarEnabled){
            progressContainer.removeClass('bg-danger').show(500);
        }

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
            if (custom_spinner){
                $("#section-animation").hide(500).html("")
            }
        }, 2000);
    }

    sendGlobalAjax(params, progressBar).then(function (response){
        toast_alert(response);
    })
}

function get_cep(cep){

    if (cep.length === 9){
        _alert("alert-circle-outline", "Buscando informações...", "primary")
        $.ajax({
            url: `https://viacep.com.br/ws/${cep.replace('-', '')}/json/`,
            success:function (data){
                auto_remove_alert(0)
                console.log(data)

                $("#zone").val((data.bairro ? data.bairro : "")).trigger('change')
                $("#complement").val((data.complemento ? data.complemento : "")).trigger('change')
                $("#address").val((data.logradouro ? data.logradouro : "")).trigger('change')
                $("#state").val((data.uf ? data.uf : "")).trigger('change')
                $("#city").val((data.localidade ? data.localidade : "")).trigger('change')
                $("#phone_number").val((data.ddd ? data.ddd : "")).trigger('change')

            },
            error: function (error){
                console.log(error)
            }
        })
    }else{
        _alert("alert-circle-outline", "CEP inválido!", "danger")
    }

}


$(document).ready(() => {

    $("#progressContainer").hide()

    $(".select-2").select2({
        placeholder: locale.select_2_placeholder,
        language: "pt",
        dropdownParent: $('.modal')
    });

    $("form").on('submit', function (e){

        let form = $(this);
        const ajaxMod = $(form).data('ajax')
        if (ajaxMod === "default"){

            e.preventDefault();
            processForm(form);

        }
    })
})
