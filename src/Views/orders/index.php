<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section mt-2">

    <div class="card">

        <div id="orders">

            <!-- class="search" automagically makes an input a search field. -->
            <div class="px-3 pt-3">
                <form class="search-form">
                    <div class="form-group searchbox">
                        <input type="text" class="form-control search">
                        <i class="input-icon">
                            <ion-icon name="search-outline" role="img" class="md hydrated" aria-label="search outline"></ion-icon>
                        </i>
                    </div>
                </form>
            </div>
            <!-- class="sort" automagically makes an element a sort buttons. The date-sort value decides what to sort by. -->
            <!--
            <button class="sort btn btn-primary" data-sort="name">
                Sort
            </button>
            -->

            <ul class="listview image-listview inset list my-2">
                <?php if (count($orders) > 0): foreach ($orders as $order): ?>
                <li id="li-model">
                    <a href="/order/<?=$order->id?>/" class="item">
                        <div class="in">
                            <div class="w-100 text-truncate">
                                <span class="name">
                                    <?=$order->id?> - <?=$order->client_name?>
                                    (<?=$order->str_created?>)
                                </span>
                                <footer class="str_created">
                                    <span class="badge badge-primary">
                                        <?=$order->type_status_name?>
                                    </span>
                                    <span class="badge badge-primary">
                                        <?=$order->type_status_finance_name?>
                                    </span>
                                </footer>
                            </div>
                        </div>
                    </a>
                    <div class="form-check form-switch me-2">

                    </div>
                </li>
                <?php endforeach; else: ?>

                    <div class="alert alert-primary mb-2" role="alert">
                        <?=$functionController->locale('not_found_results')?>
                    </div>

                <?php endif; ?>
            </ul>
        </div>


    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script>
    <?php if (count($orders) > 0): ?>
        $(document).ready(() => {
            let options = {
                valueNames: [ 'document', 'name', 'str_created' ]
            };
            window.clientList = new List('orders', options);
        })
    <?php endif;?>
</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
