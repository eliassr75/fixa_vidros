<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section mt-2">
    <ul class="listview image-listview inset list my-2">
        <?php if(count($colors)): foreach ($colors as $color):

            $color->created_text = date('d/m/Y H:i', strtotime($color->created_at));

            ?>
            <li id="li-model">
                <a href="javascript:void(0)" onclick='actionForm("editColor", <?=$functionController->parseObjectToJson($color)?>)'
                   data-bs-toggle="modal" data-bs-target="#actionSheetForm" class="item">
                    <div class="icon-box bg-<?=$color->active ? "primary" : "danger"?>">
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
        <?php endforeach;  else: ?>
            <div class="alert alert-primary" role="alert">
                <?= $functionController->locale('not_found_results') ?>
            </div>
        <?php endif; ?>
    </ul>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
