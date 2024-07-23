<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section mt-2">
    <ul class="listview image-listview inset list my-2">
        <?php if(count($finish)): foreach ($finish as $item):

            $item->created_text = date('d/m/Y H:i', strtotime($item->created_at));

            ?>
            <li id="li-model">
                <a href="javascript:void(0)" onclick='actionForm("editFinish", <?=$functionController->parseObjectToJson($item)?>)'
                   data-bs-toggle="modal" data-bs-target="#actionSheetForm" class="item">
                    <div class="icon-box bg-<?=$item->active ? "primary" : "danger"?>">
                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                    </div>
                    <div class="in">
                        <?=$item->name?>
                    </div>
                </a>
            </li>
        <?php endforeach;  else: ?>
            <div class="alert alert-primary" role="alert">
                <?= $functionController->locale('not_found_results') ?>
            </div>
        <?php endif; ?>
    </ul>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
