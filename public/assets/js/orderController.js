const actionSheetForm = new bootstrap.Modal('#actionSheetForm')
function getNameItem(category_id, sub_category_id){

    let listNamesCategories = {};
    let listNamesSubCategories = {};

    for (let c of categories){
        listNamesCategories[c.id] = c.name
    }
    for (let c of subCategorias[category_id]){
        listNamesSubCategories[c.id] = c.name
    }

    return `${listNamesCategories[category_id]} - ${listNamesSubCategories[sub_category_id]}`

}

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

function addOrderItem(){

    let data = getLocalStorageData()
    let item_model = {
        id: data.id ? data.id : null,
        category_id: data.category_id,
        sub_category_id: data.sub_category_id,
        glass_thickness_id: data.glass_thickness_id,
        glass_color_id: data.glass_color_id,
        glass_finish_id: data.glass_finish_id,
        glass_clearances_id: data.glass_clearances_id,
        product_id: data.product_id ? data.product_id : null,
        quantity: data.quantity,
        width: data.width,
        height: data.height,
        total_price: data.total_price,
        name: getNameItem(data.category_id, data.sub_category_id)
    }

    updateItemsOrderList(item_model);
    orderController();
}

function editItem(id){
    let data = getItemsOrderList();
    if (data.length) {
        for (let itemObj of data) {
            let itemId = Object.keys(itemObj)[0];
            if (parseInt(itemId) === parseInt(id)){
                let item = itemObj[itemId];
                orderController(item);
                updateLocalStorage(item);
                prepareOrderForm();
            }

        }
    }
}

function getOrderItems() {
    let data = getItemsOrderList();
    let itemsTemplate = ``;
    let total_price_order = 0.00

    itemsTemplate += `<ul class="listview image-listview my-2">`;

    if (data.length) {
        for (let itemObj of data) {
            let itemId = Object.keys(itemObj)[0];
            let item = itemObj[itemId];

            itemsTemplate += `
                <li class="px-0 mx-0 w-100" id="li-model-${itemId}">
                    <div class="item m-0 p-0">
                        <a href="javascript:void(0)" onclick='removeItemById(${item.id})' class="icon-box bg-danger">
                            <ion-icon name="trash-outline" role="img" class="md hydrated" aria-label="trash outline"></ion-icon>
                        </a>
                        <a href="javascript:void(0)" onclick='editItem(${item.id})' class="in cursor">
                            <div class="text-truncate" style="max-width: 55vw">${item.quantity}x ${item.name}</div>
                            <span class="text-muted">R$ ${showPrice ? item.total_price.toFixed(2) : "--"}</span>
                        </a>
                    </div>
                </li>
            `;

            total_price_order += item.total_price;
        }
    } else {
        itemsTemplate += `
                <div class="alert alert-primary" role="alert">
                    ${locale.not_found_results}
                </div>
            `;
    }

    itemsTemplate += `
        </ul>
        <br>
        <div class="w-100">
            <div class="row">
                <div class="col-lg-4 col-md-5 col-6 d-flex align-items-center">
                    <p>
                        <b>${locale.label_total_price}: ${showPrice ? total_price_order.toFixed(2) : "--"}</b>
                    </p>
                </div>
                <div class="col-lg-4 col-md-5 col-6">
                    <button type="button" class="btn btn-lg btn-primary btn-block btn-submit" onclick="addOrderItem()">
                        ${locale.label_btn_save}
                    </button>
                </div>
            </div>
        </div>
    `;

    $('#orderItems').html(itemsTemplate).show();
}

