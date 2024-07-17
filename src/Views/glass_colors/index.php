<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section mt-2">
    <ul class="listview image-listview inset list my-2">
        <?php foreach ($colors as $color): ?>
        <li id="li-model">
            <a href="javascript:void(0)" onclick="actionForm('editColor', <?=$functionController->parseObjectToJson($color)?>)"
               data-bs-toggle="modal" data-bs-target="#actionSheetForm" class="item">
                <div class="icon-box bg-primary">
                    <ion-icon name="color-palette-outline"></ion-icon>
                </div>
                <div class="in">
                    <?=$color->name?>
                    <div>
                        <?php if($color->percent): ?>
                            <span class="badge badge-primary">
                                R$ + <?=$color->percent?>%
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
