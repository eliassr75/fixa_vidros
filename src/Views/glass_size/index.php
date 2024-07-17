<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section mt-2">
    <ul class="listview image-listview inset list my-2">
        <?php foreach ($size as $item): ?>
        <li id="li-model">
            <a href="javascript:void(0)" onclick="actionForm('editSize', <?=$functionController->parseObjectToJson($item)?>)"
               data-bs-toggle="modal" data-bs-target="#actionSheetForm" class="item">
                <div class="icon-box bg-primary">
                    <ion-icon name="help-circle-outline"></ion-icon>
                </div>
                <div class="in">
                    <?=$item->name?> <?=$item->type?>
                    <div>
                        <?php if($item->price): ?>
                            <span class="badge badge-primary">
                                R$ <?=$item->price?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
