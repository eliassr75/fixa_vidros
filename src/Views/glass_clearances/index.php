<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section mt-2">
    <ul class="listview image-listview inset list my-2">
        <?php foreach ($clearances as $clearance): ?>
        <li id="li-model">
            <a href="javascript:void(0)" onclick="actionForm('editClearance', <?=$functionController->parseObjectToJson($clearance)?>)"
               data-bs-toggle="modal" data-bs-target="#actionSheetForm" class="item">
                <div class="icon-box bg-primary">
                    <ion-icon name="resize-outline"></ion-icon>
                </div>
                <div class="in">
                    <?=$clearance->name?>
                    <div>
                        <?php if($clearance->width): ?>
                            <span class="badge badge-primary">
                                <ion-icon name="code-outline" class="fs-5 me-1"></ion-icon>
                                <?=$clearance->width?> <?=$clearance->type?>
                            </span>
                        <?php endif; ?>
                        <?php if($clearance->height): ?>
                            <span class="badge badge-primary">
                                <ion-icon name="chevron-expand-outline" class="fs-5 me-1"></ion-icon>
                                <?=$clearance->height?> <?=$clearance->type?>
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