function prepareOrderForm() {

    let nextForm = $("#nextForm");
    let nextFormBody = "";
    nextForm.html("");

    let data = getLocalStorageData();

    //SELECT CATEGORIES
    let categories_select_values = "";
    for (let item of categories){
        categories_select_values += `
            <option value="${item.id}" ${data.category_id && parseInt(data.category_id) === item.id ? "selected" : ""}>${item.name}</option>
        `;
    }
    categories_select_values = `
        <div class="form-group boxed" id="categories">
            <div class="input-wrapper">
                <label class="label" for="select-category">${locale.menu_item_category}</label>
                <select class="form-control custom-select" id="select-category" onchange="changeValue(this, 'category_id', 'sub_category_id')" name="category">
                    <option value="" selected disabled>${locale.select_2_placeholder}</option>
                    ${categories_select_values}
                </select>
            </div>
        </div>
    `;

    let subcategory_select_values = "";
    let images = [];
    if(data.category_id) {
        for (let item of subCategorias[data.category_id]) {
            images[item.id] = item.image;
            subcategory_select_values += `
                <option value="${item.id}" ${data.sub_category_id && parseInt(data.sub_category_id) === item.id ? "selected" : ""}>
                    ${item.name} - ${item.glass_type_name}
                </option>
            `;
        }

        nextFormBody += `
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-sub-category">${locale.menu_item_sub_category}</label>
                    <select class="form-control custom-select" id="select-sub-category" onchange="changeValue(this, 'sub_category_id', 'glass_type_id')" name="sub-category">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${subcategory_select_values}
                    </select>
                </div>
            </div>

            <div class="row">
                <div class=" col-lg-3 col-md-5 col-12 border border-1 p-1 my-1 rounded-2" id="image-item">
                </div>
            </div>

        `;
    }

    let thickness_select_values = "";
    let thickness_prices = [];
    if(data.sub_category_id) {
        for (let item of thickness) {
            thickness_prices[item.id] = parseFloat(item.price);
            thickness_select_values += `
                <option value="${item.id}" ${data.glass_thickness_id && parseInt(data.glass_thickness_id) === item.id ? "selected" : ""}>
                    ${item.name} ${item.type} - (R$ ${showPrice ? item.price : "--"})
                </option>
            `;
        }

        nextFormBody += `
            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="quantity">${locale.input_quantity}</label>
                    <input type="tel" class="form-control" id="quantity" onchange="changeValue(this, 'quantity', '--')"
                    name="quantity" placeholder="${locale.input_quantity}" value="${data.quantity ? data.quantity : ""}" required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>
            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="width">${locale.input_width} (cm)</label>
                    <input type="tel" class="form-control" id="width" onchange="changeValue(this, 'width', '--')"
                    name="width" placeholder="${locale.input_width}" value="${data.width ? data.width : ""}" required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>
            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="height">${locale.input_height} (cm)</label>
                    <input type="tel" class="form-control" id="height" onchange="changeValue(this, 'height', '--')"
                    name="height" placeholder="${locale.input_height}" value="${data.height ? data.height : ""}" required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-glass-thickness">${locale.menu_item_glass_thickness}</label>
                    <select class="form-control custom-select" id="select-glass-thickness" onchange="changeValue(this, 'glass_thickness_id', 'glass_color_id')" name="glass-thickness">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${thickness_select_values}
                    </select>
                </div>
            </div>
        `;
    }

    let color_select_values = "";
    let color_percents = [];
    if(data.glass_thickness_id) {

        if (!data.quantity || !data.width || !data.height){
            dialog({
                "status": "warning",
                "message": `
                    ${locale.warning_exists_required_fields}: <br>
                    ${!data.quantity ? `<span class="text-warning">${locale.input_quantity}</span> <br>` : ""}
                    ${!data.width ? `<span class="text-warning">${locale.input_width}</span> <br>` : ""}
                    ${!data.height ? `<span class="text-warning">${locale.input_height}</span> <br>` : ""}
                `
            })
        }else{
            for (let item of colors) {
                color_percents[item.id] = parseFloat(item.percent);
                color_select_values += `
                        <option value="${item.id}" ${data.glass_color_id && parseInt(data.glass_color_id) === item.id ? "selected" : ""}>
                            ${item.name} ${showPrice ? `(R$ + ${item.percent}%)` : ""}
                        </option>
                    `;
            }

            nextFormBody += `
                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label" for="select-glass-color">${locale.menu_item_glass_colors}</label>
                        <select class="form-control custom-select" id="select-glass-color" onchange="changeValue(this, 'glass_color_id', 'glass_finish_id')" name="glass-color">
                            <option value="" selected disabled>${locale.select_2_placeholder}</option>
                            ${color_select_values}
                        </select>
                    </div>
                </div>
            `;
        }


    }

    let finish_select_values = "";
    if(data.glass_color_id) {
        for (let item of finish) {
            finish_select_values += `
                <option value="${item.id}" ${data.glass_finish_id && parseInt(data.glass_finish_id) === item.id ? "selected" : ""}>${item.name}</option>
            `;
        }

        nextFormBody += `
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-glass-finish">${locale.menu_item_glass_finish}</label>
                    <select class="form-control custom-select" id="select-glass-finish" onchange="changeValue(this, 'glass_finish_id', 'glass_clearances_id')" name="glass-finish">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${finish_select_values}
                    </select>
                </div>
            </div>
        `;
    }

    let clearances_select_values = "";
    if(data.glass_finish_id) {
        for (let item of clearances) {
            clearances_select_values += `
                <option value="${item.id}" ${data.glass_clearances_id && parseInt(data.glass_clearances_id) === item.id ? "selected" : ""}>
                    ${item.name}
                    ${typeof (item.width) == 'string' ? `(${locale.input_width}: ${item.width}${item.type})` : ""}
                    ${typeof (item.height) == 'string' ? `(${locale.input_height}: ${item.height}${item.type})` : ""}
                </option>
            `;
        }

        nextFormBody += `
                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label" for="select-glass-clearances">${locale.menu_item_glass_clearances}</label>
                        <select class="form-control custom-select" id="select-glass-clearances" onchange="changeValue(this, 'glass_clearances_id', '--')" name="glass-clearances">
                            <option value="" selected disabled>${locale.select_2_placeholder}</option>
                            ${clearances_select_values}
                        </select>
                    </div>
                </div>
            `;
    }

    let total_price = 0.00
    if(data.quantity && data.glass_thickness_id && thickness_prices.length){
        total_price = thickness_prices[data.glass_thickness_id] * data.quantity;
        if (color_percents.length && data.glass_color_id){
            total_price = total_price + (total_price * color_percents[data.glass_color_id] / 100)
        }

        updateLocalStorage('total_price', total_price);
    }

    nextForm.html(`
            ${categories_select_values}
            ${nextFormBody}
            <hr>
            <p>
                <b>${locale.label_total_price}: ${showPrice ? total_price.toFixed(2) : "--"}</b>
            </p>
        `);

    if(data.sub_category_id){
        $('#image-item').html(`
                <img src="${images.includes(images[data.sub_category_id]) ? images[data.sub_category_id] : "assets/img/sample/photo/1.jpg"}" alt="image" class="imaged img-fluid">
            `)
    }

    scrollToBottom();
    setTimeout(function (){
        updateSelect();
        $(`input[type="tel"]`).mask('000.00', { reverse: true })
    }, 0);

}

