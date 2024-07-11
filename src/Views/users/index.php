<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section mt-2">

    <div class="card">

        <div id="users">

            <!-- class="search" automagically makes an input a search field. -->
            <div class="form-group boxed px-3">
                <div class="input-wrapper">
                    <input type="text" class="search form-control" id="search" placeholder="<?=$functionController->locale('input_search')?>">
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>
            <!-- class="sort" automagically makes an element a sort buttons. The date-sort value decides what to sort by. -->
            <!--
            <button class="sort btn btn-primary" data-sort="name">
                Sort
            </button>
            -->

            <ul class="listview image-listview inset list mt-2">
                <?php foreach ($users as $user): ?>
                <li id="li-model">
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#actionSheetForm" onclick="userController(false, <?=$user->id?>)" class="item">
                        <img src="/assets/img/sample/avatar/do-utilizador.png" alt="image" class="image">
                        <div class="in">
                            <div>
                                <header class="permission"><?=$user->current_permission?></header>
                                <span class="name"><?=$user->name?></span>
                                <footer class="str_created">
                                    <?=$user->str_created?>
                                </footer>
                            </div>
                            <span class="text-muted">
                                <?=$functionController->locale('action_edit')?>
                            </span>
                        </div>

                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="modal fade modalbox" id="actionSheetForm" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <a href="#" data-bs-dismiss="modal" class="btn-close btn-close-white">
                            </a>
                        </div>
                        <div class="modal-body">
                            <div id="child-global-custom-alert" class="my-2"></div>
                            <div class="action-sheet-content" id="user-info">
                                <div id="section-animation" class="w-100 d-flex justify-content-center"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script>

    function setLegend(permission_id){
        for (let permission of window.permissions) {
            if(permission.id == permission_id) {
                $('#legend-permission').html(permission.description)
            }
        }
    }

    function userController(response=false, id=false){

        if (id){

            $(".modal-title").html('')

            params = {
                method: "GET",
                url: `${window.location.pathname}json/${id}`,
                dataType: 'json',
            }
            processForm(false, params, "userController", false, true)
        }

        if (response){
            response = response.responseJSON;
            console.log(response)

            $(".modal-title").html(response.user.name)

            let fields = ``

            fields += `

            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="name">${locale.input_name}</label>
                    <input type="text" class="form-control" id="name" name="name" value="${response.user.name}" placeholder="${locale.input_name}" required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>

            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="email">${locale.input_email}</label>
                    <input type="email" class="form-control" id="email" name="email" value="${response.user.email}" placeholder="${locale.input_email}" required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>

            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="username">${locale.input_username}</label>
                    <input type="text" class="form-control" id="username" name="username" value="${response.user.username}" placeholder="${locale.input_username}" readonly required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>

            `

            window.permissions = response.permissions
            let values_select = ""
            for (let permission of window.permissions){
                values_select += `
                    <option value="${permission.id}" ${permission.name == response.user.current_permission ? "selected" : ""}>${permission.name}</option>
                `
            }

            fields += `
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-permission">${locale.permission_level}</label>
                    <select class="form-control custom-select" id="select-permission" onchange="setLegend(this.value)" required>
                        <option value="" selected disabled>Selecione uma opção</option>
                        ${values_select}
                    </select>
                    <label class="text-warning label mt-1" id="legend-permission"></label>
                </div>
            </div>
            `;

            for (let field of response.user.missing_data){

                if(field.name === "zip_code"){

                    fields += `
                    <br>
                    <div class="custom-alert my-2"></div>
                    `;

                }

                fields += `
                    <div class="form-group basic">
                        <div class="input-wrapper">
                            <label class="label" for="${field.name}">${field.label}</label>
                            <input type="${field.type}" class="form-control" autocomplete="n-password" id="${field.name}" name="${field.name}" placeholder="${field.label}">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                        </div>
                    </div>
                `;

            }

            $(`#user-info`).html(`
                <form data-method="PUT" data-action="/users/${id}/" data-ajax="default" data-callback="">
                    ${fields}
                    <div class="form-group basic">
                        <button type="button" class="btn btn-primary btn-block btn-lg" data-bs-dismiss="modal">Deposit</button>
                    </div>
                </form>
            `)

            for (let field of response.user.missing_data){

                let input = `#${field.name}`;

                if(field.name === "zip_code"){
                    $(input).mask("00000-000").on("keyup", function (){
                        get_cep(this.value)
                    })
                }else{
                    $(input).mask(`${field.mask}`).attr('required', (!!field.required))
                }

                $(input).val(field.value).trigger('change')

            }

            $('#select-permission, #name, #email, #username').trigger('change')

            $('.btn-close').on('click', () => {
                $(".modal-title").html('')
                $("#user-info").html('<div id="section-animation" class="w-100 d-flex justify-content-center"></div>')
            })
        }


    }
    $(document).ready(() => {
        let options = {
            valueNames: [ 'permission', 'name', 'str_created' ]
        };
        window.userList = new List('users', options);
    })
</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
