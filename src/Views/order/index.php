<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section my-2">
    <div class="card">
        <div class="card-body" id="order">

            <p class="form-check-label">
                <?php if($order->created_at):?>
                    <?=$functionController->locale('label_created')?>:
                    <?=date('d/m/Y H:i', strtotime($order->created_at))?>
                <?php else: ?>
                    <?=$functionController->locale('label_new_registry')?>
                <?php endif; ?>
            </p>
            <hr>

            <div class="form-check my-2">
                <input type="checkbox" class="form-check-input" id="active" name="active" <?=$order->active ? "checked" : ""?>>
                <label class="form-check-label" for="active"><?=$functionController->locale('input_active')?></label>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-glass-type"><?=$functionController->locale('menu_item_client')?></label>
                    <select class="form-control custom-select" id="select-glass-type" name="glass-type">
                        <option value="" selected disabled><?=$functionController->locale('select_2_placeholder')?></option>
                        <?php foreach ($clients_array as $item):
                            echo "<option value='" . $item->id . "' " . ($item->id == $order->client_id ? "selected" : "") . ">" . $item->name . "</option>";
                        endforeach;?>
                    </select>
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-category"><?=$functionController->locale('menu_item_category')?></label>
                    <select class="form-control custom-select" id="select-category" name="category">
                        <option value="" selected disabled><?=$functionController->locale('select_2_placeholder')?></option>
                        <?php foreach ($categories as $item):
                            echo "<option value='" . $item->id . "' " . ($item->id == $order->category_id ? "selected" : "") . ">" . $item->name . "</option>";
                        endforeach;?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script>

    const actionSheetForm = new bootstrap.Modal('#actionSheetForm')
    function importData(data){
        updateLocalStorage(data)
        actionSheetForm.toggle('hide');
        $('#products > #rows-images').hide();
        prepareOrderForm();
    }

    function showDescriptionProduct(data){
        actionSheetForm.show();
        data.is_order = true;
        actionForm('showDescriptionProduct', data);

    }

    function updateSelect() {
        $('select').select2({
            placeholder: locale.select_2_placeholder
        });
    }

    function prepareOrderForm() {
        // Carregar dados do servidor
        const categories = <?=$functionController->parseObjectToJson($categories)?>;
        const subCategorias = <?=$functionController->parseObjectToJson($subCategorias)?>;
        const thickness = <?=$functionController->parseObjectToJson($thickness)?>;
        const types = <?=$functionController->parseObjectToJson($types)?>;
        const colors = <?=$functionController->parseObjectToJson($colors)?>;
        const clearances = <?=$functionController->parseObjectToJson($clearances)?>;
        const finish = <?=$functionController->parseObjectToJson($finish)?>;

        // Obter elemento do formulário
        let nextForm = $("#nextForm");
        let nextFormBody = "";
        nextForm.html("");

        // Carregar dados do localStorage
        let data = getLocalStorageData();
        console.log(data);

        // Verificar se category_id está presente nos dados
        if (!data.category_id || !subCategorias[data.category_id]) {
            console.error("Category ID is missing or invalid");
            return;
        }

        //SELECT CATEGORIES
        let categories_select_values = "";
        for (let item of categories){
            categories_select_values += `
                <option value="${item.id}" ${data.category_id ? `${data.category_id === item.id ? "selected" : ""}` : ""}>${item.name}</option>
            `;
        }
        categories_select_values = `
        <div class="form-group boxed" id="categories">
            <div class="input-wrapper">
                <label class="label" for="select-category">${locale.menu_item_category}</label>
                <select class="form-control custom-select" id="select-category" name="category">
                    <option value="" selected disabled>${locale.select_2_placeholder}</option>
                    ${categories_select_values}
                </select>
            </div>
        </div>
        `;

        // Gerar opções para o select de subcategorias
        let subcategory_select_values = "";
        for (let item of subCategorias[data.category_id]) {
            subcategory_select_values += `
            <option value="${item.id}" ${data.sub_category_id && data.sub_category_id === item.id ? "selected" : ""}>${item.name}</option>
        `;
        }

        // Construir o HTML do formulário
        nextFormBody += `
           ${categories_select_values}
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-sub-category">${locale.menu_item_sub_category}</label>
                    <select class="form-control custom-select" id="select-sub-category" name="sub-category">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${subcategory_select_values}
                    </select>
                </div>
            </div>
        `;

        // Atualizar o conteúdo do formulário e inicializar o select2
        nextForm.html(`
            ${nextFormBody}
        `);

        $("#select-category").on('change', function (){
            updateLocalStorage(
                {
                    "category_id": this.value,
                    "sub_category_id": false
                }
            )
            prepareOrderForm();
        })

        setTimeout(updateSelect, 0)

    }

    function orderController(data=false){

        const clients = <?=$functionController->parseObjectToJson($clients_array)?>;
        const products = <?=$functionController->parseObjectToJson($products)?>;
        let orderBody = $('#order');

        //SELECT CLIENTS
        let clients_select_values = "";
        for (let item of clients){
            clients_select_values += `
                <option value="${item.id}" ${data.id ? `${data.id === item.id ? "selected" : ""}` : ""}>${item.name}</option>
            `;
        }
        clients_select_values = `
        <div class="form-group boxed">
            <div class="input-wrapper">
                <label class="label" for="select-client">${locale.menu_item_client}</label>
                <select class="form-control custom-select" id="select-client" name="client">
                    <option value="" selected disabled>${locale.select_2_placeholder}</option>
                    ${clients_select_values}
                </select>
            </div>
        </div>
        `;

        //SEARCH PRODUCTS
        let products_select_values = `
            <div id="products">
                <form class="search-form">
                    <div class="form-group searchbox">
                        <input type="text" class="form-control search" placeholder="${locale.menu_item_products} (${products.length})" id="searchInput">
                        <i class="input-icon">
                            <ion-icon name="search-outline" role="img" class="md hydrated" aria-label="search outline"></ion-icon>
                        </i>
                    </div>
                </form>
                <div id="rows-images">
                    <div class="row overflow-scroll overflow-visible d-flex" style="max-height: 45vh">
        `;

        for (let item of products){
            products_select_values += `

                        <div onclick='showDescriptionProduct(${JSON.stringify(item)})' class="col-lg-2 col-md-4 col-6 custom-item searchable">
                            <div class="border border-1 p-1 my-1 rounded-2 search-item">
                                <div class="text-truncate font-weight-bold ">${item.name}</div>
                                <div class="text-truncate">
                                    <span class="">${item.glass_type_name ? item.glass_type_name : ""}</span> -
                                </div>
                                <img src="${item.image ? item.image : "assets/img/sample/photo/1.jpg"}" alt="image" class="imaged img-fluid">
                            </div>
                        </div>

            `;
        }

        products_select_values += `
                </div>
            </div>
        </div>
        `;

        orderBody.html(`

            <form data-method="${data.id ? "PUT" : "POST"}" data-action="/settings/order/${data.id ? data.id : "new"}"
                data-ajax="default" data-callback="">

                <p class="form-check-label">
                    ${data.str_created ? `${locale.label_created} ${data.str_created}` : locale.label_new_registry}
                </p>
                <hr>
                ${clients_select_values}
                ${products_select_values}

                <div id="nextForm"></div>

                ${data.image ? `
                    <div class="card my-2">
                        <div class="card-body" id="card-body-image">
                            <img src="${data.image}" alt="image" class="imaged img-fluid border border-1">
                        </div>
                    </div>
                ` : "" }

                <div id="child-global-custom-alert" class="custom-alert my-2"></div>

                <div class="row mt-2">
                    <div class="col-12">
                        <button type="submit" class="btn btn-lg btn-primary btn-block btn-submit">
                            ${locale.label_btn_save}
                        </button>
                    </div>
                </div>
            </form>

        `);
        updateSelect();

        $('#categories, #products').hide();
        $("#select-client").on('change', function (){
            updateLocalStorage('client_id', this.value);
            $('#categories, #products').show();
            $('#products > #rows-images').hide();
            prepareOrderForm();
        })

        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value;
            searchElements(searchTerm, 'searchable', 'search-item');
        });

        $("#searchInput").on('focus', function() {
            $('#products > #rows-images').show();
        }).on('blur', function() {
            setTimeout(function() {
                if (!$("#searchInput").is(':focus') && !$('#rows-images').is(':hover')) {
                    $('#products > #rows-images').hide();
                }
            }, 100);
        });

        $('#rows-images').on('click', function() {
            $('#products > #rows-images').show();
        });

    }

    $(document).ready(() => {

        orderController();

    })

</script>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