function changeValue(el, set_key, unset_key){

    let data = {};
    data[set_key] = el.value;
    data[unset_key] = false;

    updateLocalStorage(data);
    prepareOrderForm();
}

function orderController(data=false){

    let orderBody = $('#order');
    let formBory = ``;

    //SELECT CLIENTS
    let clients_select_values = "";
    let client_id = localStorage.getItem('selected_client_id') ? parseInt(localStorage.getItem('selected_client_id')) : false
    for (let item of clients_array){
        clients_select_values += `
                <option value="${item.id}" ${client_id ? `${client_id === item.id ? "selected" : ""}` : ""}>${item.name} - ${item.document}</option>
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

    if(data){

        if(!client_id){
            dialog({
                "status": "warning",
                "message": `
                       ${locale.warning_exists_required_fields}: <br>
                        <span class="text-warning">${locale.menu_item_client}</span>
                    `
            })
            return;
        }

        if(!data.client_id){
            localStorage.removeItem('jsonStorage');
        }

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

        formBory = `
                <form data-method="${data.id ? "PUT" : "POST"}" data-action="/settings/order/${data.id ? data.id : "new"}"
                data-ajax="default" data-callback="">

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
                        <div class="col-6">
                            <button type="button" class="btn btn-lg btn-cancel btn-outline-secondary btn-block" onclick="orderController()">
                                ${locale.label_btn_cancel}
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-lg btn-primary btn-block btn-submit" onclick="addOrderItem()">
                                ${locale.label_btn_save}
                            </button>
                        </div>
                    </div>
                </form>

            `;
    }

    orderBody.html(`

        <p class="w-100 p-0 m-0 form-check-label d-inline-flex justify-content-between">
        ${locale.form_order_item}
            <a class="headerButton" id="headerButton" href="javascript:void(0)" >
                <ion-icon role="img" class="md hydrated fs-4 me-1" name="reload-outline" onclick="clearOrders();"></ion-icon>
                <ion-icon role="img" class="md hydrated fs-4 ms-1" name="add-outline" onclick="orderController(true);"></ion-icon>
            </a>
        </p>
    
        ${clients_select_values}
        ${formBory}
    
        <br>
        <div id="orderItems"></div>
    `);

    updateSelect();

    $("#select-client").on('change', function () {
        updateLocalStorage('client_id', this.value);
        localStorage.setItem('selected_client_id', this.value);
    })

    if(data) {

        $('#orderItems, #products > #rows-images').hide();

        if (data.category_id){
            $('#products').show();
            $('#products > #rows-images').hide();
        }

        document.getElementById('searchInput').addEventListener('input', function () {
            const searchTerm = this.value;
            searchElements(searchTerm, 'searchable', 'search-item');
        });

        $("#searchInput").on('focus', function () {
            $('#products > #rows-images').show();
        }).on('blur', function () {
            setTimeout(function () {
                if (!$("#searchInput").is(':focus') && !$('#rows-images').is(':hover')) {
                    $('#products > #rows-images').hide();
                }
            }, 100);
        });

        $('#rows-images').on('click', function () {
            $('#products > #rows-images').show();
        });

        prepareOrderForm();

    }else{
        getOrderItems();
    }

}

$(document).ready(() => {
    orderController(false);
})